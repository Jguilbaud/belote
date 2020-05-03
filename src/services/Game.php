<?php

namespace Services;

class Game extends StaticAccessClass {

    /**
     * Pique : Spade
     * Coeur : Heart
     * Carreau : Diamond
     * Trèfles : Club
     *
     * @var array
     */
    public function create(?\Entities\Game &$oGame = null): int {
        $deck = DECK_CARDS_LIST;
        shuffle($deck);
        // On créé la partie en base
        if ($oGame == null) {
            $oGame = new \Entities\Game();
        }
        $oGame->setHash(Utils::generateHash(10));
        $oGame->setCards($deck);
        \Repositories\DbGame::get()->create($oGame);

        return $oGame->getId();
    }

    public function startNewRound(\Entities\Game &$oGame, ?\Entities\Round $oPrecedentRound = null): \Entities\Round {
        // On créé la nouvelle manche
        $oRound = new \Entities\Round();
        $oRound->setId_game($oGame->getId());

        // Cas première manche
        if ($oGame->getId_current_round() == 0 || $oPrecedentRound == null) {
            $dealer = array_keys(\PLAYERS)[rand(0, 3)];
            $newRound = 1;
        } else {
            // On récupère le précédent donneur et numéro de manche
            $newRound = $oPrecedentRound->getNum_round() + 1;
            $dealer = \Services\Utils::getNextPlayerFromOne($oPrecedentRound->getDealer());
        }

        $oRound->setDealer($dealer);
        $oRound->setNum_round($newRound);
        \Repositories\DbRound::get()->create($oRound);

        // On met à jour la partie
        $oGame->setStep(\Entities\Game::STEP_CUT_DECK);
        // Le joueur actif devient celui avant le donneur pour pouvoir couper le deck avant distribution
        $oGame->setCurrent_player(\Services\Utils::getPrecedentPlayerFromOne($dealer));
        $oGame->setId_current_round($oRound->getId());

        return $oRound;
    }

    public function startNewTurn(\Entities\Round &$oRound, ?\Entities\Turn $oPrecedentTurn = null): \Entities\Turn {
        $oTurn = new \Entities\Turn();
        $oTurn->setId_round($oRound->getId());

        // On calcule le numéro de tour
        if ($oRound->getId_current_turn() == 0 || $oPrecedentTurn == null) {
            $oTurn->setFirst_player(\Services\Utils::getNextPlayerFromOne($oRound->getDealer()));
            $newTurn = 1;
        } else {
            $newTurn = $oPrecedentTurn->getNum_turn() + 1;
            // On détecte si on essaye pas de créer une manche de trop
            if ($newTurn >= 9) {
                throw new \Exceptions\TurnNumberOutofBound();
            }
            $oTurn->setFirst_player($oPrecedentTurn->getWinner());
        }
        $oTurn->setNum_turn($newTurn);
        \Repositories\DbTurn::get()->create($oTurn);

        // On met à jour la partie
        $oRound->setId_current_turn($oTurn->getId());
        return $oTurn;
    }

    /**
     * Distribue à chaque joueur 3 puis 2 cartes et retourne la carte proposée comme atout
     *
     * @param \Entities\Game $oGame
     * @param \Entities\Round $oRound
     * @return String Carte proposée comme atout
     */
    public function dealCardsBeforeTrumpChoose(\Entities\Game &$oGame, \Entities\Round &$oRound): String {
        $currentDealedPlayer = $oRound->getDealer();
        // On donne les 3 + 2 cartes à chacun
        for($i = 0; $i < 4; $i++) {
            $currentDealedPlayer = \Services\Utils::getNextPlayerFromOne($currentDealedPlayer);

            $arrayPart1 = array_slice($oGame->getCards(), $i * 3, 3, true);
            $arrayPart2 = array_slice($oGame->getCards(), $i * 2 + 12, 2, true);

            $oHand = new \Entities\Hand();
            $oHand->setId_round($oRound->getId());
            $oHand->setPlayer($currentDealedPlayer);
            $oHand->setCards(array_merge($arrayPart1, $arrayPart2));
            \Repositories\DbHand::get()->create($oHand);
        }

        // On enleve du deck les cartes ditribuées, array values permet de réinexer depuis  zéro le tableau des cartes restantes
        $oGame->setCards(array_values(array_slice($oGame->getCards(), 20, 32, true)));
        \Repositories\DbGame::get()->update($oGame);

        return array_values($oGame->getCards())[0];
    }

