<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected function tearDown(): void
    {
        try {
            DB::disconnect();
        } catch (\Throwable $e) {
            // Ignore disconnect errors
        }

        parent::tearDown();
    }
}
