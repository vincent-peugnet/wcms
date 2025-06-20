<?php

namespace Wcms\Tests;

use Exception;
use RuntimeException;
use Throwable;
use Wcms\Logger;

class LoggerTest extends FilesTest
{
    protected string $logfile;

    protected function setUp(): void
    {
        $this->logfile = "$this->testdir/w_error.log";
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
        $this->assertIsWritable($this->logfile);
        $this->assertEmpty(file_get_contents($this->logfile));
    }

    /**
     * @test
     */
    public function initDirNotExistTest(): void
    {
        $dir = 'not/existing/path';
        $file = "$dir/w_error.log";
        $this->assertDirectoryDoesNotExist($dir);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Parent directory of '$file' does not exist.");
        Logger::init($file);
        $this->assertFileNotExists($file);
    }

    /**
     * @test
     * @requires OSFAMILY Linux
     */
    public function initDirNotWritableTest(): void
    {
        $dir = $this->notwritabledir;
        $file = "$dir/w_error.log";
        $this->assertDirectoryExists($dir);
        $this->assertDirectoryIsNotWritable($dir);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("Parent directory of '$file' is not writable.");
        Logger::init($file);
        $this->assertFileNotExists($file);
    }

    /**
     * @test
     */
    public function initNotWritableTest(): void
    {
        $file = $this->notwritablefile;
        $this->assertDirectoryExists(dirname($file));
        $this->assertDirectoryIsWritable(dirname($file));
        $this->assertIsNotWritable($file);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage("The logfile '$file' is not writable.");
        Logger::init($file);
        $this->assertFileNotExists($file);
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

    /** @return mixed[][] */
    public function errorNotLoggedProvider(): array
    {
        return [[0]];
    }

    /**
     * @test
     * @param mixed[] $args
     * @dataProvider errorLoggedProvider
     */
    public function errorLoggedTest(int $verbosity, string $msg, array $args, string $expected): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::error($msg, ...$args);
        $expected = " [ ERROR ] tests{$this->ds}LoggerTest.php(104) $expected\n";
        $this->assertEquals($expected, substr(file_get_contents($this->logfile), 25));
    }

    /** @return mixed[][] */
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

    /** @return mixed[][] */
    public function warningNotLoggedProvider(): array
    {
        return [[0], [1]];
    }

