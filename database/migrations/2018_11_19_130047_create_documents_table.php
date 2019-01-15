<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDocumentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('documents', function(Blueprint $table)
		{
			$table->increments('document_id');
			$table->integer('owner_id')->unsigned()->index('documents_owner');
			$table->integer('group_id')->unsigned()->nullable()->index('documents_group');
			$table->integer('company_id')->unsigned()->nullable()->index('documents_company_id');
			$table->string('name', 50);
			$table->string('slug_url', 100)->nullable();
			$table->string('description', 500)->nullable();
			$table->text('content', 65535)->nullable();
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
		Schema::drop('documents');
	}

}
