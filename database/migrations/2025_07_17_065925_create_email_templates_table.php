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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id')->constrained('workshops')->onDelete('cascade');
            $table->enum('type', ['invite', 'confirm', 'ticket', 'reminder', 'thank_you']);
            $table->string('subject');
            $table->text('content');
            $table->timestamps();

            // Unique constraint to ensure one template per type per workshop
            $table->unique(['workshop_id', 'type'], 'unique_workshop_template_type');
            
            // Indexes for performance
            $table->index('workshop_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
