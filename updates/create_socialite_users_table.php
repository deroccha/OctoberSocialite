<?php namespace Kakuki\OAuth2\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateSocialiteUsersTable extends Migration
{

    public function up()
    {
        Schema::create('kakuki_oauth2_socialite_users', function($table)
        {
            $table->integer('user_id')->unsigned();
            $table->string('socialite_id');
            $table->string('provider');
            $table->primary(['user_id', 'socialite_id']);
            $table->index(['user_id', 'socialite_id']);
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kakuki_oauth2_socialite_users');
    }

}
