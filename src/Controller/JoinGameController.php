<?php

namespace App\Controller;

use App\Entity\Game;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Repository\GameRepository;

class JoinGameController extends AbstractController
{
    /**
     * @Route("/join/game/{hashGame}", name="join_game")
     */
    public function index(String $hashGame, GameRepository $repoGame, TranslatorInterface $translator)
    {

        //On récupère la partie
        $oGame = $repoGame->findOneBy(['hash' => $hashGame]);

        if($oGame === null){
            return $this->render('join_game/error.html.twig', ['errorMessage' => $translator->trans('join.error.unknowngame',['{hash}' => $hashGame])]);
        }

        $tplVars = [
            'hashGame' => $hashGame,
            'playerNameNorth' => $oGame->getNameNorth(),
            'playerNameEast' => $oGame->getNameEast() ?? '',
            'playerNameSouth' => $oGame->getNameSouth() ?? '',
            'playerNameWest' => $oGame->getNameWest() ?? '',
            'html_disabled_pseudo' => '',
            'html_disabled' => '',
            'html_disabled_s' => $oGame->getNameSouth() == null ? '' : 'disabled="disabled"',
            'html_disabled_w' => $oGame->getNameWest() == null ? '' : 'disabled="disabled"',
            'html_disabled_e' => $oGame->getNameEast() == null ? '' : 'disabled="disabled"',
            'currentPlayerName' => '',
        ];



        return $this->render('join_game/index.html.twig', $tplVars);
    }

    /**
     *
     */
    public function join(){

    }
}
