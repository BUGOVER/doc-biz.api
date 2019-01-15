<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSmGroupPositionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sm_group_positions', function(Blueprint $table)
		{
			$table->increments('group_position_id');
			$table->integer('user_id')->unsigned()->index('sm_group_positions_user_id');
			$table->integer('company_id')->unsigned()->nullable()->index('sm_group_positions_company_id');
			$table->integer('group_id')->unsigned()->nullable()->index('sm_group_positions_group_id');
			$table->boolean('current_position');
			$table->boolean('previous_position')->nullable();
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
		Schema::drop('sm_group_positions');
	}

}
