<?php

namespace Wcms;

use RuntimeException;

class Controllerfont extends Controller
{
    /**
     * @var Modelfont
     */
    protected $fontmanager;

    public function __construct($router)
    {
        parent::__construct($router);
        $this->fontmanager = new Modelfont();
    }

    public function desktop()
    {
        if ($this->user->iseditor()) {
            try {
                Fs::dircheck(Model::FONT_DIR);
            } catch (RuntimeException $e) {
                Model::sendflashmessage($e->getMessage(), Model::FLASH_ERROR);
            }

            $fontlist = $this->fontmanager->getfontlist();

            $this->showtemplate(
                'font',
                [
                    'fontlist' => $fontlist,
                    'fonttypes' => $this->fontmanager->getfonttypes(),
                    'fontfile' => Model::dirtopath(Model::ASSETS_CSS_DIR) . 'fonts.css'
                ]
            );
        } else {
            $this->routedirect('home');
        }
    }

    public function render()
    {
        try {
            $this->fontmanager->renderfontface();
        } catch (RuntimeException $e) {
            Model::sendflashmessage("Error while rendering font file", Model::FLASH_ERROR);
        }
        $this->routedirect('font');
    }

    public function add()
    {
        if (isset($_POST['fontname'])) {
            $fontname = $_POST['fontname'];
        } else {
            $fontname = '';
        }
        $message = $this->fontmanager->upload($_FILES, 2 ** 16, $fontname);
        if ($message !== true) {
            echo $message;
        } else {
            $this->render();
        }
    }
}
