<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToGroupUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('group_user', function(Blueprint $table)
		{
			$table->foreign('role_id', 'group_user_role')->references('role_id')->on('roles')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('user_id', 'group_user_users')->references('user_id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('group_id', 'user_group_group')->references('group_id')->on('groups')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('group_user', function(Blueprint $table)
		{
			$table->dropForeign('group_user_role');
			$table->dropForeign('group_user_users');
			$table->dropForeign('user_group_group');
		});
	}

}
