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
        Schema::create('project_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                ->constrained('projects', 'project_id')
                ->cascadeOnDelete();

            $table->foreignId('actor_id')
                ->constrained('users', 'user_id')
                ->cascadeOnDelete();

            $table->string('action');
            $table->text('comment')->nullable();
            $table->integer('revision_number')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
