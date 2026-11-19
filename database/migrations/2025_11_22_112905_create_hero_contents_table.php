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
        Schema::create('hero_contents', function (Blueprint $table) {
            $table->id();
            $table->string('main_title');
            $table->text('main_description');
            $table->string('main_title_bn');
            $table->text('main_description_bn');
            $table->string('students_count');
            $table->string('courses_count');
            $table->string('success_rate');
            $table->string('tutors_count');
            $table->string('students_count_bn');
            $table->string('courses_count_bn');
            $table->string('success_rate_bn');
            $table->string('tutors_count_bn');
            $table->string('badge_guarantee');
            $table->string('badge_certificate');
            $table->string('badge_self_paced');
            $table->string('badge_guarantee_bn');
            $table->string('badge_certificate_bn');
            $table->string('badge_self_paced_bn');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_contents');
    }
};
