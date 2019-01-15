<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToDocumentUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('document_user', function(Blueprint $table)
		{
			$table->foreign('role_id', 'document_user_role')->references('role_id')->on('roles')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('user_id', 'document_users_doc_id')->references('user_id')->on('users')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			$table->foreign('document_id', 'document_users_id')->references('document_id')->on('documents')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('document_user', function(Blueprint $table)
		{
			$table->dropForeign('document_user_role');
			$table->dropForeign('document_users_doc_id');
			$table->dropForeign('document_users_id');
		});
	}

}
