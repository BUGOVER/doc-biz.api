<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAuthCompaniesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('auth_companies', function(Blueprint $table)
		{
			$table->foreign('company_id', 'auth_companies_company_id')->references('company_id')->on('companies')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('user_id', 'auth_companies_user_id')->references('user_id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('auth_companies', function(Blueprint $table)
		{
			$table->dropForeign('auth_companies_company_id');
			$table->dropForeign('auth_companies_user_id');
		});
	}

}
