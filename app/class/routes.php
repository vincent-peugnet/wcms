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
        $router->addRoutes([
            ['GET|POST', '/', 'Backrouter#run', 'backrouter'],
            ['GET', '/[a:art]/', 'Controllerart#read', 'artread/'],
            ['GET', '/[a:art]', 'Controllerart#read', 'artread'],
            ['GET', '/[a:art]/add', 'Controllerart#add', 'artadd'],
            ['GET', '/[a:art]/edit', 'Controllerart#edit', 'artedit'],
            ['GET', '/[a:art]/log', 'Controllerart#log', 'artlog'],
            ['POST', '/[a:art]/edit', 'Controllerart#update', 'artupdate'],
            ['GET', '/[a:art]/delete', 'Controllerart#confirmdelete', 'artconfirmdelete'],
            ['POST', '/[a:art]/delete', 'Controllerart#delete', 'artdelete'],
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
            header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
        }
    }
}