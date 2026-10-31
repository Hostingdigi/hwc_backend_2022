<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tables the code uses that were missing from the local copy of the database
 * (hwc_new) when the schema baseline was taken. Columns are RECONSTRUCTED FROM
 * THE CODE, not copied from production, so types and lengths are best guesses.
 *
 * Every table is guarded with hasTable(), so on production (where they already
 * exist) this migration does nothing. Replace with the real definitions once a
 * production structure dump is available.
 */
class CreateTablesMissingFromLocalSchema extends Migration
{
    public function up()
    {
        // Admin password change screen (App\Models\PasswordChange)
        $this->createIfMissing('admin', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->nullableTimestamps();
        });

        $this->createIfMissing('coupon_code', function (Blueprint $table) {
            $table->increments('id');
            $table->string('coupon_code');
            $table->integer('customer_id')->default(0);
            $table->integer('category_id')->default(0);
            $table->integer('brand_id')->default(0);
            $table->date('validity')->nullable();
            $table->integer('nooftimes')->default(0);
            $table->tinyInteger('discount_type')->default(1); // 1 = percent, else fixed amount
            $table->decimal('discount', 10, 2)->default(0);
            $table->tinyInteger('customer_type')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->nullableTimestamps();
        });

        $this->createIfMissing('coupon_code_usage', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('coupon_id');
            $table->integer('customer_id')->default(0);
            $table->integer('order_id')->default(0);
            $table->nullableTimestamps();
        });

        $this->createIfMissing('paymentlog', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->nullable();
            $table->string('pay_method')->nullable();
            $table->longText('sent_values')->nullable();
            $table->longText('received_values')->nullable();
            $table->string('status')->nullable();
            $table->nullableTimestamps();
        });

        $this->createIfMissing('mobileversions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('version_id')->nullable();
            $table->string('version_name')->nullable();
            $table->nullableTimestamps();
        });

        $this->createIfMissing('account_deactivate_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->dateTime('date_time')->nullable();
            $table->string('otp_number')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->text('description')->nullable();
            $table->nullableTimestamps();
        });

        $this->createIfMissing('self_collection_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->date('colection_start_date')->nullable();
            $table->string('colection_start_time')->nullable();
            $table->date('colection_to_date')->nullable();
            $table->string('colection_to_time')->nullable();
            $table->tinyInteger('collection_status')->default(0);
            $table->text('remarks')->nullable();
            $table->nullableTimestamps();
        });

        $this->createIfMissing('producttag', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tagname');
            $table->tinyInteger('status')->default(1);
            $table->nullableTimestamps();
        });

        $this->createIfMissing('sms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('templatename')->nullable();
            $table->integer('template_type')->nullable();
            $table->string('subject')->nullable();
            $table->text('content')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->nullableTimestamps();
        });

        // Role & rights module list
        $this->createIfMissing('modules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('module_name');
            $table->integer('parent_id')->default(0);
            $table->integer('action_count')->default(0);
            $table->nullableTimestamps();
        });

        $this->createIfMissing('country_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('iso', 2);
            $table->string('name')->nullable();
            $table->string('phonecode', 10)->nullable();
        });

        $this->createIfMissing('punchout_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('buyer_cookie');
            $table->string('payload_id')->nullable();
            $table->string('deployment')->nullable();
            $table->text('browser_form_post_url')->nullable();
            $table->text('supplier_setup_url')->nullable();
            $table->longText('cxml')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        // Intentionally empty: on production these tables hold real data and
        // were not created by this migration.
    }

    private function createIfMissing($name, Closure $definition)
    {
        if (!Schema::hasTable($name)) {
            Schema::create($name, $definition);
        }
    }
}
