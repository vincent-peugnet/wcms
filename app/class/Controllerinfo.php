<?php

namespace Wcms;

use AltoRouter;
use RuntimeException;

class Controllerinfo extends Controller
{
    public function __construct(AltoRouter $router)
    {
        parent::__construct($router);

        if ($this->user->isvisitor()) {
            $this->showconnect('info');
        }
    }

    public function desktop(): never
    {
        $version = getversion();
        $mandir = Model::MAN_RENDER_DIR;
        try {
            $manual = Fs::readfile("$mandir/manual_$version.html");
            $summary = Fs::readfile("$mandir/summary_$version.html");
        } catch (RuntimeException $e) {
            try {
                $mansrc = Fs::readfile(Model::MAN_FILE);
                $render = new Servicerenderv2($this->router, $this->pagemanager, true);
                $manual = $render->rendermanual($mansrc);

                $sum = new Summary(['min' => 2, 'max' => 4, 'sum' => $render->sum()]);
                $summary = $sum->sumparser();

                Fs::folderflush($mandir);
                Fs::dircheck($mandir, true, 0775);

                Fs::writefile("$mandir/manual_$version.html", $manual);
                Fs::writefile("$mandir/summary_$version.html", $summary);
            } catch (RuntimeException $e) {
                $manual = '⚠️ Error while trying to access MANUAL.md file.';
                $summary = '';
            }
        }
        $this->showtemplate('info', ['version' => getversion(), 'manual' => $manual, 'summary' => $summary]);
    }
}
