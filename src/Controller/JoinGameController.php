<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class JoinGameController extends AbstractController
{
    /**
     * @Route("/join/game", name="join_game")
     */
    public function index()
    {
        return $this->render('join_game/index.html.twig', [
            'controller_name' => 'JoinGameController',
        ]);
    }
}
