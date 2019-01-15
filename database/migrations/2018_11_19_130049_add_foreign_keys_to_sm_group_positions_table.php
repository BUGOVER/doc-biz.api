<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSmGroupPositionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sm_group_positions', function(Blueprint $table)
		{
			$table->foreign('company_id', 'sm_group_positions_company_id')->references('company_id')->on('companies')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('group_id', 'sm_group_positions_group_id')->references('group_id')->on('groups')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('user_id', 'sm_group_positions_user_id')->references('user_id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sm_group_positions', function(Blueprint $table)
		{
			$table->dropForeign('sm_group_positions_company_id');
			$table->dropForeign('sm_group_positions_group_id');
			$table->dropForeign('sm_group_positions_user_id');
		});
	}

}