    /**
     * @test
     * @param mixed[] $args
     * @dataProvider warningLoggedProvider
     */
    public function warningLoggedTest(int $verbosity, string $msg, array $args, string $expected): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::warning($msg, ...$args);
        $expected = " [ WARN ]  tests{$this->ds}LoggerTest.php(145) $expected\n";
        $this->assertEquals($expected, substr(file_get_contents($this->logfile), 25));
    }

    /** @return mixed[][] */
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

    /** @return mixed[][] */
    public function infoNotLoggedProvider(): array
    {
        return [[0], [1], [2]];
    }

    /**
     * @test
     * @param mixed[] $args
     * @dataProvider infoLoggedProvider
     */
    public function infoLoggedTest(int $verbosity, string $msg, array $args, string $expected): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::info($msg, ...$args);
        $expected = " [ INFO ]  tests{$this->ds}LoggerTest.php(185) $expected\n";
        $this->assertEquals($expected, substr(file_get_contents($this->logfile), 25));
    }

    /** @return mixed[][] */
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

    /** @return mixed[][] */
    public function debugNotLoggedProvider(): array
    {
        return [[0], [1], [2], [3]];
    }

    /**
     * @test
     * @param mixed[] $args
     * @dataProvider debugLoggedProvider
     */
    public function debugLoggedTest(int $verbosity, string $msg, array $args, string $expected): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::debug($msg, ...$args);
        $expected = " [ DEBUG ] tests{$this->ds}LoggerTest.php(224) $expected\n";
        $this->assertEquals($expected, substr(file_get_contents($this->logfile), 25));
    }

    /** @return mixed[][] */
    public function debugLoggedProvider(): array
    {
        return [
            [4, 'Error %s.', ['test'], 'Error test.'],
        ];
    }

    /**
     * @test
     * @dataProvider errorexNotLoggedProvider
     */
    public function errorexNotLoggedTest(int $verbosity): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::errorex(new Exception('Error'));
        $this->assertEmpty(file_get_contents($this->logfile));
    }

    /** @return mixed[][] */
    public function errorexNotLoggedProvider(): array
    {
        return [[0]];
    }

    /**
     * @test
     * @dataProvider errorexLoggedProvider
     */
    public function errorexLoggedTest(int $verbosity, Throwable $e, string $expected, int $line): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::errorex($e);
        $file = __FILE__;
        $line += 272;
        $expected = " [ ERROR ] tests{$this->ds}LoggerTest.php(261) $expected in $file($line)\n";
        $this->assertEquals($expected, substr(file_get_contents($this->logfile), 25));
    }

    /** @return mixed[][] */
    public function errorexLoggedProvider(): array
    {
        return [
            [1, new Exception('Test 1'), 'Test 1', 0],
            [2, new Exception('Test 2'), 'Test 2', 1],
            [3, new Exception('Test 3'), 'Test 3', 2],
            [4, new Exception('Test 4'), 'Test 4', 3],
        ];
    }

    /**
     * @test
     * @requires OSFAMILY Linux
     */
    public function errorexBacktraceTest(): void
    {
        Logger::init($this->logfile, 1);
        Logger::errorex(new Exception('Error'), true);
        $content = file_get_contents($this->logfile);
        $expected = " [ ERROR ] tests{$this->ds}LoggerTest.php(286) Error ";
        $this->assertEquals($expected, substr($content, 25, 43));
        $this->assertMatchesRegularExpression('/(#\d+ [\w\/\.]*\(\d+\): .*\)\n)+#\d+ \{main\}\n/U', $content);
    }

    /**
     * @test
     * @dataProvider warningexNotLoggedProvider
     */
    public function warningexNotLoggedTest(int $verbosity): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::warningex(new Exception('Error'));
        $this->assertEmpty(file_get_contents($this->logfile));
    }

    /** @return mixed[][] */
    public function warningexNotLoggedProvider(): array
    {
        return [[0], [1]];
    }

    /**
     * @test
     * @dataProvider warningexLoggedProvider
     */
    public function warningexLoggedTest(int $verbosity, Throwable $e, string $expected, int $line): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::warningex($e);
        $file = __FILE__;
        $line += 328;
        $expected = " [ WARN ]  tests{$this->ds}LoggerTest.php(317) $expected in $file($line)\n";
        $this->assertEquals($expected, substr(file_get_contents($this->logfile), 25));
    }

    /** @return mixed[][] */
    public function warningexLoggedProvider(): array
    {
        return [
            [2, new Exception('Test 1'), 'Test 1', 0],
            [3, new Exception('Test 2'), 'Test 2', 1],
            [4, new Exception('Test 3'), 'Test 3', 2],
        ];
    }

    /**
     * @test
     * @dataProvider infoexNotLoggedProvider
     */
    public function infoexNotLoggedTest(int $verbosity): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::infoex(new Exception('Error'));
        $this->assertEmpty(file_get_contents($this->logfile));
    }

    /** @return mixed[][] */
    public function infoexNotLoggedProvider(): array
    {
        return [[0], [1], [2]];
    }

    /**
     * @test
     * @dataProvider infoexLoggedProvider
     */
    public function infoexLoggedTest(int $verbosity, Throwable $e, string $expected, int $line): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::infoex($e);
        $file = __FILE__;
        $line += 369;
        $expected = " [ INFO ]  tests{$this->ds}LoggerTest.php(358) $expected in $file($line)\n";
        $this->assertEquals($expected, substr(file_get_contents($this->logfile), 25));
    }

    /** @return mixed[][] */
    public function infoexLoggedProvider(): array
    {
        return [
            [3, new Exception('Test 1'), 'Test 1', 0],
            [4, new Exception('Test 2'), 'Test 2', 1],
        ];
    }

    /**
     * @test
     * @dataProvider debugexNotLoggedProvider
     */
    public function debugexNotLoggedTest(int $verbosity): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::debugex(new Exception('Error'));
        $this->assertEmpty(file_get_contents($this->logfile));
    }

    /** @return mixed[][] */
    public function debugexNotLoggedProvider(): array
    {
        return [[0], [1], [2], [3]];
    }

    /**
     * @test
     * @dataProvider debugexLoggedProvider
     */
    public function debugexLoggedTest(int $verbosity, Throwable $e, string $expected, int $line): void
    {
        Logger::init($this->logfile, $verbosity);
        Logger::debugex($e);
        $file = __FILE__;
        $line += 409;
        $expected = " [ DEBUG ] tests{$this->ds}LoggerTest.php(398) $expected in $file($line)\n";
        $this->assertEquals($expected, substr(file_get_contents($this->logfile), 25));
    }

    /** @return mixed[][] */
    public function debugexLoggedProvider(): array
    {
        return [
            [4, new Exception('Test 4'), 'Test 4', 0],
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Logger::close();
    }
}
