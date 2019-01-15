<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('group_user', function(Blueprint $table)
		{
			$table->increments('group_user_id');
			$table->integer('group_id')->unsigned()->index('user_group_group');
			$table->integer('user_id')->unsigned()->index('user_group_user');
			$table->integer('role_id')->unsigned()->nullable()->index('group_user_role');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('group_user');
	}

}
