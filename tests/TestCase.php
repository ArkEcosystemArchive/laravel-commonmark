<?php

namespace Tests;

use ARKEcosystem\CommonMark\CommonMarkServiceProvider;
use GrahamCampbell\Markdown\MarkdownServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * @coversNothing
 */
class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            MarkdownServiceProvider::class,
            CommonMarkServiceProvider::class,
        ];
    }
}
