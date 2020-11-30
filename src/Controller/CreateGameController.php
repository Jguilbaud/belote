<?php
namespace App\Controller;

use App\Entity\Game;
use App\Entity\GameCookiePayload;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\GameCreateType;
use App\Service\Cookie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CreateGameController extends AbstractController

{

    /**
     *
     * @Route("/", name="create_game")
     */
    public function create(Request $request)
    {
        $cookieService = new Cookie($request->cookies);

        $form = $this->createForm(GameCreateType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formValues = $form->getData();

            $oGame = new Game();
            $oGame->setHash(substr(md5(random_bytes(10)), 0, 10));
            $oGame->setNameNorth($formValues['firstPlayerName']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($oGame);
            $entityManager->flush();

            // On crée les cookies de session
            $cookiePayload = new GameCookiePayload();
            $cookiePayload->setHashGame($oGame->getHash());
            $cookiePayload->setPlayerPosition('n');

            $cookieService->addGame($cookiePayload);

            // TODO On créé le cookie mercure

            // On redirige vers la salle d'attente
            $response = new RedirectResponse($this->generateUrl('join_game', [
                'hashGame' => $oGame->getHash()
            ]));
            $response->headers->setCookie($cookieService->generateGameCookie());
            $response->sendHeaders();
            return $response;
        } else {

            // On récupère la liste des Jeux
            $rGame = $this->getDoctrine()->getRepository(Game::class);
            $hashAndPositionList = $cookieService->getGamesHashList();

            return $this->render('create_game/index.html.twig', [
                'recentgames' => $rGame->findBy([
                    'hash' => $hashAndPositionList
                ]),
                'create_form' => $form->createView()
            ]);
        }
    }
}
