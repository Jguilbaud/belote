<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Repository\GameRepository;
use App\Service\GameCookie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;

class JoinGameController extends AbstractController
{
    /**
     * @Route("/join/game/{hashGame}", name="join_game")
     */
    public function index(Request $request, String $hashGame, GameRepository $repoGame, TranslatorInterface $translator)
    {
        //On récupère le cookie de l'utilisateur
        $cookieService = new GameCookie($request->cookies);

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
            'html_disabled_pseudo' => $cookieService->getPlayerPosition($hashGame) == null ?'' : 'disabled="disabled"',
            'html_disabled' => $cookieService->getPlayerPosition($hashGame) == null ?'' : 'disabled="disabled"',
            'html_disabled_s' => $oGame->getNameSouth() == null ? '' : 'disabled="disabled"',
            'html_disabled_w' => $oGame->getNameWest() == null ? '' : 'disabled="disabled"',
            'html_disabled_e' => $oGame->getNameEast() == null ? '' : 'disabled="disabled"',
            'currentPlayerName' => $cookieService->getPlayerPosition($hashGame) == null ?'' : $oGame->getNameNorth(),
        ];

        if($cookieService->getPlayerPosition($hashGame) == 'n'){
            $tplVars['html_disabled_s'] = 'disabled="disabled"';
            $tplVars['html_disabled_w'] = 'disabled="disabled"';
            $tplVars['html_disabled_e'] = 'disabled="disabled"';
        }




        return $this->render('join_game/index.html.twig', $tplVars);
    }

    /**
     *
     */
    public function join(){

    }
}
