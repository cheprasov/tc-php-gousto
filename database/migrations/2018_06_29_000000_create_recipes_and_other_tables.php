<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecipesAndOtherTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->increments('id');
            $table->char('title');
        });

        Schema::create('recipes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->enum('box_type', ['gourmet', 'vegetarian']);
            $table->string('title');
            $table->string('slug');
            $table->string('short_title')->nullable();
            $table->text('marketing_description')->default('');
            $table->integer('calories_kcal', false, true);
            $table->integer('protein_grams', false, true);
            $table->integer('fat_grams', false, true);
            $table->integer('carbs_grams', false, true);
            $table->string('bulletpoint1')->nullable();
            $table->string('bulletpoint2')->nullable();
            $table->string('bulletpoint3')->nullable();
            // I do not see reasons for a separate table
            $table->enum('recipe_diet_type_id', ['meat', 'fish', 'vegetarian']);
            $table->enum('season', ['all']);
            $table->enum('base', ['noodles', 'pasta', 'beans/lentils'])->nullable();
            $table->enum('protein_source', ['beef', 'seafood', 'pork', 'cheese', 'chicken', 'eggs', 'fish']);
            $table->unsignedTinyInteger('preparation_time_minutes');
            $table->unsignedTinyInteger('shelf_life_days');
            // It is better to use a separate table, but for the test is fine
            $table->enum('equipment_needed', ['None', 'Appetite', 'Pestle & Mortar (optional)']);
            $table->unsignedInteger('origin_country_id');
            // It can be like foreign, but I think enum is fine
            $table->enum('recipe_cuisine', ['asian', 'italian', 'british', 'mediterranean', 'mexican']);
            $table->text('in_your_box')->default('');
            $table->unsignedInteger('gousto_reference');

            $table->foreign('origin_country_id')->references('id')->on('countries');
            $table->index(['recipe_cuisine']);
        });

        Schema::create('rates', function (Blueprint $table) {
            $table->unsignedInteger('recipe_id');
            $table->unsignedInteger('user_id');
            $table->unsignedTinyInteger('rate');

            $table->foreign('recipe_id')->references('id')->on('recipes');
            $table->primary(['recipe_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('rates');
        Schema::drop('recipes');
        Schema::drop('countries');
    }
}
