<?php

namespace Database\Factories;

use App\Enums\ProjectEnums\ProjectStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'project_name'        => $this->faker->sentence(3),
            'project_description' => $this->faker->paragraph(),
            'project_status'      => ProjectStatus::STATUS_OPEN,
            'project_deadline'    => now()->addDays(7),
            'project_price'       => 1000,
            'user_id'            => null,
            'client_id'          => null,
        ];
    }
}