    /**
     * Prendre l'atout
     *
     * @param String $color sur un caractère
     * @param String $player
     */
    public function chooseTrumpAndDeal(\Entities\Game &$oGame, \Entities\Round &$oRound, String $color, String $taker) {
        $oRound->setTaker($taker);
        $oRound->setTrump_color($color[0]);

        // On ne prend pas la premier carte par défaut qui est pour le preneur
        $cardsOffset = 1;
        //On commencere par le joueur à droite du donneur
        $currentDealedPlayer = $oRound->getDealer();
        // On donne les 3 + 2 cartes à chacun
        for($i = 0; $i < 4; $i++) {
            $currentDealedPlayer = \Services\Utils::getNextPlayerFromOne($currentDealedPlayer);

            // On récupère la main du joueur
            $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oRound->getId(), $currentDealedPlayer);

            // Si c'est le preneur
            if ($currentDealedPlayer == $taker) {
                $nbCards = 2;
                $newCards = array(
                    $oGame->getCards()[0]
                );
            } else {
                $nbCards = 3;
                $newCards = array();
            }
            $newCards = array_merge($newCards, array_slice($oGame->getCards(), $cardsOffset, $nbCards));
            $oHand->setCards(array_merge($oHand->getCards(), $newCards));
            \Repositories\DbHand::get()->update($oHand);

            $cardsOffset += $nbCards;
        }

        $oGame->setCurrent_player(\Services\Utils::getNextPlayerFromOne($oRound->getDealer()));
        $oGame->setStep(\Entities\Game::STEP_PLAY_CARD);
        // On enleve du deck les cartes ditribuées, c'est à dire toutes :)
        $oGame->setCards(array());

        // On démarre le premier tour
        $this->startNewTurn($oRound);

