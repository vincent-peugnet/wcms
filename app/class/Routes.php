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
    public function match(): void
    {
        $router = new AltoRouter();
        if (!empty(Config::basepath())) {
            $router->setBasePath('/' . Config::basepath());
        }
        $router->addMatchTypes(array('noslash' => '[^/]+'));
        $router->addMatchTypes(array('cid' => Model::ID_REGEX));
        $router->addRoutes([
            ['GET', '/api/v0/page/[cid:page]', 'Controllerapipage#get', 'apipageget'],
            ['GET', '/api/v0/pages/list', 'Controllerapipage#list', 'apipagelist'],
            ['POST', '/api/v0/pages/query', 'Controllerapipage#query', 'apipagequery'],
            ['POST', '/api/v0/page/[cid:page]/update', 'Controllerapipage#update', 'apipageupdate'],
            ['POST', '/api/v0/page/[cid:page]/add', 'Controllerapipage#add', 'apipageadd'],
            ['DELETE', '/api/v0/page/[cid:page]', 'Controllerapipage#delete', 'apipagedelete'],
            ['PUT', '/api/v0/page/[cid:page]', 'Controllerapipage#put', 'apipageput'],
            ['GET', '/api/v0/user/[cid:user]', 'Controllerapiuser#get', 'apiuserget'],
            ['POST', '/api/v0/auth', 'Controllerapiconnect#auth', 'apiconnectauth'],
            ['GET', '/api/v0/health', 'Controllerapiconnect#health', 'apiconnecthealth'],
            ['POST', '/api/v0/media/upload/[*:path]', 'Controllerapimedia#upload', 'apimediaupload'],
            ['DELETE', '/api/v0/media/[*:path]', 'Controllerapimedia#delete', 'apimediadelete'],
            ['POST', '/api/v0/workspace', 'Controllerapiworkspace#update', 'apiworkspaceupdate'],
            ['GET', '/', 'Controllerhome#desktop', 'home'],
            ['POST', '/', 'Controllerhome#desktop', 'homequery'],
            ['POST', '/columns', 'Controllerhome#columns', 'homecolumns'],
            ['POST', '/colors', 'Controllerhome#colors', 'homecolors'],
            ['POST', '/bookmark/add', 'Controllerbookmark#add', 'bookmarkadd'],
            ['POST', '/bookmark/update', 'Controllerbookmark#update', 'bookmarkupdate'],
            ['POST', '/bookmark/delete', 'Controllerbookmark#delete', 'bookmarkdelete'],
            ['GET', '/!bookmark/[cid:bookmark]/publish', 'Controllerbookmark#publish', 'bookmarkpublish'],
            ['GET', '/!bookmark/[cid:bookmark]/unpublish', 'Controllerbookmark#unpublish', 'bookmarkunpublish'],
            ['GET', '/!random', 'Controllerrandom#direct', 'randomdirect'],
            ['GET', '/!flushrendercache', 'Controllerhome#flushrendercache', 'flushrendercache'],
            ['GET', '/!flushurlcache', 'Controllerhome#flushurlcache', 'flushurlcache'],
            ['GET', '/!cleanurlcache', 'Controllerhome#cleanurlcache', 'cleanurlcache'],
            ['POST', '/multi', 'Controllerhome#multi', 'multi'],
            ['POST', '/upload', 'Controllerpage#upload', 'pageupload'],
            ['POST', '/!co', 'Controllerconnect#log', 'log'],
            ['GET', '/!co', 'Controllerconnect#connect', 'connect'],
            ['POST', '/!search', 'Controllerhome#search', 'search'],
            ['GET', '/!media', 'Controllermedia#desktop', 'media'],
            ['GET', '/!media/fontface', 'Controllermedia#fontface', 'mediafontface'],
            ['POST', '/!media', 'Controllermedia#desktop', 'mediaquery'],
            ['POST', '/!media/upload', 'Controllermedia#upload', 'mediaupload'],
            ['POST', '/!media/urlupload', 'Controllermedia#urlupload', 'mediaurlupload'],
            ['POST', '/!media/folderadd', 'Controllermedia#folderadd', 'mediafolderadd'],
            ['POST', '/!media/folderdelete', 'Controllermedia#folderdelete', 'mediafolderdelete'],
            ['POST', '/!media/edit', 'Controllermedia#edit', 'mediaedit'],
            ['POST', '/!media/rename', 'Controllermedia#rename', 'mediarename'],
            ['GET', '/!admin', 'Controlleradmin#desktop', 'admin'],
            ['GET', '/!admin/log', 'Controlleradmin#log', 'adminlog'],
            ['POST', '/!admin', 'Controlleradmin#update', 'adminupdate'],
            ['POST', '/!admin/database', 'Controlleradmin#database', 'admindatabase'],
            ['GET', '/!user', 'Controlleruser#desktop', 'user'],
            ['POST', '/!user/add', 'Controlleruser#add', 'useradd'],
            ['POST', '/!user/edit', 'Controlleruser#edit', 'useredit'],
            ['GET', '/!profile', 'Controllerprofile#desktop', 'profile'],
            ['POST', '/!profile', 'Controllerprofile#update', 'profileupdate'],
            ['POST', '/!profile/password', 'Controllerprofile#password', 'profilepassword'],
            ['GET', '/!profile/deletesessions', 'Controllerprofile#deletesessions', 'profiledeletesessions'],
            ['GET', '/!info', 'Controllerinfo#desktop', 'info'],
            ['GET', '/[noslash:page]/', 'Controllerpage#pagepermanentredirect', 'pageread/'],
            ['HEAD', '/[cid:page]/', 'Controllerpage#pagepermanentredirect', 'pageread/head'],
            ['POST', '/[cid:page]', 'Controllerpage#read', 'pagereadpost'], /** Used for password protected pages */
            ['GET', '/[noslash:page]', 'Controllerpage#read', 'pageread'],
            ['HEAD', '/[cid:page]', 'Controllerpage#readhead', 'pagereadhead'],
            ['GET', '/[noslash:page]/add', 'Controllerpage#add', 'pageadd'],
            ['GET', '/[noslash:page]/add:[cid:copy]', 'Controllerpage#addascopy', 'pageaddascopy'],
            ['GET', '/[cid:page]/edit', 'Controllerpage#edit', 'pageedit'],
            ['GET', '/[cid:page]/render', 'Controllerpage#render', 'pagerender'],
            ['GET', '/[cid:page]/log', 'Controllerpage#log', 'pagelog'],
            ['GET', '/[cid:page]/download', 'Controllerpage#download', 'pagedownload'],
            ['GET', '/[cid:page]/logout', 'Controllerpage#logout', 'pagelogout'],
            ['GET', '/[cid:page]/login', 'Controllerpage#login', 'pagelogin'],
            ['POST', '/[cid:page]/edit', 'Controllerpage#update', 'pageupdate'],
            ['POST', '/workspace/update', 'Controllerworkspace#update', 'workspaceupdate'],
            ['GET', '/[cid:page]/delete', 'Controllerpage#delete', 'pagedelete'],
            ['POST', '/[cid:page]/delete', 'Controllerpage#confirmdelete', 'pageconfirmdelete'],
            ['GET', '/[cid:page]/duplicate:[noslash:duplicate]', 'Controllerpage#duplicate', 'pageduplicate'],
            ['GET', '/[cid:page]/[*:command]', 'Controllerpage#commandnotfound', 'pageread/etoile'],
        ]);

        $match = $router->match();
        if ($match) {
            $callableParts = explode('#', $match['target']);
            $controllerName = '\\Wcms\\' . $callableParts[0];
            $methodName = $callableParts[1];

            $controller = new $controllerName($router);

            call_user_func_array(array($controller, $methodName), $match['params']);
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
                http_response_code(405);
            } else {
                header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
            }
        }
    }
}
