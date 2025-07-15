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
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('breed_id')->constrained('breeds');
            $table->enum('status', ['accepted', 'rejected', 'pending'])->default('pending');
            $table->string('primary_color')->nullable();
            $table->string('secondary_color')->nullable();
            $table->float('weight')->nullable();
            $table->string('address')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('cover_image')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->enum('size', ['small', 'medium', 'large', 'extra_large']);
            $table->text('desc')->nullable();
            $table->enum('health_status', ['healthy', 'injured', 'sick', 'unknown'])->default('unknown');
            $table->date('birth_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('animals');
    }
};
