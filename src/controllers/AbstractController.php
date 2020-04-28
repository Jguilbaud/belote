<?php

namespace Controllers;

abstract class AbstractController {
    /**
     *
     * @var \Twig\Environment
     */
    protected ?\Twig\Environment $tplEngine = null;
    protected String $tplName = 'index.html';
    protected array $tplVars = array();

    public function __construct() {

        // init twig
        $loader = new \Twig\Loader\FilesystemLoader(VIEWS_PATH);

        $this->tplEngine = new \Twig\Environment($loader, array(
            // 'cache' => TMP_PATH . 'cache',
            'debug' => \Conf::DEBUG
        ));

        $this->tplVars['BASE_URL'] = \Conf::BASE_URL;
        $this->tplVars['MERCURE_URL'] = \Conf::MERCURE_URL;
    }

    protected function renderPage() {
        echo $this->tplEngine->render($this->tplName, $this->tplVars);
    }
}