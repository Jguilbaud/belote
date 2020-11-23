<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\GameRepository;
use App\Service\GameCookie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Validator\PlayerPosition as PlayerPositionConstraint;
use Symfony\Component\Validator\Constraints\Regex as RegexConstraint;
use Symfony\Component\Validator\Constraints\NotNull as NotNullConstraint;

class JoinGameController extends AbstractController
{

    /**
     *
     * @Route("/join/game/{hashGame}", name="join_game")
     */
    public function index(Request $request, String $hashGame, GameRepository $repoGame, TranslatorInterface $translator)
    {
        // On récupère le cookie de l'utilisateur
        $cookieService = new GameCookie($request->cookies);

        // On récupère la partie
        $oGame = $repoGame->findOneBy([
            'hash' => $hashGame
        ]);

        if ($oGame === null) {
            return $this->render('join_game/error.html.twig', [
                'errorMessage' => $translator->trans('join.error.unknowngame', [
                    '{hash}' => $hashGame
                ])
            ]);
        }

        $tplVars = [
            'hashGame' => $hashGame,
            'playerNameNorth' => $oGame->getNameNorth(),
            'playerNameEast' => $oGame->getNameEast() ?? '',
            'playerNameSouth' => $oGame->getNameSouth() ?? '',
            'playerNameWest' => $oGame->getNameWest() ?? '',
            'html_disabled_pseudo' => $cookieService->getPlayerPosition($hashGame) == null ? '' : 'disabled="disabled"',
            'html_disabled' => $cookieService->getPlayerPosition($hashGame) == null ? '' : 'disabled="disabled"',
            'html_disabled_s' => $oGame->getNameSouth() == null ? '' : 'disabled="disabled"',
            'html_disabled_w' => $oGame->getNameWest() == null ? '' : 'disabled="disabled"',
            'html_disabled_e' => $oGame->getNameEast() == null ? '' : 'disabled="disabled"',
            'currentPlayerName' => $cookieService->getPlayerPosition($hashGame) == null ? '' : $oGame->getNameNorth()
        ];

        if ($cookieService->getPlayerPosition($hashGame) == 'n') {
            $tplVars['html_disabled_s'] = 'disabled="disabled"';
            $tplVars['html_disabled_w'] = 'disabled="disabled"';
            $tplVars['html_disabled_e'] = 'disabled="disabled"';
        }

        return $this->render('join_game/index.html.twig', $tplVars);
    }

    /**
     *
     * @Route("/ws/join/game/{hashGame}", name="ws_join_game")
     */
    public function join(Request $request, String $hashGame, ValidatorInterface $validator, GameRepository $repoGame, TranslatorInterface $translator)
    {
        $paramsErrors = array();
        $playerName = $request->request->get('pseudo');
        $errors = $validator->validate($playerName, [
            new NotNullConstraint(),
            new RegexConstraint('/^([A-Za-z0-9-_]){1,30}$/')
        ]);
        if (count($errors) > 0) {
            $paramsErrors['playerPosition'] = $errors[0]->getMessage();
        }

        $playerPosition = $request->request->get('playerPosition');
        $errors = $validator->validate($playerPosition, [
            new NotNullConstraint(),
            new PlayerPositionConstraint()
        ]);
        if (count($errors) > 0) {
            echo "ko";
            // this is *not* a valid email address
            $paramsErrors['playerPosition'] = $errors[0]->getMessage();
        }

        if (count($paramsErrors) > 0) {
            return $this->json([
                'response' => 'ko',
                'errors' => $paramsErrors
            ], 400);
        }

        // On récupère la partie
        $oGame = $repoGame->findOneBy([
            'hash' => $hashGame
        ]);

        if ($oGame === null) {
            return $this->json([
                'response' => 'ko',
                'errors' => $translator->trans('join.error.unknowngame', [
                    '{hash}' => $hashGame
                ], 404)
            ]);
        }


        // On vérifie que la place demandée est bien dispo
        // TODO

        // On enregistre le joueur à la place demandée
        // TODO

        // On positionne les cookies
        // TODO

        // On envoie la notification mercure aux autres joueurs
        // TODO

        // On enregistre dnas la bdd
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($oGame);
        $entityManager->flush();

        return $this->json([
            'response' => 'ok',
            'playerName' => $playerName,
            'position' => $playerPosition
        ]);
    }
}
