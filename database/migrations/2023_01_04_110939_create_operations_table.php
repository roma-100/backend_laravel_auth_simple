<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->id();
            $table->date('opdate')->nullable();
            $table->string('type');
            $table->string('slug');
            $table->string('name')->nullable;
            $table->string('description')->nullable;
            $table->decimal('usd',12,2)->default(0);
            $table->decimal('rur',12,2)->default(0);
            $table->decimal('tg',12,2)->default(0);
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
        Schema::dropIfExists('operations');
    }
}
