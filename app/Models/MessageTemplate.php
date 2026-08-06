<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'name',
        'channel',
        'subject',
        'body',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function scopeChannel(Builder $query, string $channel): Builder
    {
        return $query->where('channel', $channel);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Substitute {merge_field} placeholders in the template body with real values.
     *
     * @param  array<string, string>  $mergeFields
     */
    public function render(array $mergeFields): string
    {
        $replacements = collect($mergeFields)->mapWithKeys(fn ($value, $key) => ["{{$key}}" => $value])->all();

        return strtr($this->body, $replacements);
    }
}
