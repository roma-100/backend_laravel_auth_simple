<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMkHeaderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mk_list', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('КГ');; //КГ или КРезотатор
            $table->string('name'); //23-153
            $table->text('decsription'); //23-153
            $table->integer('quantity'); //
            $table->date('date_start')->nullable();
            $table->date('date_finish')->nullable();
            $table->boolean('active')->default(false); //
            $table->timestamps()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mk_header');
    }
}
