<?php

namespace Wcms\Tests;

use AltoRouter;
use LogicException;
use PHPUnit\Framework\TestCase;
use Wcms\Modelrender;
use Wcms\Page;

class ModelrenderTest extends TestCase
{
    protected string $cwd;
    protected string $tmpdir;
    protected Modelrender $model;

    public function setUp(): void
    {
        parent::setUp();
        $this->cwd = getcwd();
        $this->tmpdir = $this->mktempdir('wcms-render');
        chdir($this->tmpdir);
        mkdir('assets/render', 0777, true);

        $router = new AltoRouter([
            ['GET', '/[cid:page]/', 'Controllerpage#read', 'pageread/'],
        ]);
        $this->model = new Modelrender($router);
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
        $this->model->render($page);

        $expected = __DIR__ . "/data/ModelrenderTest/$name.html";
        $actual = "$this->tmpdir/render/$name.html";
        $this->assertFileEquals($expected, $actual, "$actual render does not match expected $expected");
    }

    public function renderProvider(): array
    {
        return [
            ['empty-test', ['id' => 'empty-test']],
        ];
    }

    protected function mktempdir(string $prefix)
    {
        $tmp = sys_get_temp_dir();
        $randstr = dechex(mt_rand() % (2 << 20));
        $path = "$tmp/$prefix-$randstr";
        if (!mkdir($path)) {
            throw new LogicException("cannot create tmp dir '$path'");
        }
        return $path;
    }
}
