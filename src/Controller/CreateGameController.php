<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\GameCreateType;
use App\Entity\Game;
use App\Service\GameCookie;
use App\Entity\GameCookiePayload;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CreateGameController extends AbstractController

{

    /**
     *
     * @Route("/", name="create_game")
     */
    public function create(Request $request)
    {
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

            $cookieService = new GameCookie($request->cookies);
            $cookieService->addGame($cookiePayload);

            //TODO On créé le cookie mercure


            // On redirige vers la salle d'attente
            $response = new RedirectResponse($this->generateUrl('join_game', [
                'hashGame' => $oGame->getHash()
            ]));
            $response->headers->setCookie($cookieService->generateCookie());
            $response->sendHeaders();
            return $response;
        } else {
            return $this->render('create_game/index.html.twig', [
                'create_form' => $form->createView()
            ]);
        }
    }
}
