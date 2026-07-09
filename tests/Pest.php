<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| RefreshDatabase migrates a fresh schema before each test and wraps the
| test in a transaction that's rolled back afterward. Combined with the
| in-memory SQLite connection set in .env.testing, every test run starts
| from a clean, fully-migrated database — no manual setup, no leftover
| data between tests, no need for a real MySQL test database at all.
|
*/

uses(TestCase::class, RefreshDatabase::class)->in('Feature');
