<?php

namespace Wcms;

class Controllerapiworkspace extends Controllerapi
{
    public function update()
    {
        if (!empty($_POST)) {
            $this->workspace->hydrate($_POST);
            $this->servicesession->setworkspace($this->workspace);
        } else {
            $this->shortresponse(400, "No POST datas recieved");
        }
    }
}
