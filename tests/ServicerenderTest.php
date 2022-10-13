<?php

namespace Wcms\Tests;

use AltoRouter;
use PHPUnit\Framework\TestCase;
use Wcms\Fs;
use Wcms\Servicerender;
use Wcms\Page;

class ServicerenderTest extends TestCase
{
    protected string $cwd;
    protected static string $tmpdir;
    protected Servicerender $renderengine;

    public static function setUpBeforeClass(): void
    {
        self::$tmpdir = mktempdir("w-cms-test-servicerender");
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->cwd = getcwd();
        chdir("tests/fixtures");

        $router = new AltoRouter([
            ['GET', '/[cid:page]/', 'Controllerpage#read', 'pageread/'],
        ]);
        $this->renderengine = new Servicerender($router);
    }

    public function tearDown(): void
    {
        chdir($this->cwd);
        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider renderProvider
     */
    public function renderTest(string $name): void
    {
        $pagedata = json_decode(file_get_contents(__DIR__ . "/data/ServicerenderTest/$name.json"), true);
        $page = new Page($pagedata);
        $html = $this->renderengine->render($page);

        $expected = __DIR__ . "/data/ServicerenderTest/$name.html";
        $actual = self::$tmpdir . "/$name.html";
        Fs::writefile($actual, $html);

        $this->assertFileEquals($expected, $actual, "$actual render does not match expected $expected");
    }

    public function renderProvider(): array
    {
        return [
            ['empty-test'],
            ['markdown-test'],
            ['body-test'],
        ];
    }
}
