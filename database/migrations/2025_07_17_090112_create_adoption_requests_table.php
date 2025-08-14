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
        Schema::create('adoption_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('animal_id')->constrained('animals');
            $table->enum('status', ['accepted', 'rejected', 'pending'])->default('pending');

            $table->integer('family_members_count')->nullable();
            $table->string('address')->nullable();
            $table->boolean('has_children')->nullable();
            $table->json('children_ages')->nullable(); 

            $table->string('job_title')->nullable();
            $table->string('company_name')->nullable();
            $table->integer('work_hours_per_day')->nullable();
            $table->enum('work_type', ['remote', 'on_site', 'hybrid'])->nullable();

            $table->enum('housing_type', ['apartment', 'house', 'villa'])->nullable();
            $table->boolean('is_rented')->nullable();
            $table->string('landlord_name')->nullable();
            $table->string('landlord_phone')->nullable();
            $table->boolean('landlord_allows_pets')->nullable();
            $table->boolean('has_garden')->nullable();

            $table->boolean('has_patience')->nullable();
            $table->boolean('can_handle_issues')->nullable(); // صحية وسلوكية
            $table->integer('hours_with_pet_daily')->nullable();
            $table->boolean('someone_home_24_7')->nullable();
            $table->boolean('can_be_with_pet_when_sick')->nullable();

            $table->boolean('agreed_to_terms')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adoption_requests');
    }
};
