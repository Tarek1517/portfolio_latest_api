<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique()->index();
            $table->string('image')->nullable();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('live_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('duration')->nullable();
            $table->boolean('highlight')->default(false);
            $table->boolean('status')->default(true);
            // JSON columns for structured repeater fields
            $table->json('key_features')->nullable();  // [{ icon, title, description }]
            $table->json('challenges')->nullable();    // [{ title, solution }]
            $table->json('statistics')->nullable();    // [{ title, desc }]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
