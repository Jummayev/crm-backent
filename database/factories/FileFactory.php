<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\FileManager\Models\File;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\FileManager\Models\File>
 */
class FileFactory extends Factory
{
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ext = fake()->randomElement(['jpg', 'png', 'pdf', 'docx']);
        $slug = fake()->unique()->lexify('??????????');

        return [
            'name' => fake()->word().'.'.$ext,
            'path' => '2025/01/01/12/00',
            'slug' => $slug,
            'ext' => $ext,
            'file' => $slug.'.'.$ext,
            'domain' => config('filemanager.cdn_domain', 'http://localhost'),
            'size' => fake()->numberBetween(1024, 1048576),
            'folder_id' => null,
            'user_id' => User::factory(),
            'description' => fake()->sentence(),
            'sort' => 0,
        ];
    }
}
