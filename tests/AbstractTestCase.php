<?php
namespace Tests;

abstract class AbstractTestCase extends \PHPUnit\Framework\TestCase
{
    public function getStubsFile($filename)
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'yaml_stubs' . DIRECTORY_SEPARATOR . $filename . '.yml';
    }
}