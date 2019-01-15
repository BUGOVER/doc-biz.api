<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('groups', function(Blueprint $table)
		{
			$table->increments('group_id');
			$table->integer('owner_id')->unsigned()->nullable()->index('group_owner');
			$table->integer('company_id')->unsigned()->nullable()->index('groups_company_id');
			$table->string('name', 50)->unique('groups_name_uindex');
			$table->string('slug_url', 100)->unique('groups_slug_url_uindex');
			$table->boolean('type');
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
		Schema::drop('groups');
	}

}
