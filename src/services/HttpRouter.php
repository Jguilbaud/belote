<?php

namespace Services;

class HttpRouter {
    private array $routes = array();

    public function __construct(String $pagesConfFilePath) {
        $jsonRoutes = file_get_contents($pagesConfFilePath);
        $this->routes = json_decode($jsonRoutes);
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
            if($method == $routeDef->http_method){
                $tMatches = array();

                if (preg_match('#^' . $routeDef->regexp . '$#is', $tReq, $tMatches)) {
                    $params = prepareParameters($routeDef->params);
                    switch($routeDef->type){
                        case 'api':
                            $this->doApi($routeDef->controller,$routeDef->method,$params);
                            break;
                        case 'page':
                        default:
                            $this->showPage($routeDef->controller,$routeDef->method,$params);
                            break;
                    }
                    // On ne va pas plus loin dans la boucle - ne devrait pas être nécessaire si pas de conflit dans la conf des routes
                    break;
                }
            }
        }


    }

    private function prepareParameters($routeParametersConf){
        $params = array();

        foreach($routeParametersConf as $method => $paramConf){



        }

        // on empêche l'utilisation directe de $_POST et $_GET
        $_POST = array();
        $_GET = array();
        $_REQUEST = array();

        return $params;
    }

    private function showPage($controllerName,$methodName,$params){

    }

    private function doApi($controllerName,$methodName,$params){

    }
}