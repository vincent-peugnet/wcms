<?php

namespace Wcms;

use Michelf\MarkdownExtra;
use RuntimeException;

class Controllerinfo extends Controller
{
    public function __construct($render)
    {
        parent::__construct($render);
    }

    public function desktop()
    {
        if ($this->user->isinvite()) {
            if (file_exists(Model::MAN_FILE)) {
                $render = new Modelrender($this->router);
                $htmlman = file_get_contents(Model::MAN_FILE);
                $htmlman = $render->rendermanual($htmlman);

                $sum = new Summary(['max' => 4, 'sum' => $render->sum()]);
                $summary = $sum->sumparser();

                $this->showtemplate('info', ['version' => getversion(), 'manual' => $htmlman, 'summary' => $summary]);
            }
        }
    }
}
