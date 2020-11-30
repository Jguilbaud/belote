<?php
namespace App\Controller;

use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Validator\PlayerPosition as PlayerPositionConstraint;
use Symfony\Component\Validator\Constraints\Regex as RegexConstraint;
use Symfony\Component\Validator\Constraints\NotNull as NotNullConstraint;
use App\Entity\GameCookiePayload;
use Symfony\Component\Mercure\Update;
use App\Entity\GameEventMercurePayload;
use App\Service\Cookie;
use Symfony\Component\HttpFoundation\Response;

class JoinGameController extends AbstractController
{

    /**
     *
     * @Route("/join/game/{hashGame}", name="join_game")
     */
    public function index(Request $request, String $hashGame, GameRepository $repoGame, TranslatorInterface $translator): Response
    {
        // On récupère le cookie de l'utilisateur
        $cookieService = new Cookie($request->cookies, $repoGame);

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

        $response = $this->render('join_game/index.html.twig', $tplVars);
        // On positionne le cookie Mercure pour être au courant des arrivées de joueurs
        $response->headers->setCookie($cookieService->generateMercureCookie($this->getParameter('app.mercure.key')));
        return $response;
    }

    /**
     *
     * @Route("/ws/join/game/{hashGame}", name="ws_join_game")
     */
    public function join(Request $request, String $hashGame, ValidatorInterface $validator, GameRepository $repoGame, TranslatorInterface $translator, PublisherInterface $publisher): Response
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

        $playerPosition = strtolower($request->request->get('playerPosition'));
        $errors = $validator->validate($playerPosition, [
            new NotNullConstraint(),
            new PlayerPositionConstraint()
        ]);
        if (count($errors) > 0) {
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
                'response' => 'join.error.unknowngame',
                'errors' => $translator->trans('join.error.unknowngame', [
                    '{hash}' => $hashGame
                ])
            ], 400);
        }

        // On vérifie que la partie n'est pas démarrée (étape join)
        if ($oGame->getStep() !== Game::STEP_JOIN) {
            return $this->json([
                'response' => 'join.error.notjoinstep',
                'errors' => $translator->trans('join.error.notjoinstep')
            ], 400);
        }

        // On défini les méthodes d'accès selon la position demandée
        switch ($playerPosition) {
            case 'n':
                $methodNameGet = 'getNameNorth';
                $methodNameSet = 'setNameNorth';
                break;
            case 'e':
                $methodNameGet = 'getNameEast';
                $methodNameSet = 'setNameEast';
                break;
            case 's':
                $methodNameGet = 'getNameSouth';
                $methodNameSet = 'setNameSouth';
                break;
            case 'w':
                $methodNameGet = 'getNameWest';
                $methodNameSet = 'setNameWest';
                break;
        }

        // On vérifie que la place demandée est bien dispo
        if ($oGame->$methodNameGet() !== null) {
            return $this->json([
                'response' => 'join.error.positionalreadyused',
                'errors' => $translator->trans('join.error.positionalreadyused', [
                    '{playerName}' => $oGame->$methodNameGet()
                ])
            ], 400);
        }

        // On enregistre le joueur à la place demandée
        $oGame->$methodNameSet($playerName);

        // On positionne les cookies de jeu et mercure
        $cookiePayload = new GameCookiePayload();
        $cookiePayload->setHashGame($oGame->getHash());
        $cookiePayload->setPlayerPosition($playerPosition);

        $cookieService = new Cookie();
        $cookieService->addGame($cookiePayload);

        // On envoie la notification mercure aux autres joueurs
        $mercureEventPayload = new GameEventMercurePayload();
        $mercureEventPayload->setAction('playerjoin');
        $mercureEventPayload->addData('hashGame', $hashGame);
        $mercureEventPayload->addData('newPlayerPosition', $playerPosition);
        $mercureEventPayload->addData('newPlayerName', $playerName);

        $update = new Update('game/' . $hashGame, json_encode($mercureEventPayload), true);
        $publisher($update);

        // On enregistre dans la bdd
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($oGame);
        $entityManager->flush();

        // On prépare la réponse
        $response = $this->json([
            'response' => 'ok',
            'playerName' => $playerName,
            'position' => $playerPosition
        ]);
        // On ajoute les cookies à la réponse
        $response->headers->setCookie($cookieService->generateGameCookie());
        $response->headers->setCookie($cookieService->generateMercureCookie($this->getParameter('app.mercure.key')));
        return $response;
    }
}
