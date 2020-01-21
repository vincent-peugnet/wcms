<?php

namespace Wcms;
use Michelf\MarkdownExtra;

class Controllerinfo extends Controller
{
    public function __construct($render){
        parent::__construct($render);
    }

    public function desktop()
    {
        if($this->user->iseditor()) {

            if(file_exists(Model::MAN_FILE)) {

                $render = new Modelrender($this->router);
                $htmlman = file_get_contents(Model::MAN_FILE);
                $htmlman = $render->rendermanual($htmlman);

                $summary = $render->sumparser(2, 4);

                $this->showtemplate('info', ['version' => getversion(), 'manual' => $htmlman, 'summary' => $summary]);

            }
        }
    }

}


?>