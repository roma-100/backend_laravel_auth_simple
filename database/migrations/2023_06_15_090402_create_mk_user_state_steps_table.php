<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMkUserStateStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mk_user_state_steps', function (Blueprint $table) {
            $table->id();
            $table->integer('mk_list_id');
            $table->integer('user_id');
            $table->integer('step_num');
            $table->integer('items')->default(0);
            $table->integer('handle')->default(0);
            $table->integer('passed')->default(0);
            $table->integer('failed')->default(0);
            $table->boolean('done')->default(false); //
            $table->boolean('numbhidden')->default(true); //
            $table->boolean('radiohidden')->default(false); //
            $table->string('bgcolor')->default(''); //
            $table->string('color')->default(''); //
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mk_user_state_steps');
    }
}
