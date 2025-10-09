<?php

namespace Modules\FileManager\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\FileManager\Repository\FileRepository;
use Modules\FileManager\Repository\Interfaces\FileInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(FileInterface::class, FileRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
