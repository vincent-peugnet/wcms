<?php

namespace Wcms;

use LogicException;

class Controllerworkspace extends Controller
{
    public function update(): never
    {
        if (!$this->user->isinvite()) {
            http_response_code(403);
            $this->showtemplate('forbidden');
        }
        $this->workspace->hydrate($_POST);
        $this->servicesession->setworkspace($this->workspace);

        switch ($_POST['route']) {
            case 'pageedit':
                if (isset($_POST['page'])) {
                    $this->routedirect('pageedit', ['page' => $_POST['page']]);
                } else {
                    throw new LogicException("Missing page value send through post data for route pageedit");
                }

            case 'home':
                $this->routedirect('home');

            case 'media':
                $this->routedirect('media');

            default:
                $route = $_POST['route'];
                throw new LogicException(
                    "Invalid route value send through post data: '$route'. Should be pagedit, home or media"
                );
        }
    }
}
