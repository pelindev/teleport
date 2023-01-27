<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->index('user_id', 'payment_user_idx');
            $table->foreign('user_id', 'payment_user_fk')
                ->on('users')
                ->references('id');
            $table->integer('amount');
            $table->enum('action', ['replenishment', 'write-off']);
            $table->enum('status', ['successed', 'failed', 'canceled'])
                ->default('successed');
            $table->integer('total');
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
        Schema::dropIfExists('payments');
    }
};
