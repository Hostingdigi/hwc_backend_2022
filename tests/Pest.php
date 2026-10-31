<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test setup
|--------------------------------------------------------------------------
|
| Feature tests boot Laravel against the MySQL database named in phpunit.xml
| (hwc_backend_testing). RefreshDatabase loads database/schema/mysql-schema.dump
| once per run (this needs the `mysql` client on PATH) and wraps every test in
| a transaction.
|
| Unit tests are plain PHP and need neither Laravel nor a database.
|
*/

uses(TestCase::class, RefreshDatabase::class)->in('Feature');
