<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained('workshops')->onDelete('cascade');
            $table->foreignId('ticket_type_id')->constrained('ticket_types')->onDelete('cascade');
            $table->string('name');
            $table->string('email');
            $table->string('phone', 20)->nullable();
            $table->string('occupation')->nullable();
            $table->text('address')->nullable();
            $table->string('company')->nullable();
            $table->string('position')->nullable();
            $table->string('ticket_code', 50)->unique();
            $table->boolean('is_paid')->default(false);
            $table->boolean('is_checked_in')->default(false);
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();

            // Unique constraint for email within workshop
            $table->unique(['workshop_id', 'email'], 'unique_workshop_email');
            
            // Indexes for performance
            $table->index('ticket_code');
            $table->index('workshop_id');
            $table->index('is_paid');
            $table->index('is_checked_in');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
