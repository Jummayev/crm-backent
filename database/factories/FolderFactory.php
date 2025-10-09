<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\FileManager\Models\Folder;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\FileManager\Models\Folder>
 */
class FolderFactory extends Factory
{
    protected $model = Folder::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'path' => fake()->unique()->slug(),
            'parent_id' => null,
            'user_id' => User::factory(),
            'sort' => 0,
        ];
    }
}
