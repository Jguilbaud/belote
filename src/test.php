<?php
require_once 'conf/constants.inc.php';
require_once 'conf/conf.inc.php';
require_once 'conf/autoload.inc.php';

// $oTurn = \Repositories\DbTurn::get()->findOneById(1);
// try {
//     $winner = \Services\Game::get()->calculateTurnWinner($oTurn);
//     echo '# Le tour est terminé' . "\n";
//     echo '- Le vainqueur est : ' . $winner . "\n";
// } catch ( \Exceptions\TurnIsIncomplete $e ) {
//     echo 'Le tour n\'est pas fini' . "\n";
// }

// exit;

echo '###################' . "\n";
echo '## Création partie ' . "\n";
echo '###################' . "\n";
$idGame = \Services\Game::get()->create();
echo '- Partie créée : ' . $idGame . "\n";

try {
    while ( true ) {
        $oRound = \Services\Game::get()->startNewRound($idGame);
        echo ' ###################' . "\n";
        echo ' ## Démarrage manche N°' . $oRound->getNum_manche() . "\n";
        echo ' ###################' . "\n";
        echo '-- donneur : ' . $oRound->getDonneur() . "\n";
        echo '  ###################' . "\n";
        echo '  ## Coupe deck par ' . \Services\Game::get()->getPrecedentPlayerFromOne($oRound->getNum_manche()) . "\n";
        echo '  ###################' . "\n";
        $cut = 0;
        while ( $cut <= 0 || $cut >= 32 ) {
            echo '  => Couper à : (valeur entre 1 et 31) ? ' . "\n";
            echo '? ';
            $cut = trim(fgets(STDIN));
        }
        \Services\Game::get()->cutDeck($idGame, $cut);
        echo '  - Coupe faite' . "\n";

        echo '  ###################' . "\n";
        echo '  ## Distribution des cartes automatique ' . "\n";
        echo '  ###################' . "\n";
        $proposedTrumpCard = \Services\Game::get()->dealCards($oRound->getId());

        echo '  ###################' . "\n";
        echo '  ## Atout proposé : ' . $proposedTrumpCard . "\n";
        echo '  ###################' . "\n";

        echo '  ###################' . "\n";
        echo '  ## Prise d\'atout : ' . "\n";
        echo '  ###################' . "\n";

        $player = '';
        while ( !in_array($player, array(
            'N',
            'S',
            'E',
            'O'
        )) ) {
            echo '  => Qui prend l\'atout ? (N, E, S ou O) ' . "\n";
            echo '? ';
            $player = trim(fgets(STDIN));
        }

        $color = '';
        while ( !in_array($color, array(
            'H',
            'D',
            'C',
            'S'
        )) ) {
            echo '  => Quelle couleur ? (Coeur : H, Carreau : D, Trèfle : C ou Pique : S) ' . "\n";
            echo '? ';
            $color = trim(fgets(STDIN));
        }
        \Services\Game::get()->takeTrumpAndDeal($oRound->getId(), $color, $player);

        $currentPlayer = \Services\Game::get()->getNextPlayerFromOne($oRound->getDonneur());

        try {
            while ( true ) {
                $oTurn = \Services\Game::get()->startNewTurn($oRound->getId(), $currentPlayer);
                echo '  ###################' . "\n";
                echo '  ## Démarrage tour N°' . $oTurn->getNum_tour() . "\n";
                echo '  ###################' . "\n";

                // Pour chaque joueur
                for($i = 0; $i < 4; $i++) {
                    echo '- C\'est à ' . $currentPlayer . ' de jouer.' . "\n";
                    $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oRound->getId(), $currentPlayer);
                    echo ' Main  du joueur  : ' . print_r($oHand->getCartes(), true) . "\n";
                    echo '- Choisir une carte : (exemple hD)' . "\n";
                    $card = trim(fgets(STDIN));
                    try {
                        echo '- Carte jouée : ' . $card . "\n";
                        $oTurn = \Services\Game::get()->playCard($oTurn->getId(), $currentPlayer, $card);
                    } catch ( Exception $e ) {
                        echo $e->getMessage();
                        echo '- Carte jouée interdite (non possédée ou déjà jouée) - ' . get_class($e) . "\n";
                    }
                    $currentPlayer = \Services\Game::get()->getNextPlayerFromOne($currentPlayer);
                }

                // On regarde si le tour est complet
                try {
                    $winner = \Services\Game::get()->calculateTurnWinner($oTurn);
                    echo '  ###################' . "\n";
                    echo '  # Le tour est terminé' . "\n";
                    echo '  ###################' . "\n";
                    echo '- Le pli est remporté par : ' . $winner . "\n";
                    $currentPlayer = $winner;
                } catch ( \Exceptions\TurnIsIncomplete $e ) {
                    echo 'Le tour n\'est pas fini' . "\n";
                }
            }
        } catch ( \Exceptions\TurnNumberOutofBound $e ) {
            echo ' ###################' . "\n";
            echo ' ## La manche est terminée' . "\n";
            echo ' ###################' . "\n";
            $oRound = \Services\Game::get()->closeRound($oRound->getId());
            $oGame = \Repositories\DbGame::get()->findOneById($idGame);

            echo ' - Points NS : ' . $oRound->getPoints_ns() . '(Total : ' . $oGame->getTotal_points_ns() . ')' . "\n";
            echo ' - Points OE : ' . $oRound->getPoints_oe() . '(Total : ' . $oGame->getTotal_points_oe() . ')' . "\n";
        }
    }
} catch ( \Exceptions\GameIsFinished $e ) {
    echo '###################' . "\n";
    echo '## La partie est terminée' . "\n";
    echo '###################' . "\n";
    $oGame = \Repositories\DbGame::get()->findOneById($idGame);
    echo ' - Points NS : ' . $oGame->getTotal_points_NS() . "\n";
    echo ' - Points OE : ' . $oGame->getTotal_points_OE() . "\n";
}
exit();


// ###########################
// affichage mains
// ###########################
/*
 * $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oRound->getId(), 'N');
 * echo ' Main finale du joueur Nord : ' . print_r($oHand->getCartes(), true) . "\n";
 *
 * $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oRound->getId(), 'E');
 * echo ' Main finale du joueur Est : ' . print_r($oHand->getCartes(), true) . "\n";
 *
 * $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oRound->getId(), 'S');
 * echo ' Main finale du joueur Sud : ' . print_r($oHand->getCartes(), true) . "\n";
 *
 * $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oRound->getId(), 'O');
 * echo ' Main finale du joueur Ouest : ' . print_r($oHand->getCartes(), true) . "\n";
 */
