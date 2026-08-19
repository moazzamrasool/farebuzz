<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use App\Models\Concerns\ForSiteTenant;
use Illuminate\Database\Eloquent\Model;

class ContactPage extends Model
{
    use BelongsToTenant, ForSiteTenant;

    protected $table = 'contact_page';

    protected $fillable = [
        'address',
        'phone',
        'email',
        'support_hours',
        'map_embed_url',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'focus_keyword',
        'tags',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'robots_index',
        'robots_follow',
    ];

    protected function casts(): array
    {
        return [
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
        ];
    }

    // Explicit embed URL wins (pasted from Maps' "Share > Embed a map"); otherwise
    // auto-generate a query-based embed from the address — no API key needed for
    // either. Null when there's nothing to point the map at.
    public function resolvedMapEmbedUrl(): ?string
    {
        if ($this->map_embed_url) {
            return $this->map_embed_url;
        }

        if ($this->address) {
            return 'https://maps.google.com/maps?q='.urlencode($this->address).'&output=embed';
        }

        return null;
    }
}
