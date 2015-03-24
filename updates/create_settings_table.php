<?php namespace Kakuki\OAuth2\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class CreateSettingsTable extends Migration {

	public function up() {
		Schema::create('kakuki_oauth2_settings', function ($table) {
			$table->engine = 'InnoDB';
			$table->increments('id');
			$table->string('provider');
			$table->text('client_id');
			$table->text('client_secret');
			$table->timestamps();
		});
	}

	public function down() {
		Schema::dropIfExists('kakuki_oauth2_settings');
	}

}
