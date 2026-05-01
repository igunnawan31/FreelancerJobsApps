<?php

use App\Enums\ProjectEnums\ProjectStatus;
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
        Schema::create('projects', function (Blueprint $table) {
            $table->id('project_id');
            $table->string('project_name');
            $table->text('project_description');

            $table->string('project_status')
                ->default(ProjectStatus::STATUS_OPEN->value)
                ->index();
            
            $table->dateTime('project_deadline');
            $table->unsignedBigInteger('project_price');
            
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users', 'user_id')
                ->nullOnDelete();

            $table->foreignId('client_id')
                ->nullable()
                ->constrained('users', 'user_id')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
