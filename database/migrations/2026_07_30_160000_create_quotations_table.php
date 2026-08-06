<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            // Tenant key — see App\Models\Concerns\BelongsToTenant, same convention as
            // bookings/package_enquiries/holiday_packages.
            $table->string('unique_id', 36)->nullable()->index();

            $table->string('quotation_number', 20)->unique();
            $table->foreignId('package_enquiry_id')->constrained('package_enquiries')->cascadeOnDelete();
            $table->foreignId('holiday_package_id')->nullable()->constrained('holiday_packages')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('admins')->nullOnDelete();

            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();

            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->timestamp('sent_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
