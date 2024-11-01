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

        switch ($_POST['route']) {
            case 'pageedit':
                if (isset($_POST['page'])) {
                    $this->routedirect('pageedit', ['page' => $_POST['page']]);
                }
                break;

            case 'home':
                $this->routedirect('home');
                break;

            case 'media':
                $this->routedirect('media');
                break;
        }
    }
}
