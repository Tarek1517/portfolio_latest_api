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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
            $table->string('package_name', 191)->nullable();
            $table->integer('package_duration')->nullable()->comment('Duration in days');
            $table->string('cus_name', 191)->nullable();
            $table->string('cus_email', 191)->nullable();
            $table->string('cus_phone', 30)->nullable();
            $table->decimal('investment_amount', 15, 2)->nullable();
            $table->string('payment_method', 191)->default('sslcommerz');
            $table->string('transaction_id', 191)->nullable();
            $table->date('invest_start')->nullable();
            $table->date('invest_expire')->nullable();
            $table->tinyInteger('auto_renuw')->default(1);
            $table->enum('status', ['pending', 'paid', 'failed', 'cancelled', 'refunded'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid','cancelled'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
