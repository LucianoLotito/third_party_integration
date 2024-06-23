<?php

declare(strict_types=1);

namespace Tests;

use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected Generator $faker;

    public function __construct(string $name)
    {
        $this->faker = Factory::create();
        parent::__construct($name);
    }
}
