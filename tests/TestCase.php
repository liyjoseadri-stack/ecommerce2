<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\File;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $compiledViewsPath = storage_path('framework/testing/views');

        File::ensureDirectoryExists($compiledViewsPath);
        config(['view.compiled' => $compiledViewsPath]);

        $this->withoutVite();
    }
}
