<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('waste_files', function (Blueprint $table) {

            $table->id();

            $table->foreignId('waste_record_id')
                  ->constrained('waste_records')
                  ->cascadeOnDelete();

            $table->string('file_name');

            $table->string('file_type', 100);

            $table->decimal('file_size', 10, 2);

            $table->text('file_path');

            $table->timestamp('uploaded_at')->useCurrent();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waste_files');
    }
};