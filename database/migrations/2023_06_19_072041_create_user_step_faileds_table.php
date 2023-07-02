<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserStepFailedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_step_faileds', function (Blueprint $table) {
            $table->id();
            $table->integer('mk_list_id'); //
            $table->integer('user_id'); //
            $table->integer('items')->default(0); //
            $table->integer('step_tx')->default(0); //
            $table->string('description')->default(0); //
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_step_faileds');
    }
}
