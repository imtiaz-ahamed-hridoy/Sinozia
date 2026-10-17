<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected static bool $createdTempEnv = false;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if (! file_exists(dirname(__DIR__).'/.env') && file_exists(dirname(__DIR__).'/.env.example')) {
            @copy(dirname(__DIR__).'/.env.example', dirname(__DIR__).'/.env');
            static::$createdTempEnv = true;
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public static function tearDownAfterClass(): void
    {
        if (static::$createdTempEnv && file_exists(dirname(__DIR__).'/.env')) {
            @unlink(dirname(__DIR__).'/.env');
            static::$createdTempEnv = false;
        }
        parent::tearDownAfterClass();
    }
}
