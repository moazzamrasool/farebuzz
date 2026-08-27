<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\ActivityCategory;
use App\Traits\GeneratesUniqueSlug;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillActivityCategories extends Command
{
    use GeneratesUniqueSlug;

    protected $signature = 'activities:backfill-categories {--apply : Actually write changes. Without this flag, only a preview is printed.}';

    protected $description = 'Create ActivityCategory records from activities.category free-text values and link activities.activity_category_id to them';

    // Known icon for the categories seen during planning; anything else backfills with no icon.
    private const ICONS = [
        'water sports' => 'bi-water',
        'sightseeing'  => 'bi-binoculars-fill',
        'adventure'    => 'bi-lightning-charge-fill',
        'wildlife'     => 'bi-tree-fill',
        'beaches'      => 'bi-umbrella-fill',
    ];

    public function handle(): int
    {
        $apply = $this->option('apply');

        $activities = Activity::withoutGlobalScopes()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->get(['id', 'unique_id', 'category', 'activity_category_id']);

        if ($activities->isEmpty()) {
            $this->info('No activities with a free-text category found. Nothing to do.');

            return self::SUCCESS;
        }

        $groups = $activities->groupBy(fn (Activity $a) => ($a->unique_id ?? '—').'|'.$a->category);

        $this->line($apply ? 'Applying backfill…' : 'DRY RUN — nothing will be written. Re-run with --apply to commit.');
        $this->newLine();

        $plan = [];
        foreach ($groups as $key => $rows)
            /** @var \Illuminate\Support\Collection<int, Activity> $rows */
        {
            [$uniqueId, $categoryName] = explode('|', $key, 2);
            $uniqueId = $uniqueId === '—' ? null : $uniqueId;

            $existing = ActivityCategory::withoutGlobalScopes()
                ->where('unique_id', $uniqueId)
                ->where('name', $categoryName)
                ->first();

            $plan[] = [
                'unique_id' => $uniqueId ?? '(none)',
                'category'  => $categoryName,
                'activities' => $rows->count(),
                'action'    => $existing ? "reuse #{$existing->id}" : 'create new',
            ];
        }

        $this->table(['Tenant (unique_id)', 'Category', '# Activities', 'Action'], $plan);

        if (!$apply) {
            return self::SUCCESS;
        }

        DB::transaction(function () use ($groups) {
            foreach ($groups as $key => $rows) {
                [$uniqueId, $categoryName] = explode('|', $key, 2);
                $uniqueId = $uniqueId === '—' ? null : $uniqueId;

                $category = ActivityCategory::withoutGlobalScopes()
                    ->where('unique_id', $uniqueId)
                    ->where('name', $categoryName)
                    ->first();

                if (!$category) {
                    // forceCreate: 'unique_id' isn't mass-assignable (it's normally set by the
                    // BelongsToTenant boot hook off the authenticated admin, which doesn't exist
                    // in a console context), so it must be force-filled here explicitly.
                    $category = ActivityCategory::withoutGlobalScopes()->forceCreate([
                        'unique_id'  => $uniqueId,
                        'name'       => $categoryName,
                        'slug'       => $this->generateUniqueSlug(ActivityCategory::class, $categoryName),
                        'icon'       => self::ICONS[strtolower($categoryName)] ?? null,
                        'status'     => 'active',
                        'sort_order' => 0,
                    ]);
                }

                Activity::withoutGlobalScopes()
                    ->whereIn('id', $rows->pluck('id'))
                    ->update(['activity_category_id' => $category->id]);
            }
        });

        $this->newLine();
        $this->info('Backfill complete. The old "category" column was left untouched.');

        return self::SUCCESS;
    }
}
