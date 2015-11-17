<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_group_id');
            $table->string('username');
            $table->string('fullname');
            $table->string('info');
            $table->string('password');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_group_id')->references('id')->on('user_groups')->onDelete('cascade');
        });

        Schema::create('user_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('locale')->index();
            $table->string('bio');
            $table->unique(['user_id', 'locale']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('user_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('title');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('user_groups');
        Schema::dropIfExists('user_translations');
        Schema::dropIfExists('users');
    }
}