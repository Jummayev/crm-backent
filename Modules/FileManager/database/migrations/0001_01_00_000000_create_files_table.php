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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('name', 2000);
            $table->string('path');
            $table->string('slug')->unique();
            $table->string('ext', 20);
            $table->string('file', 2000);
            $table->string('domain', 1000)->nullable();
            $table->unsignedBigInteger('size');
            $table->foreignId('folder_id')->nullable()->constrained('folders');
            $table->text('description')->nullable();
            $table->integer('sort')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
