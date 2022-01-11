<?php

namespace Wcms;

use AltoRouter;
use Exception;

class Routes
{
    /**
     * Cherche une correspondance entre l'URL et les routes, et appelle la méthode appropriée
     *
     * @throws Exception if addRoutes fails (maybe it should be catched).
     */
    public function match()
    {
        $router = new AltoRouter();
        if (!empty(Config::basepath())) {
            $router->setBasePath('/' . Config::basepath());
        }
        $router->addMatchTypes(array('cid' => '[^/]+'));
        $router->addRoutes([
            ['GET', '/', 'Controllerhome#desktop', 'home'],
            ['POST', '/', 'Controllerhome#desktop', 'homequery'],
            ['POST', '/columns', 'Controllerhome#columns', 'homecolumns'],
            ['POST', '/colors', 'Controllerhome#colors', 'homecolors'],
            ['GET', '//renderall', 'Controllerhome#renderall', 'homerenderall'],
            ['POST', '/multi', 'Controllerhome#multi', 'multi'],
            ['POST', '/upload', 'Controllerpage#upload', 'pageupload'],
            ['POST', '/!co', 'Controllerconnect#log', 'log'],
            ['GET', '/!co', 'Controllerconnect#connect', 'connect'],
            ['POST', '/!search', 'Controllerhome#search', 'search'],
            ['GET', '/!media', 'Controllermedia#desktop', 'media'],
            ['POST', '/!media/upload', 'Controllermedia#upload', 'mediaupload'],
            ['POST', '/!media/urlupload', 'Controllermedia#urlupload', 'mediaurlupload'],
            ['POST', '/!media/folderadd', 'Controllermedia#folderadd', 'mediafolderadd'],
            ['POST', '/!media/folderdelete', 'Controllermedia#folderdelete', 'mediafolderdelete'],
            ['POST', '/!media/edit', 'Controllermedia#edit', 'mediaedit'],
            ['POST', '/!media/rename', 'Controllermedia#rename', 'mediarename'],
            ['GET', '/!font', 'Controllerfont#desktop', 'font'],
            ['GET', '/!font/render', 'Controllerfont#render', 'fontrender'],
            ['POST', '/!font/add', 'Controllerfont#add', 'fontadd'],
            ['GET', '/!admin', 'Controlleradmin#desktop', 'admin'],
            ['POST', '/!admin', 'Controlleradmin#update', 'adminupdate'],
            ['POST', '/!admin/database', 'Controlleradmin#database', 'admindatabase'],
            ['GET', '/!user', 'Controlleruser#desktop', 'user'],
            ['POST', '/!user/add', 'Controlleruser#add', 'useradd'],
            ['POST', '/!user/update', 'Controlleruser#update', 'userupdate'],
            ['POST', '/!user/bookmark', 'Controlleruser#bookmark', 'userbookmark'],
            ['POST', '/!user/pref', 'Controlleruser#pref', 'userpref'],
            ['POST', '/!user/password', 'Controlleruser#password', 'userpassword'],
            ['POST', '/!user/token', 'Controlleruser#token', 'usertoken'],
            ['GET', '/!info', 'Controllerinfo#desktop', 'info'],
            ['GET', '/!timeline', 'Controllertimeline#desktop', 'timeline'],
            ['POST', '/!timeline/add', 'Controllertimeline#add', 'timelineadd'],
            ['POST', '/!timeline/clap', 'Controllertimeline#clap', 'timelineclap'],
            ['GET', '/[cid:page]/', 'Controllerpage#read', 'pageread/'],
            ['POST', '/[cid:page]/', 'Controllerpage#read', 'pageread/post'],
            ['GET', '/[cid:page]', 'Controllerpage#read', 'pageread'],
            ['GET', '/[cid:page]/add', 'Controllerpage#add', 'pageadd'],
            ['GET', '/[cid:page]/add:[cid:copy]', 'Controllerpage#addascopy', 'pageaddascopy'],
            ['GET', '/[cid:page]/edit', 'Controllerpage#edit', 'pageedit'],
            ['GET', '/[cid:page]/render', 'Controllerpage#render', 'pagerender'],
            ['GET', '/[cid:page]/log', 'Controllerpage#log', 'pagelog'],
            ['GET', '/[cid:page]/download', 'Controllerpage#download', 'pagedownload'],
            ['GET', '/[cid:page]/logout', 'Controllerpage#logout', 'pagelogout'],
            ['GET', '/[cid:page]/login', 'Controllerpage#login', 'pagelogin'],
            ['POST', '/[cid:page]/edit', 'Controllerpage#update', 'pageupdate'],
            ['POST', '/[cid:page]/editby', 'Controllerpage#editby', 'pageeditby'],
            ['POST', '/[cid:page]/removeeditby', 'Controllerpage#removeeditby', 'pageremoveeditby'],
            ['GET', '/[cid:page]/delete', 'Controllerpage#confirmdelete', 'pageconfirmdelete'],
            ['POST', '/[cid:page]/delete', 'Controllerpage#delete', 'pagedelete'],
            ['GET', '/[cid:page]/duplicate:[cid:duplicate]', 'Controllerpage#duplicate', 'pageduplicate'],
            ['GET', '/[cid:page]/[*]', 'Controllerpage#pagedirect', 'pageread/etoile'],
        ]);

        $match = $router->match();
        if ($match) {
            $callableParts = explode('#', $match['target']);
            $controllerName = '\\Wcms\\' . $callableParts[0];
            $methodName = $callableParts[1];

            $controller = new $controllerName($router);

            call_user_func_array(array($controller, $methodName), $match['params']);
        } else {
            header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
        }
    }
}
