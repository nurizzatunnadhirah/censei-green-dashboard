<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_records', function (Blueprint $table) {

            $table->id();

           $table->foreignId('location_id')
                 ->constrained('locations')
                 ->cascadeOnDelete();

            $table->foreignId('waste_type_id')
                  ->constrained('waste_types')
                  ->cascadeOnDelete();

            $table->foreignId('recorded_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->decimal('actual_weight', 10, 2);

            $table->string('collection_method', 100)->nullable();

            $table->date('collection_date');

            $table->boolean('recycled')->default(true);

            $table->text('remarks')->nullable();

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_records');
    }
};