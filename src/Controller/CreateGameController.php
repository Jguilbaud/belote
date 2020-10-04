<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CreateGameController extends AbstractController
{
    /**
     * @Route("/", name="create_game")
     */
    public function index()
    {
        return $this->render('create_game/index.html.twig', [
            'controller_name' => 'CreateGameController',
        ]);
    }
}
