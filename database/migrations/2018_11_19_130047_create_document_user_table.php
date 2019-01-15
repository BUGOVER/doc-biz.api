<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('document_user', function(Blueprint $table)
		{
			$table->increments('document_user_id');
			$table->integer('document_id')->unsigned()->nullable()->index('document_users_id');
			$table->integer('user_id')->unsigned()->nullable()->index('document_users_doc_id');
			$table->integer('role_id')->unsigned()->nullable()->index('document_user_role');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('document_user');
	}

}
