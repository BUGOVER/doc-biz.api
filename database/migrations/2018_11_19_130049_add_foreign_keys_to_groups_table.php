<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('groups', function(Blueprint $table)
		{
			$table->foreign('owner_id', 'group_owner')->references('user_id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('company_id', 'groups_company_id')->references('company_id')->on('companies')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('groups', function(Blueprint $table)
		{
			$table->dropForeign('group_owner');
			$table->dropForeign('groups_company_id');
		});
	}

}
