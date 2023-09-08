<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
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

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sites');
    }
};
