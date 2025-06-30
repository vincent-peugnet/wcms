<?php

namespace Wcms;

use AltoRouter;
use LogicException;
use RuntimeException;
use Wcms\Exception\Filesystemexception;

class Controlleradmin extends Controller
{
    /** @var Modelmedia $mediamanager */
    protected $mediamanager;
    /** @var Modeladmin */
    protected $adminmanager;

    public function __construct(AltoRouter $router)
    {
        parent::__construct($router);

        $this->adminmanager = new Modeladmin();

        if ($this->user->isvisitor()) {
            http_response_code(401);
            $this->showtemplate('connect', ['route' => 'admin']);
        }
        if (!$this->user->isadmin()) {
            http_response_code(403);
            $this->showtemplate('forbidden');
        }
    }

    public function desktop(): void
    {
        $datas['pagelist'] = $this->pagemanager->list();
        $this->mediamanager = new Modelmedia();
        $datas['faviconlist'] = $this->mediamanager->listfavicon();
        $datas['thumbnaillist'] = $this->mediamanager->listthumbnail();
        $datas['themes'] = $this->mediamanager->listthemes();

        $globalcssfile = Model::GLOBAL_CSS_FILE;

        if (is_file($globalcssfile)) {
            $datas['globalcss'] = file_get_contents($globalcssfile);
        } else {
            $datas['globalcss'] = "";
        }

        try {
            $datas['pagetables'] = $this->adminmanager->pagetables();
        } catch (RuntimeException $e) {
            Logger::errorex($e);
            $datas['pagetables'] = [];
        }

        $this->showtemplate('admin', $datas);
    }

    public function update(): void
    {
        try {
            Fs::accessfile(Model::GLOBAL_CSS_FILE, true);
            Fs::writefile(Model::GLOBAL_CSS_FILE, $_POST['globalcss'], 0664);
            Config::hydrate($_POST);
            Config::savejson();
            $this->sendflashmessage("Configuration succesfully updated", self::FLASH_SUCCESS);
        } catch (Filesystemexception $e) {
            $this->sendflashmessage("Can't write config file or global css file", self::FLASH_ERROR);
        }
        $this->routedirect('admin');
    }

    public function database(): void
    {
        if (!empty($_POST['action'])) {
            switch ($_POST['action']) {
                case 'duplicate':
                    if (!empty($_POST['dbsrc']) && !empty($_POST['dbtarget'])) {
                        $this->adminmanager->copydb($_POST['dbsrc'], $_POST['dbtarget']);
                    }
                    break;
                case 'select':
                    if (!empty($_POST['pagetable'])) {
                        Config::hydrate($_POST);
                        try {
                            $this->pagemanager->flushrendercache();
                            Config::savejson();
                        } catch (RuntimeException $e) {
                            $this->sendflashmessage(
                                'Cannot update Config file : ' . $e->getMessage(),
                                self::FLASH_ERROR
                            );
                        }
                    }
                    break;
            }
        }
        $this->routedirect('admin');
    }

    public function log(): void
    {
        if (!$this->user->isadmin()) {
            http_response_code(403);
            $this->showtemplate('forbidden');
        }

        $filters = [
            'warn' => $_GET['warn'] ?? true,
            'error' => $_GET['error'] ?? true,
            'info' => $_GET['info'] ?? true,
            'limit' => $_GET['limit'] ?? 500,
        ];

        $loglines = file(Model::ERROR_LOG);
        $filelines = count($loglines);

        $stats = stat(Model::ERROR_LOG);
        if ($stats === false) {
            throw new LogicException();
        }

        $logs = [];
        $t = 0;
        $max = max(0, $filelines - $filters['limit']);
        for ($l = $filelines - 1; $l >= 0; $l--) {
            if ($t >= $filters['limit']) {
                break;
            }
            $line = $loglines[$l];
            if (!str_starts_with($line, '#')) {
                try {
                    $log = new Logline($line);
                    if ($filters[$log->level]) {
                        $logs[$l + 1] = $log;
                        $t++;
                    }
                } catch (RuntimeException $e) {
                    // Skip the line if parsing failed
                }
            }
        }
        $this->showtemplate('adminlog', array_merge([
            'logs' => array_reverse($logs, true),
            'total' => $t,
            'filesize' => $stats['size'],
            'filelines' => $filelines,
        ], $filters));
    }
}
