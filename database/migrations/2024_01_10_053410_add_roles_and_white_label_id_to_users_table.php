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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['staff', 'admin', 'super_admin'])->default('admin');
            $table->unsignedBigInteger('white_label_id')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();

            $table->unique(['email', 'white_label_id']);

            $table->foreign('white_label_id')->references('id')->on('white_labels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'white_label_id', 'phone', 'address']);
        });
    }
};
