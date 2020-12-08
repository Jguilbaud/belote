<?php

namespace App\Model;

class GameCookiePayload implements \JsonSerializable
{
    /**
     */
    private String $hashGame;

    /**
     */
    private String $playerPosition;


    public function getHashGame(): ?string
    {
        return $this->hashGame;
    }

    public function setHashGame(string $hashGame): self
    {
        $this->hashGame = $hashGame;

        return $this;
    }

    public function getPlayerPosition(): ?string
    {
        return $this->playerPosition;
    }

    public function setPlayerPosition(string $playerPosition): self
    {
        $this->playerPosition = $playerPosition;

        return $this;
    }
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }


}
