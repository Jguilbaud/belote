<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\GameCreateType;
use App\Entity\Game;
use App\Repository\GameRepository;

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
            $oGame->setHash(substr(md5(random_bytes(10)),0,10));
            $oGame->setNameNorth($formValues['firstPlayerName']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($oGame);
            $entityManager->flush();

            echo $oGame->getId();

            return $this->render('create_game/index.html.twig', [
                'create_form' => $form->createView()
            ]);

        } else {
            return $this->render('create_game/index.html.twig', [
                'create_form' => $form->createView()
            ]);
        }
    }
}
