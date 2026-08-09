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
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('slug')->unique();
            $table->string('specialty')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('avatar_url')->nullable();
            $table->text('bio')->nullable();
            $table->smallInteger('experience_years')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('salon_service_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salon_service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['salon_service_id', 'staff_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salon_service_staff');
        Schema::dropIfExists('staff');
    }
};
