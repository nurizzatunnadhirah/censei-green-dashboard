<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('greenmetric_guidelines', function (Blueprint $table) {

            $table->id();

            $table->string('indicator_name', 100);

            $table->text('description')->nullable();

            $table->decimal('target_percentage', 5, 2);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('greenmetric_guidelines');
    }
};