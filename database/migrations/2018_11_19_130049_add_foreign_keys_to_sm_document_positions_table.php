<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSmDocumentPositionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sm_document_positions', function(Blueprint $table)
		{
			$table->foreign('company_id', 'sm_document_positions_company_id')->references('company_id')->on('companies')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('document_id', 'sm_document_positions_docuemnt_id')->references('document_id')->on('documents')->onUpdate('RESTRICT')->onDelete('CASCADE');
			$table->foreign('user_id', 'sm_document_positions_user_id')->references('user_id')->on('users')->onUpdate('RESTRICT')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sm_document_positions', function(Blueprint $table)
		{
			$table->dropForeign('sm_document_positions_company_id');
			$table->dropForeign('sm_document_positions_docuemnt_id');
			$table->dropForeign('sm_document_positions_user_id');
		});
	}

}
