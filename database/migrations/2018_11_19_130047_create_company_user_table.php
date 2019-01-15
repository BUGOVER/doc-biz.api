<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCompanyUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('company_user', function(Blueprint $table)
		{
			$table->increments('company_user_id');
			$table->integer('company_id')->unsigned()->nullable()->index('users_company_company');
			$table->integer('user_id')->unsigned()->nullable()->index('users_company_user');
			$table->integer('role_id')->unsigned()->nullable()->index('company_user_role');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('company_user');
	}

}
