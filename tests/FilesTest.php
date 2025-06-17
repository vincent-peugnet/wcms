<?php

namespace Wcms\Tests;

use PHPUnit\Framework\TestCase;

/**
 * This abstract test class adds 3 usefull variables for files tests:
 * - $this->testdir
 * - $this->notwritabledir
 * - $this->notwritablefile
 */
abstract class FilesTest extends TestCase
{
    protected string $ds = DIRECTORY_SEPARATOR;
    protected string $testdir = 'build/test';
    protected string $notwritabledir = 'build/test/notwritabledir';
    protected string $notwritablefile = 'build/test/notwritablefile';

    protected function setUp(): void
    {
        parent::setUp();
        if (!is_dir($this->testdir)) {
            mkdir($this->testdir, 0755, true);
        }
        if (!file_exists($this->notwritabledir)) {
            mkdir($this->notwritabledir, 0000);
        }
        if (!file_exists($this->notwritablefile)) {
            touch($this->notwritablefile);
            chmod($this->notwritablefile, 0000);
        }
    }
}
