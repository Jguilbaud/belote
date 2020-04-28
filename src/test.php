<?php
require_once 'conf/constants.inc.php';
require_once 'conf/conf.inc.php';
require_once 'conf/autoload.inc.php';

echo '###################' . "\n";
echo '## Création partie ' . "\n";
echo '###################' . "\n";
$oGame = null;
$idGame = \Services\Game::get()->create($oGame);
echo '- Partie créée : ' . $idGame . "\n";

while ( true ) {
    $oRound = \Services\Game::get()->startNewRound($oGame);
    echo ' ###################' . "\n";
    echo ' ## Démarrage manche N°' . $oRound->getNum_round() . "\n";
    echo ' ###################' . "\n";
    echo '-- donneur : ' . $oRound->getDealer() . "\n";
    echo '  ###################' . "\n";
    echo '  ## Coupe deck par ' . \Services\Utils::getPrecedentPlayerFromOne($oRound->getNum_round()) . "\n";
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
    $proposedTrumpCard = \Services\Game::get()->dealCardsBeforeTrumpChoose($oGame, $oRound);

    echo '  ###################' . "\n";
    echo '  ## Atout proposé : ' . $proposedTrumpCard . "\n";
    echo '  ###################' . "\n";

    echo '  ###################' . "\n";
    echo '  ## Prise d\'atout : ' . "\n";
    echo '  ###################' . "\n";

    $player = '';
    while ( !in_array($player, array_keys(\PLAYERS)) ) {
        echo '  => Qui prend l\'atout ? (n, e, s ou w) ' . "\n";
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
    \Services\Game::get()->chooseTrumpAndDeal($oGame, $oRound, $color, $player);

    $currentPlayer = \Services\Utils::getNextPlayerFromOne($oRound->getDealer());

    try {
        $oTurn = null;
        while ( true ) {
            $oTurn = \Services\Game::get()->startNewTurn($oRound, $oTurn);
            echo '  ###################' . "\n";
            echo '  ## Démarrage tour N°' . $oTurn->getNum_turn() . "\n";
            echo '  ###################' . "\n";

            // Pour chaque joueur
            for($i = 0; $i < 4; $i++) {
                echo '- C\'est à ' . $currentPlayer . ' de jouer.' . "\n";
                $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oRound->getId(), $currentPlayer);
                echo ' Main  du joueur  : ' . print_r($oHand->getCards(), true) . "\n";
                echo '- Choisir une carte : (exemple hD)' . "\n";
                $card = trim(fgets(STDIN));
                try {
                    echo '- Carte jouée : ' . $card . "\n";
                    $oTurn = \Services\Game::get()->playCard($oTurn->getId(), $currentPlayer, $card);
                } catch ( Exception $e ) {
                    echo $e->getMessage();
                    echo '- Carte jouée interdite (non possédée ou déjà jouée) - ' . get_class($e) . "\n";
                }
                $currentPlayer = \Services\Utils::getNextPlayerFromOne($currentPlayer);
            }

            // On regarde si le tour est complet
            try {
                $winner = \Services\Game::get()->calculateTurnWinner($oRound, $oTurn);
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
        $oGame = \Repositories\DbGame::get()->findOneById($idGame);
        if (\Services\Game::get()->closeRound($oGame, $oRound)) {
            echo '###################' . "\n";
            echo '## La partie est terminée' . "\n";
            echo '###################' . "\n";
            $oGame = \Repositories\DbGame::get()->findOneById($idGame);
            echo ' - Points NS : ' . $oGame->getTotal_points_NS() . "\n";
            echo ' - Points OE : ' . $oGame->getTotal_points_we() . "\n";
            exit();
        }

        echo ' - Points NS : ' . $oRound->getPoints_ns() . '(Total : ' . $oGame->getTotal_points_ns() . ')' . "\n";
        echo ' - Points OE : ' . $oRound->getPoints_we() . '(Total : ' . $oGame->getTotal_points_we() . ')' . "\n";
    }
}

exit();


// ###########################
// affichage mains
// ###########################
/*
 * $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oRound->getId(), 'N');
 * echo ' Main finale du joueur Nord : ' . print_r($oHand->getCards(), true) . "\n";
 *
 * $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oRound->getId(), 'E');
 * echo ' Main finale du joueur Est : ' . print_r($oHand->getCards(), true) . "\n";
 *
 * $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oRound->getId(), 'S');
 * echo ' Main finale du joueur Sud : ' . print_r($oHand->getCards(), true) . "\n";
 *
 * $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oRound->getId(), 'O');
 * echo ' Main finale du joueur Ouest : ' . print_r($oHand->getCards(), true) . "\n";
 */
