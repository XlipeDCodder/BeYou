<?php

use Beyou\Catalog\Domain\Model\Channel as DomainChannel; 
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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignIdFor(DomainChannel::class)->constrained()->onDelete('cascade');

            $table->string('title');
            $table->text('description')->nullable();

            $table->string('original_file_path');
            $table->string('stream_file_path')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->unsignedInteger('duration_in_seconds')->nullable();

            $table->string('status')->index();
            $table->string('visibility')->default('private')->index();

            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
