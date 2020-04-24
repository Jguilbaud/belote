<?php

namespace Entities;

class GameJwtPayload extends AbstractJwtPayload {
    private String $hashGame = '';
    private String $playerPosition = '';

    /**
     *
     * @return string
     */
    public function getHashGame() {
        return $this->hashGame;
    }

    /**
     *
     * @param string $hashGame
     */
    public function setHashGame($hashGame) {
        $this->hashGame = $hashGame;
    }

    /**
     *
     * @return string
     */
    public function getPlayerPosition() {
        return $this->playerPosition;
    }

    /**
     *
     * @param string $playerPosition
     */
    public function setPlayerPosition($playerPosition) {
        $this->playerPosition = $playerPosition;
    }

    public function jsonSerialize() {
        return array(
            'hashGame' => $this->hashGame,
            'playerPosition' => $this->playerPosition
        );
    }

    public function fromStdClass(\stdClass $stdClass) {
        $this->hashGame = $stdClass->hashGame;
        $this->playerPosition = $stdClass->playerPosition;
    }
}