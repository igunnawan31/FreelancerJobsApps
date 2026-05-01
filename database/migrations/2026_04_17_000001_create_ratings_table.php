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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id('rating_id');
            $table->string('rating_type')
                ->index();

            $table->unsignedTinyInteger('rating_value');
            $table->foreignId('project_id')
                ->constrained('projects', 'project_id')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users', 'user_id')
                ->cascadeOnDelete();

            $table->foreignId('penilai_id')
                ->constrained('users', 'user_id')
                ->cascadeOnDelete();

            $table->timestamps();
            $table->unique(['project_id', 'user_id', 'rating_type', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
