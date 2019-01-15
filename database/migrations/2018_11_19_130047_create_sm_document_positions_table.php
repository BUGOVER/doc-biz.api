<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSmDocumentPositionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sm_document_positions', function(Blueprint $table)
		{
			$table->integer('document_position_id', true);
			$table->integer('user_id')->unsigned()->index('sm_document_positions_user_id');
			$table->integer('company_id')->unsigned()->nullable()->index('sm_document_positions_company_id');
			$table->integer('document_id')->unsigned()->index('sm_document_positions_docuemnt_id');
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
		Schema::drop('sm_document_positions');
	}

}
