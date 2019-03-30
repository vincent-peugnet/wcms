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
            $router->setBasePath('/' . Config::basepath());
        }
        $router->addMatchTypes(array('cid' => '[a-zA-Z0-9-_+,\'!%@&.$€=\(\|\)]+'));
        $router->addRoutes([
            ['GET', '/', 'Controllerhome#desktop', 'home'],
            ['POST', '/columns', 'Controllerhome#columns', 'homecolumns'],
            ['POST', '/upload', 'Controllerart#upload', 'artupload'],
            ['POST', '/!co', 'Controllerconnect#log', 'log'],
            ['GET', '/!co', 'Controllerconnect#connect', 'connect'],
            ['POST', '/!search', 'Controllerhome#search', 'search'],
            ['GET', '/!media', 'Controllermedia#desktop', 'media'],
            ['POST', '/!media/upload', 'Controllermedia#upload', 'mediaupload'],
            ['POST', '/!media/folder', 'Controllermedia#folder', 'mediafolder'],
            ['GET', '/!font', 'Controllerfont#desktop', 'font'],
            ['GET', '/!font/render', 'Controllerfont#render', 'fontrender'],
            ['POST', '/!font/add', 'Controllerfont#add', 'fontadd'],
            ['POST', '/!admin', 'Controlleradmin#update', 'adminupdate'],
            ['GET', '/!admin', 'Controlleradmin#desktop', 'admin'],
            ['GET', '/!user', 'Controlleruser#desktop', 'user'],
            ['POST', '/!user/add', 'Controlleruser#add', 'useradd'],
            ['POST', '/!user/update', 'Controlleruser#update', 'userupdate'],
            ['POST', '/!user/pref', 'Controlleruser#pref', 'userpref'],
            ['GET', '/!info', 'Controllerinfo#desktop', 'info'],
            ['GET', '/!timeline', 'Controllertimeline#desktop', 'timeline'],
            ['POST', '/!timeline/add', 'Controllertimeline#add', 'timelineadd'],
            ['POST', '/!timeline/clap', 'Controllertimeline#clap', 'timelineclap'],
            ['GET', '/[cid:art]/', 'Controllerart#read', 'artread/'],
            ['GET', '/[cid:art]', 'Controllerart#read', 'artread'],
            ['GET', '/[cid:art]/add', 'Controllerart#add', 'artadd'],
            ['GET', '/[cid:art]/edit', 'Controllerart#edit', 'artedit'],
            ['GET', '/[cid:art]/render', 'Controllerart#render', 'artrender'],
            ['GET', '/[cid:art]/log', 'Controllerart#log', 'artlog'],
            ['GET', '/[cid:art]/download', 'Controllerart#download', 'artdownload'],
            ['POST', '/[cid:art]/edit', 'Controllerart#update', 'artupdate'],
            ['POST', '/[cid:art]/editby', 'Controllerart#editby', 'arteditby'],
            ['POST', '/[cid:art]/removeeditby', 'Controllerart#removeeditby', 'artremoveeditby'],
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