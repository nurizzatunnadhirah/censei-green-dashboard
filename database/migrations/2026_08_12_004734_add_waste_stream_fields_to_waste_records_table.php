<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('waste_records', function (Blueprint $table) {

            /*
             * Identifies whether the record belongs to
             * recycling data or waste-to-landfill data.
             */
            $table->string('waste_stream', 50)
                ->nullable();

            /*
             * Landfill collection information.
             * Recycling records can leave these fields NULL.
             */
            $table->string('vehicle_no', 50)
                ->nullable();

            $table->time('time_in')
                ->nullable();

            $table->time('time_out')
                ->nullable();

        });

        /*
         * Landfill PDF records do not contain a campus
         * location/program, so location_id must allow NULL.
         */
        Schema::table('waste_records', function (Blueprint $table) {

            $table->unsignedBigInteger('location_id')
                ->nullable()
                ->change();

        });
    }

    public function down(): void
    {
        Schema::table('waste_records', function (Blueprint $table) {

            $table->dropColumn([
                'waste_stream',
                'vehicle_no',
                'time_in',
                'time_out',
            ]);

        });

        Schema::table('waste_records', function (Blueprint $table) {

            $table->unsignedBigInteger('location_id')
                ->nullable(false)
                ->change();

        });
    }
};