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

        $table->string('phone_number', 20)->nullable();

        $table->string('role', 20)->default('Staff');

        $table->string('position', 100)->nullable();

        $table->foreignId('faculty_id')
              ->nullable()
              ->constrained('faculties')
              ->nullOnDelete();

        $table->string('status', 20)
              ->default('Active');

    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('users', function (Blueprint $table) {

        $table->dropForeign(['faculty_id']);

        $table->dropColumn([
            'phone_number',
            'role',
            'position',
            'faculty_id',
            'status'
        ]);

    });
}
};