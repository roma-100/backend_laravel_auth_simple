<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMkStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mk_steps', function (Blueprint $table) {
            $table->id();
            $table->integer('mk_list_id');
            $table->integer('step_num'); //
            $table->text('action'); // what to do
            $table->text('description');
            $table->integer('duration');
            /* name,  */
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
        Schema::dropIfExists('mk_steps');
    }
}
