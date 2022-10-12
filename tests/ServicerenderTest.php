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
    protected string $tmpdir;
    protected Servicerender $renderengine;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->tmpdir = mktempdir("w-cms-test-servicerender");
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
     * @requires OSFAMILY Linux
     * @dataProvider renderProvider
     */
    public function renderTest(string $name, array $pagedata): void
    {
        $page = new Page($pagedata);
        $html = $this->renderengine->render($page);

        $expected = __DIR__ . "/data/ServicerenderTest/$name.html";
        $actual = "$this->tmpdir/$name.html";
        Fs::writefile($actual, $html);

        $this->assertFileEquals($expected, $actual, "$actual render does not match expected $expected");
    }

    public function renderProvider(): array
    {
        return [
            ['empty-test', ['id' => 'empty-test']],
        ];
    }
}
