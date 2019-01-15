<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAuthCompaniesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('auth_companies', function(Blueprint $table)
		{
			$table->increments('auth_company_id');
			$table->integer('company_id')->unsigned()->index('auth_companies_company_id');
			$table->integer('user_id')->unsigned()->index('auth_companies_user_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('auth_companies');
	}

}
