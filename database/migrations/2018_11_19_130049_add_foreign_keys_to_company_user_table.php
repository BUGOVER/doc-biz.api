<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCompanyUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('company_user', function(Blueprint $table)
		{
			$table->foreign('role_id', 'company_user_role')->references('role_id')->on('roles')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('company_id', 'users_company_company')->references('company_id')->on('companies')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('user_id', 'users_company_user')->references('user_id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('company_user', function(Blueprint $table)
		{
			$table->dropForeign('company_user_role');
			$table->dropForeign('users_company_company');
			$table->dropForeign('users_company_user');
		});
	}

}
