<?php

namespace Services;

class HttpRouter {
    private \stdClass $routes;

    public function __construct(String $pagesConfFilePath) {
        $jsonRoutes = file_get_contents($pagesConfFilePath);
        $this->routes = json_decode($jsonRoutes, false, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Répond à la requete demandée
     */
    public function doResponse() {

        // On détermine si c'est une requete GET ou POST
        $method = strtolower($_SERVER['REQUEST_METHOD']);

        // On récupère l'URI appelée (transformée par l'url rewriting)
        if (isset($_GET['req'])) {
            $tReq = '/' . $_GET['req'];
        } else {
            $tReq = '/';
        }

        // On recherche notre route
        foreach ( $this->routes as $routeDef ) {

            // On vérifie que c'est la bonne méthode
            if ($method == $routeDef->http_method) {
                $tMatches = array();

                if (preg_match('#^' . $routeDef->regexp . '$#is', $tReq, $tMatches)) {
                    $params = $this->prepareParameters($routeDef->params,$tMatches);

                    switch ($routeDef->type) {
                        case 'api' :
                            return $this->doApi($routeDef->controller, $routeDef->method, $params);
                        case 'page' :
                        default :
                            return $this->showPage($routeDef->controller, $routeDef->method, $params);
                    }
                }
            }
        }

        // Cas page non trouvée => 404
        $this->showPage('Error','show404Page',$this->prepareParameters(array()));
    }

    private function prepareParameters($routeParametersConf, $uriMatches = array()) {
        $params = array();
        foreach ( $routeParametersConf as $paramName => $paramConf ) {

            switch ($paramConf->method) {
                case 'get' :
                    $params[] = $_GET[$paramName] ?? null;
                    break;
                case 'post' :
                    $params[] =$_POST[$paramName] ?? null;
                    break;
                case 'cookie' :
                    $params[] = $_COOKIE[$paramName] ?? null;
                    break;
                case 'uri' :
                    $params[] = $uriMatches[$paramConf->uri_position] ?? null; // TODO vérifier index entre conf et indexs du matche de preg amtch
                    break;
            }
        }

        // on empêche l'utilisation directe de $_POST et $_GET
        $_POST = array();
        $_GET = array();
        $_REQUEST = array();

        return $params;
    }

    private function showPage($controllerName, $methodName, $params) {
        // On prépare l'appel dynamique
        $controllerName = 'Controllers\\' . $controllerName;

        // On réalise l'appel dynamique
        $controller = new $controllerName();
        $controller->$methodName(...$params);
    }

    private function doApi($controllerName, $methodName, $params) {
        // On prépare l'appel dynamique
        $controllerName = 'Controllers\\' . $controllerName;

        // On réalise l'appel dynamique
        $controller = new $controllerName();
        echo json_encode($controller->$methodName(...$params));
    }
}