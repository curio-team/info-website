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
        Schema::create('sites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('introduction_text_nl')->nullable();
            $table->string('introduction_text_en')->nullable();
            $table->string('name');
            $table->tinyInteger('year');
            $table->text('path_nl');
            $table->text('path_en')->nullable();
            $table->boolean('allow_unsafe')->default(false);
            $table->string('publisher_id');

            $table->foreign('publisher_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};
