<?php


class Routes
{
    /**
     * Cherche une correspondance entre l'URL et les routes, et appelle la méthode appropriée
     */
    public function match()
    {
        $router = new AltoRouter();
        if(!empty(Config::basepath())) {
            $router->setBasePath(DIRECTORY_SEPARATOR . Config::basepath());
        }
        $router->addMatchTypes(array('cid' => '[a-zA-Z0-9-_+,\'!%@&.$€=\(\|\)]+'));
        $router->addRoutes([
            ['GET|POST', '/', 'Backrouter#run', 'backrouter'],
            ['POST', '/!co', 'Controllerconnect#log', 'log'],
            ['GET', '/!co', 'Controllerconnect#connect', 'connect'],
            ['GET', '/!m', 'Controllermedia#desktop', 'media'],
            ['GET', '/[cid:art]/', 'Controllerart#read', 'artread/'],
            ['GET', '/[cid:art]', 'Controllerart#read', 'artread'],
            ['GET', '/[cid:art]/add', 'Controllerart#add', 'artadd'],
            ['GET', '/[cid:art]/edit', 'Controllerart#edit', 'artedit'],
            ['GET', '/[cid:art]/log', 'Controllerart#log', 'artlog'],
            ['POST', '/[cid:art]/edit', 'Controllerart#update', 'artupdate'],
            ['GET', '/[cid:art]/delete', 'Controllerart#confirmdelete', 'artconfirmdelete'],
            ['POST', '/[cid:art]/delete', 'Controllerart#delete', 'artdelete'],
            ['GET', '/[cid:art]/[*]', 'Controllerart#artdirect', 'artread/etoile'],
        ]);

        $match = $router->match();
        if ($match) {
            $callableParts = explode('#', $match['target']);
            $controllerName = $callableParts[0];
            $methodName = $callableParts[1];

            $controller = new $controllerName($router);
			
            call_user_func_array(array($controller, $methodName), $match['params']);
        }
		//404
        else {
            if(!empty(Config::route404())) {
                $controller = new Controller($router);
                $controller->routedirect('artread/', ['art' => Config::route404()]);
            } else {
                header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
            }
        }
    }
}