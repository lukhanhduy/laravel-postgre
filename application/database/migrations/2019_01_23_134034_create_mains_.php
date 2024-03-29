<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMains extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();
        Schema::create('files', function (Blueprint $table) {
            $table->increments('file_id');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('file_size');
            $table->unsignedInteger('object_id');
            $table->integer('object_type');
            $table->unsignedInteger('created_by')->nullable();
            $table->foreign('created_by')->references('file_id')->on('files')->onDelete('set null');
            $table->boolean('is_locale')->default(false);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->increments('role_id');
            $table->string('role_name');
            $table->tinyInteger('status')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->increments('userId');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone_number');
            $table->integer('user_type')->default(1);
            $table->string('slug');
            $table->tinyInteger('status')->default(1);
            $table->unsignedInteger('avatar')->nullable();
            $table->foreign('avatar')->references('file_id')->on('files')->onDelete('set null');
            $table->string('password');
            $table->rememberToken();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('passwords', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('admins', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('password');
            $table->unsignedInteger('role_id')->nullable();
            $table->string('slug')->nullable();
            $table->string('phone_number')->nullable();
            $table->unsignedInteger('avatar')->nullable();
            $table->foreign('avatar')->references('file_id')->on('files')->onDelete('set null');
            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('modules', function (Blueprint $table) {
            $table->increments('module_id');
            $table->string('module_name');
            $table->tinyInteger('status')->default(1);
            $table->string('slug')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('module_id')->on('modules')->onDelete('set null');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });


        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('permission_id');
            $table->unsignedInteger('role_id');
            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');
            $table->unsignedInteger('module_id');
            $table->foreign('module_id')->references('module_id')->on('modules')->onDelete('cascade');
            $table->json('rules');
            $table->tinyInteger('status')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->increments('category_id');
            $table->string('category_name');
            $table->string('slug');
            $table->unsignedInteger('icon')->nullable();
            $table->foreign('icon')->references('file_id')->on('files')->onDelete('set null');
            $table->tinyInteger('status')->default(1);
            $table->unsignedInteger('parent_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('systems', function (Blueprint $table) {
            $table->increments('field_id');
            $table->string('field_name');
            $table->string('field_data');
            $table->tinyInteger('status')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->increments('product_id');
            $table->string('product_name');
            $table->string('slug');
            $table->longText('description');
            $table->unsignedInteger('category_id')->nullable();
            $table->foreign('category_id')->references('category_id')->on('categories')->onDelete('set null');
            $table->unsignedInteger('image')->nullable();
            $table->foreign('image')->references('file_id')->on('files')->onDelete('set null');
            $table->tinyInteger('status')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('product_details', function (Blueprint $table) {
            $table->increments('field_id');
            $table->unsignedInteger('product_id');
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            $table->string('field_type');
            $table->longText('field_value');
            $table->tinyInteger('status')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_details');
        Schema::dropIfExists('systems');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('passwords');
        Schema::dropIfExists('users');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('files');
        Schema::dropIfExists('roles');
        
    }
}
