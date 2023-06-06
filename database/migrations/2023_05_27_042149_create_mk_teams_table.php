<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMkTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mk_teams', function (Blueprint $table) {
            $table->id();
            $table->integer('mk_list_id'); //
            $table->integer('user_id'); //
            $table->integer('items')->default(0);
            $table->string('role')->default('N/A'); //member | leader
            /* name,  */
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
        Schema::dropIfExists('mk_teams');
    }
}
