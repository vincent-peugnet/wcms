<?php

namespace Wcms;

class Controllerworkspace extends Controller
{
    public function update()
    {
        if ($this->user->isinvite()) {
            $this->workspace->hydrate($_POST);
            $this->servicesession->setworkspace($this->workspace);
        }
        if (isset($_POST['page'])) {
            $this->routedirect('pageedit', ['page' => $_POST['page']]);
        } else {
            $this->routedirect('home');
        }
    }
}
