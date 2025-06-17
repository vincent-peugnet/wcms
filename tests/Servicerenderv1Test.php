<?php

namespace Wcms\Tests;

use AltoRouter;
use DOMDocument;
use PHPUnit\Framework\TestCase;
use Wcms\Config;
use Wcms\Fs;
use Wcms\Modelpage;
use Wcms\Servicerender;
use Wcms\Pagev1;
use Wcms\Servicerenderv1;

class Servicerenderv1Test extends TestCase
{
    protected string $cwd;
    protected static string $tmpdir;
    protected Servicerender $renderengine;

    public static function setUpBeforeClass(): void
    {
        self::$tmpdir = mktmpdir("w-cms-test-servicerenderv1");
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->cwd = getcwd();
        chdir("tests/fixtures");

        $router = new AltoRouter([
            ['GET', '/[cid:page]', 'Controllerpage#read', 'pageread'],
        ]);
        $this->renderengine = new Servicerenderv1($router, new Modelpage(Config::pagetable()), true, false);
    }

    public function tearDown(): void
    {
        chdir($this->cwd);
        parent::tearDown();
    }

    public function renderTest(string $name): void
    {
        $pagedata = json_decode(file_get_contents(__DIR__ . "/data/Servicerenderv1Test/$name.json"), true);
        $page = new Pagev1($pagedata);
        $html = $this->renderengine->render($page);

        $expected = __DIR__ . "/data/Servicerenderv1Test/$name.html";
        $actual = self::$tmpdir . "/$name.html";

        $doc = new DOMDocument();
        $doc->loadHTML($html, LIBXML_NOERROR);
        $body = $doc->getElementsByTagName("body")->item(0);
        $body = $doc->saveHTML($body) . "\n";

        Fs::writefile($actual, $body);

        $this->assertFileEquals($expected, $actual, "$actual render does not match expected $expected");
    }

    /**
     * @test
     * @dataProvider renderProvider
     */
    public function renderTestCommon(string $name): void
    {
        $this->renderTest($name);
    }

    /**
     * @return array<array<string>>
     */
    public function renderProvider(): array
    {
        return [
            ['empty-test'],
            ['markdown-test'],
            ['markdown-test-2'],
            ['body-test'],
            ['external-links-test'],
        ];
    }

    /**
     * @test
     * @requires OS Linux
     * @requires extension intl
     */
    public function renderTestDate(): void
    {
        if (floatval(INTL_ICU_VERSION) >= 72) {
            $this->markTestSkipped();
        }
        $this->renderTest('date-time-test');
    }
}
