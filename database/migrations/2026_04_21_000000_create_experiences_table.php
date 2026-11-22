<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('position');
            $table->string('type');
            $table->string('company_name');
            $table->string('company_logo');
            $table->string('address');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->json('languages')->nullable(); // [{ title, }]
            $table->json('heighlights')->nullable(); // [{ icon, desc }]
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};
