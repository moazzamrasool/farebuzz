<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    // Shared, tenant-agnostic permission catalog every company's roles draw from.
    // Permissions are never team-scoped in Spatie — only roles are.
    protected array $modules = [
        'enquiries', 'travel-categories', 'destinations',
        'holiday-packages', 'activities', 'users', 'roles', 'settings',
        'amenities', 'hotels', 'hotel-reviews', 'inclusions', 'exclusions',
        'homepage-sections', 'cms-pages', 'bookings', 'navbar-menu', 'blog', 'about-page', 'coupons',
        // Leads/CRM (package enquiries follow-up + WhatsApp/email + message templates)
        'leads',
        // Customers — public-facing signups, filtered to this company's bookings/enquiries
        'customers',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->modules as $module) {
            foreach (['view', 'create', 'edit', 'delete'] as $action) {
                Permission::findOrCreate("{$module}.{$action}", 'admin');
            }
        }
    }
}
