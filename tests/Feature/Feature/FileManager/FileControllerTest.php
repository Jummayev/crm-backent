<?php

namespace Tests\Feature\Feature\FileManager;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;
use Modules\FileManager\Models\File;
use Tests\TestCase;

class FileControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create static directory if not exists
        if (! is_dir(base_path('static'))) {
            mkdir(base_path('static'), 0777, true);
        }
    }

    protected function tearDown(): void
    {
        // Clean up test files
        $staticPath = base_path('static');
        if (is_dir($staticPath)) {
        }

        parent::tearDown();
    }

    private function deleteDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = "$dir/$file";
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    public function test_unauthenticated_user_cannot_upload_files(): void
    {
        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->postJson('/api/v1/files', [
            'files' => [$file],
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_upload_single_file(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $file = UploadedFile::fake()->image('test.jpg', 100, 100);

        $response = $this->postJson('/api/v1/files', [
            'files' => [$file],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'ext',
                        'file',
                        'size',
                        'src',
                        'thumbnails',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('files', [
            'name' => 'test.jpg',
            'ext' => 'jpg',
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_user_can_upload_multiple_files(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $files = [
            UploadedFile::fake()->image('test1.jpg'),
            UploadedFile::fake()->image('test2.png'),
            UploadedFile::fake()->create('document.pdf', 100),
        ];

        $response = $this->postJson('/api/v1/files', [
            'files' => $files,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('files', ['name' => 'test1.jpg', 'ext' => 'jpg']);
        $this->assertDatabaseHas('files', ['name' => 'test2.png', 'ext' => 'png']);
        $this->assertDatabaseHas('files', ['name' => 'document.pdf', 'ext' => 'pdf']);
    }

    public function test_upload_validates_required_files(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->postJson('/api/v1/files', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['files']);
    }

    public function test_upload_validates_max_files_limit(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        config(['filemanager.max_files_per_upload' => 3]);

        $files = [
            UploadedFile::fake()->image('test1.jpg'),
            UploadedFile::fake()->image('test2.jpg'),
            UploadedFile::fake()->image('test3.jpg'),
            UploadedFile::fake()->image('test4.jpg'),
        ];

        $response = $this->postJson('/api/v1/files', [
            'files' => $files,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['files']);
    }

    public function test_upload_blocks_dangerous_extensions(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $file = UploadedFile::fake()->create('malicious.php', 100);

        $response = $this->postJson('/api/v1/files', [
            'files' => [$file],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['files.0']);
    }

    public function test_upload_blocks_double_extensions(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $file = UploadedFile::fake()->create('image.php.jpg', 100);

        $response = $this->postJson('/api/v1/files', [
            'files' => [$file],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['files.0']);
    }

    public function test_upload_validates_file_size(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        config(['filemanager.max_file_size' => 10]); // 10KB

        $file = UploadedFile::fake()->create('large.jpg', 100); // 100KB

        $response = $this->postJson('/api/v1/files', [
            'files' => [$file],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['files.0']);
    }

    public function test_upload_validates_allowed_extensions(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $file = UploadedFile::fake()->create('test.xyz', 100);

        $response = $this->postJson('/api/v1/files', [
            'files' => [$file],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['files.0']);
    }

    public function test_upload_rollback_on_error(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        // First file is valid, second has blocked extension
        $files = [
            UploadedFile::fake()->image('valid.jpg'),
            UploadedFile::fake()->create('malicious.php', 100),
        ];

        $response = $this->postJson('/api/v1/files', [
            'files' => $files,
        ]);

        $response->assertStatus(422);

        // Ensure no files were saved in DB
        $this->assertDatabaseCount('files', 0);
    }

    public function test_authenticated_user_can_view_file(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $file = File::factory()->create([
            'slug' => 'test123',
            'user_id' => $user->id,
        ]);

        $response = $this->getJson("/api/v1/files/{$file->slug}/view");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'ext',
                    'src',
                ],
            ]);
    }

    public function test_authenticated_user_can_delete_file(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $file = File::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson("/api/v1/files/{$file->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('files', [
            'id' => $file->id,
        ]);
    }

    public function test_file_is_stored_on_disk(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->postJson('/api/v1/files', [
            'files' => [$file],
        ]);

        $response->assertStatus(200);

        $uploadedFile = File::query()->first();
        $filePath = $uploadedFile->getDist();

        $this->assertFileExists($filePath);
    }

    public function test_upload_with_folder_id(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        // Create a folder first
        $folder = \Modules\FileManager\Models\Folder::factory()->create([
            'user_id' => $user->id,
        ]);

        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->postJson('/api/v1/files', [
            'files' => [$file],
            'folder_id' => $folder->id,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('files', [
            'name' => 'test.jpg',
            'folder_id' => $folder->id,
        ]);
    }

    public function test_upload_validates_folder_exists(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->postJson('/api/v1/files', [
            'files' => [$file],
            'folder_id' => 999999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['folder_id']);
    }

    public function test_can_upload_large_file_over_2mb(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        // Create 5MB file
        $file = UploadedFile::fake()->create('large_file.pdf', 5120); // 5MB

        $response = $this->postJson('/api/v1/files', [
            'files' => [$file],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'ext',
                        'size',
                    ],
                ],
            ]);

        $this->assertDatabaseHas('files', [
            'name' => 'large_file.pdf',
            'ext' => 'pdf',
        ]);
    }

    public function test_can_upload_file_close_to_100mb_limit(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        // Create 50MB file (half of limit)
        $file = UploadedFile::fake()->create('huge_file.pdf', 51200); // 50MB

        $response = $this->postJson('/api/v1/files', [
            'files' => [$file],
        ]);

        $response->assertStatus(200);

        $uploadedFile = \Modules\FileManager\Models\File::query()->latest()->first();
        $this->assertNotNull($uploadedFile);
        $this->assertEquals('huge_file.pdf', $uploadedFile->name);
        $this->assertFileExists($uploadedFile->getDist());
    }

    public function test_cannot_upload_file_over_100mb_limit(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        // Create 150MB file (over limit)
        $file = UploadedFile::fake()->create('too_large.pdf', 153600); // 150MB

        $response = $this->postJson('/api/v1/files', [
            'files' => [$file],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['files.0']);
    }

    public function test_sanitize_filename_preserves_unicode_characters(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        // Test with Cyrillic characters
        $file = UploadedFile::fake()->image('Отчет 2024.jpg');

        $response = $this->postJson('/api/v1/files', [
            'files' => [$file],
        ]);

        $response->assertStatus(200);

        $uploadedFile = \Modules\FileManager\Models\File::query()->latest()->first();
        $this->assertEquals('Отчет 2024.jpg', $uploadedFile->name);
    }

    public function test_sanitize_filename_removes_dangerous_characters(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        // Test with dangerous characters: < > : " | ? are replaced, | is 1 char
        $file = UploadedFile::fake()->image('file<>:"|?.jpg');

        $response = $this->postJson('/api/v1/files', [
            'files' => [$file],
        ]);

        $response->assertStatus(200);

        $uploadedFile = \Modules\FileManager\Models\File::query()->latest()->first();
        // Dangerous characters should be replaced with underscore: <>"? = 6 underscores
        $this->assertEquals('file______.jpg', $uploadedFile->name);
    }
}