        \Repositories\DbRound::get()->update($oRound);
        \Repositories\DbGame::get()->update($oGame);
    }

    /**
     * Cas où personne n'a voulu de l'atout, on reprend les cartes et on se remet à l'étape où on coupe
     *
     * @param \Entities\Game &$oGame
     * @param \Entities\Round &$oRound
     */
    public function noTrumpChosen(\Entities\Game &$oGame, \Entities\Round &$oRound): void {

        // On récupère les mains des joueurs pour reconstituer le deck
        $deck = $oGame->getCards();
        foreach ( array_keys(\PLAYERS) as $player ) {
            $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oGame->getId_current_round(), $player);
            $deck = array_merge($oHand->getCards(), $deck);

            // On supprime la main en base
            \Repositories\DbHand::get()->remove($oHand);
        }
        $oGame->setCards($deck);

        // On se remet à l'étape de coupe et on passe au donneur suivant, on positionne le dealer comme celui qui coupe le deck
        $oGame->setStep(\Entities\Game::STEP_CUT_DECK);
        $oGame->setCurrent_player($oRound->getDealer());
        // On change donc de dealer
        $oRound->setDealer(\Services\Utils::getNextPlayerFromOne($oRound->getDealer()));

        \Repositories\DbRound::get()->update($oRound);
        \Repositories\DbGame::get()->update($oGame);

    }

    public function cutDeck(\Entities\Game $oGame, int $cut = 16): void {
        if ($cut < 0 || $cut > 31) {
            throw new \Exceptions\CutOutOfRange();
        }
        $arrayPart1 = array_slice($oGame->getCards(), 0, $cut, true);
        $arrayPart2 = array_slice($oGame->getCards(), $cut, 32, true);
        $oGame->setCards(array_merge($arrayPart2, $arrayPart1));
        // On passe à l'étape suivante du jeu : le choix de l'atout
        $oGame->setStep(\Entities\Game::STEP_CHOOSE_TRUMP);
        \Repositories\DbGame::get()->update($oGame);
    }

    /**
     *
     * @param int $idTurn
     * @param String $player
     * @param String $card
     * @throws \Exceptions\PlayerHasAlreadyPlayedCard
     * @throws \Exceptions\IllegalCard
     * @return
     */
    public function playCard(int $idTurn, String $player, String $card): \Entities\Turn {

        // On récupère le tour de jeu
        $oTurn = \Repositories\DbTurn::get()->findOneById($idTurn);

        // On récupère la main du joueur
        $oHand = \Repositories\DbHand::get()->findOneByRoundAndPlayer($oTurn->getId_round(), $player);

        // On vérifie qu'il n'a pas déjà joué une carte
        $method = 'getCard_' . strtolower($player);
        if ($oTurn->$method() != '') {
            throw new \Exceptions\PlayerHasAlreadyPlayedCard("Ce joueur a déjà joué une carte");
        }
        // On vérifie que le joueur a bien cette carte en main
        if (!in_array($card, $oHand->getCards())) {
            throw new \Exceptions\IllegalCard("Carte non possédée ou inconnue");
        }
        $method = 'setCard_' . strtolower($player);

        // On joue la carte du joueur
        $oTurn->$method($card);
        // On enlève la carte de la main du joueur
        $handCards = $oHand->getCards();
        if (($key = array_search($card, $handCards)) !== false) {
            unset($handCards[$key]);
            $oHand->setCards($handCards);
        }
        \Repositories\DbHand::get()->update($oHand);
        \Repositories\DbTurn::get()->update($oTurn);
        return $oTurn;
    }

    public function calculateTurnWinner(\Entities\Round &$oRound, \Entities\Turn &$oTurn) {
        if ($oTurn->getCard_n() == '' || $oTurn->getCard_e() == '' || $oTurn->getCard_s() == '' || $oTurn->getCard_w() == '') {
            throw new \Exceptions\TurnIsIncomplete();
        }

        $currentPlayer = $oTurn->getFirst_Player();
        $method = 'getCard_' . strtolower($currentPlayer);
        $askedColor = $oTurn->$method()[0];
        $bestCardColor = $oTurn->$method()[0];
        $bestCardChar = substr($oTurn->$method(), 1);
        if ($bestCardColor == strtolower($oRound->getTrump_color())) {
            $bestCardValue = \CARDS_TRUMP_VALUES[$bestCardChar];
        } else {
            $bestCardValue = \CARDS_VALUES[substr($oTurn->$method(), 1)];
        }

        $bestPlayer = $currentPlayer;
        // On parcoure les cartes jouées par les autres joueurs
        for($i = 0; $i < 3; $i++) {
            // On passe au joueur suivant
            $currentPlayer = \Services\Utils::getNextPlayerFromOne($currentPlayer);

            // On regarde si c'est la couleur demandée n'est pas de l'atout que l'atout n'est pas la meilleure carte du pli
            $method = 'getCard_' . strtolower($currentPlayer);
            $currentColor = $oTurn->$method()[0];
            $currentValue = substr($oTurn->$method(), 1);
            // Si c'est la couleur demandée et que ce n'est pas de l'atout
            if ($currentColor == $askedColor && $bestCardColor != strtolower($oRound->getTrump_color())) {
                if (\CARDS_VALUES[$currentValue] > $bestCardValue) {
                    $bestCardValue = \CARDS_VALUES[$currentValue];
                    $bestPlayer = $currentPlayer;
                }
            } elseif ($currentColor == strtolower($oRound->getTrump_color())) { // Sinon on joue de l'atout
                                                                                // Si la meilleure carte pour le moment n'est pas de l'atout,c'est la premiere coupe du pli
                                                                                // ou si de l'atout a déjà été joué, on regarde s'il est plus fort
                if ($bestCardColor != strtolower($oRound->getTrump_color()) || \CARDS_TRUMP_VALUES[$currentValue] > $bestCardValue) {
                    $bestCardValue = \CARDS_TRUMP_VALUES[$currentValue];
                    $bestPlayer = $currentPlayer;
                    $bestCardColor = $currentColor;
                }
            }
            // Sinon le joueur pisse, il ne peu donc pas remporter le pli
        }

        $oTurn->setWinner($bestPlayer);
        return $bestPlayer;
    }

    /**
     * Cloture une manche et calcule les points
     *
     * @param \Entities\Game $oGame
     * @param \Entities\Round $oRound
     * @return bool Retourne vrai si la partie est terminée, sinon false
     */
    public function closeRound(\Entities\Game &$oGame, \Entities\Round &$oRound): bool {
        // On récupère les tours
        $turns = \Repositories\DbTurn::get()->findAll(array(), array(
            'id_round' => $oRound->getId()
        ));

        $roundPointsNS = 0;
        $roundPointsWE = 0;
        $cardsWonByNS = array();
        $cardsWonByWE = array();
        $currentPlayer = \Services\Utils::getNextPlayerFromOne($oRound->getDealer());

        // On calcule les points de la manche et on reconstitue le deck
        foreach ( $turns as $oTurn ) {
            $turnPoints = 0;
            $turnCards = array();

            for($i = 0; $i < 4; $i++) {
                $method = 'getCard_' . strtolower($currentPlayer);
                $card = $oTurn->$method();
                $cardColor = $card[0];
                $cardChar = substr($card, 1);

                // On reconstitue les cartes du pli
                $turnCards[] = $card;

                // On calcule les points
                // - Si c'est la couleur d'atout
                if ($cardColor == $oRound->getTrump_color()) {
                    $turnPoints += \CARDS_TRUMP_VALUES[$cardChar];
                } else {
                    $turnPoints += \CARDS_VALUES[$cardChar];
                }
                // On passe au joueur suivant en fin de boucle
                $currentPlayer = \Services\Utils::getNextPlayerFromOne($currentPlayer);
            }

            // - On donne les points et on met les cartes dans le tas de la bonne équipe
            if ($oTurn->getWinner() == 'n' || $oTurn->getWinner() == 's') {
                $roundPointsNS += $turnPoints;
                $cardsWonByNS = array_merge($turnCards, $cardsWonByNS);
            } else {
                $roundPointsWE += $turnPoints;
                $cardsWonByWE = array_merge($turnCards, $cardsWonByWE);
            }
            $currentPlayer = $oTurn->getWinner();
        }

        // On donne le 10 de DER au dernier vainqueur
        if ($oTurn->getWinner() == 'n' || $oTurn->getWinner() == 's') {
            $roundPointsNS += 10;
        } else {
            $roundPointsWE += 10;
        }

        // On vérifie que le preneur a rempli son contrat
        if ($oRound->getTaker() == 'n' || $oRound->getTaker() == 's') {
            $team = 'ns';
            $otherTeam = 'we';
        } else {
            $team = 'we';
            $otherTeam = 'ns';
        }
        // SI l'équipe preneuse a fait moins de point que l'équipe adverse, le contrat n'est pas rempli
        if(${'roundPoints' . strtoupper($otherTeam)} == 0){
            ${'roundPoints' . strtoupper($team)} = 0;
            ${'roundPoints' . strtoupper($otherTeam)} = 252;
        }else if(${'roundPoints' . strtoupper($team)} == 0){
            ${'roundPoints' . strtoupper($otherTeam)} = 0;
            ${'roundPoints' . strtoupper($team)} = 252;
        }elseif (${'roundPoints' . strtoupper($team)} < ${'roundPoints' . strtoupper($otherTeam)}) {
            ${'roundPoints' . strtoupper($team)} = 0;
            ${'roundPoints' . strtoupper($otherTeam)} = 162;
        }
        
        

        $oRound->setPoints_NS($roundPointsNS);
        $oRound->setPoints_WE($roundPointsWE);
        \Repositories\DbRound::get()->update($oRound);

        $totalPointNS = $oGame->getTotal_Points_NS() + $roundPointsNS;
        $totalPointWE = $oGame->getTotal_points_WE() + $roundPointsWE;
        $oGame->setTotal_Points_NS($totalPointNS);
        $oGame->setTotal_Points_WE($totalPointWE);
        // On alterne quel paquet va sur l'autre
        if (rand(0, 1)) {
            $oGame->setCards(array_merge($cardsWonByNS, $cardsWonByWE));
        } else {
            $oGame->setCards(array_merge($cardsWonByWE, $cardsWonByNS));
        }

        \Repositories\DbGame::get()->update($oGame);

        // On vérifie que la partie n'est pas terminée
        return ($totalPointNS >= 500 || $totalPointWE >= 500);
    }
}



