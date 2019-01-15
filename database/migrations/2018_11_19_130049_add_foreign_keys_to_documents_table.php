<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDocumentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('documents', function(Blueprint $table)
		{
			$table->foreign('company_id', 'documents_company_id')->references('company_id')->on('companies')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('group_id', 'documents_group')->references('group_id')->on('groups')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('owner_id', 'documents_owner')->references('user_id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('documents', function(Blueprint $table)
		{
			$table->dropForeign('documents_company_id');
			$table->dropForeign('documents_group');
			$table->dropForeign('documents_owner');
		});
	}

}
