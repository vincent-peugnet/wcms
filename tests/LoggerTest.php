<?php

namespace Wcms\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Throwable;
use Wcms\Logger;

class LoggerTest extends TestCase
{
    protected $logfile = 'build/w_error.log';

    protected function setUp(): void
    {
        parent::setUp();
        if (file_exists($this->logfile)) {
            unlink($this->logfile);
        }
    }

    /**
     * @test
     */
    public function initTest(): void
    {
        Logger::init($this->logfile);
        $this->assertFileExists($this->logfile, 'Log file has not been created.');
        $this->assertEmpty(file_get_contents($this->logfile));
    }

    /**
     * @test
     * @dataProvider errorNotLoggedProvider
     */
    public function errorNotLoggedTest(int $verbosity): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::error('Error.');
        $this->assertEmpty(file_get_contents($this->logfile));
    }

    public function errorNotLoggedProvider(): array
    {
        return [[0]];
    }

    /**
     * @test
     * @dataProvider errorLoggedProvider
     */
    public function errorLoggedTest(int $verbosity, string $msg, array $args, string $expected): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::error($msg, ...$args);
        $expected = " [ ERROR ] tests/LoggerTest.php(55) $expected\n";
        $this->assertEquals($expected, substr(file_get_contents($this->logfile), 25));
    }

    public function errorLoggedProvider(): array
    {
        return [
            [1, 'Error %s.', ['test'], 'Error test.'],
            [2, 'Error %s %d.', ['test', 2], 'Error test 2.'],
            [3, 'Error.', [], 'Error.'],
            [4, 'Error.', [], 'Error.'],
        ];
    }

    /**
     * @test
     * @dataProvider warningNotLoggedProvider
     */
    public function warningNotLoggedTest(int $verbosity): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::warning('Error.');
        $this->assertEmpty(file_get_contents($this->logfile));
    }

    public function warningNotLoggedProvider(): array
    {
        return [[0], [1]];
    }

    /**
     * @test
     * @dataProvider warningLoggedProvider
     */
    public function warningLoggedTest(int $verbosity, string $msg, array $args, string $expected): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::warning($msg, ...$args);
        $expected = " [ WARN ]  tests/LoggerTest.php(93) $expected\n";
        $this->assertEquals($expected, substr(file_get_contents($this->logfile), 25));
    }

    public function warningLoggedProvider(): array
    {
        return [
            [2, 'Error %s.', ['test'], 'Error test.'],
            [3, 'Error.', [], 'Error.'],
            [4, 'Error.', [], 'Error.'],
        ];
    }

    /**
     * @test
     * @dataProvider infoNotLoggedProvider
     */
    public function infoNotLoggedTest(int $verbosity): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::info('Error.');
        $this->assertEmpty(file_get_contents($this->logfile));
    }

    public function infoNotLoggedProvider(): array
    {
        return [[0], [1], [2]];
    }

    /**
     * @test
     * @dataProvider infoLoggedProvider
     */
    public function infoLoggedTest(int $verbosity, string $msg, array $args, string $expected): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::info($msg, ...$args);
        $expected = " [ INFO ]  tests/LoggerTest.php(130) $expected\n";
        $this->assertEquals($expected, substr(file_get_contents($this->logfile), 25));
    }

    public function infoLoggedProvider(): array
    {
        return [
            [3, 'Error %s.', ['test'], 'Error test.'],
            [4, 'Error.', [], 'Error.'],
        ];
    }

    /**
     * @test
     * @dataProvider debugNotLoggedProvider
     */
    public function debugNotLoggedTest(int $verbosity): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::debug('Error.');
        $this->assertEmpty(file_get_contents($this->logfile));
    }

    public function debugNotLoggedProvider(): array
    {
        return [[0], [1], [2], [3]];
    }

    /**
     * @test
     * @dataProvider debugLoggedProvider
     */
    public function debugLoggedTest(int $verbosity, string $msg, array $args, string $expected): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::debug($msg, ...$args);
        $expected = " [ DEBUG ] tests/LoggerTest.php(166) $expected\n";
        $this->assertEquals($expected, substr(file_get_contents($this->logfile), 25));
    }

    public function debugLoggedProvider(): array
    {
        return [
            [4, 'Error %s.', ['test'], 'Error test.'],
        ];
    }

    /**
     * @test
     * @dataProvider exceptionNotLoggedProvider
     */
    public function exceptionNotLoggedTest(int $verbosity): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::exception(new Exception('Error'));
        $this->assertEmpty(file_get_contents($this->logfile));
    }

    public function exceptionNotLoggedProvider(): array
    {
        return [[0]];
    }

    /**
     * @test
     * @dataProvider exceptionLoggedProvider
     */
    public function exceptionLoggedTest(int $verbosity, Throwable $e, string $expected)
    {
        Logger::init($this->logfile, $verbosity);
        Logger::exception($e);
        $expected = " [ ERROR ] tests/LoggerTest.php(201) $expected\n";
        $this->assertEquals($expected, substr(file_get_contents($this->logfile), 25));
    }

    public function exceptionLoggedProvider(): array
    {
        return [
            [1, new Exception('Test 1'), 'Test 1'],
            [2, new Exception('Test 2'), 'Test 2'],
            [3, new Exception('Test 3'), 'Test 3'],
            [4, new Exception('Test 4'), 'Test 4'],
        ];
    }

    /**
     * @test
     */
    public function exceptionBacktraceTest(): void
    {
        Logger::init($this->logfile, 1);
        Logger::exception(new Exception('Error'), true);
        $content = file_get_contents($this->logfile);
        $expected = " [ ERROR ] tests/LoggerTest.php(222) Error\n";
        $this->assertEquals($expected, substr($content, 25, 43));
        $this->assertRegExp('/(#\d+ [\w\/\.]*\(\d+\): .*\)\n)+#\d+ \{main\}\n/U', substr($content, 68));
    }
}
