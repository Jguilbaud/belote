<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Cookie;
use App\Entity\GameCookiePayload;
use Symfony\Component\HttpFoundation\InputBag;

class GameCookie
{

    const COOKIE_NAME = 'belote';

    private array $cookiePayload = array();

    public function __construct(InputBag $cookies)
    {
        $this->cookiePayload = json_decode($cookies->get(self::COOKIE_NAME), true) ?? array();
    }

    /**
     * Ajoute dans le cookie le joueur à une position pour une partie.
     */
    public function addGame(GameCookiePayload $gamePayload)
    {
        $this->cookiePayload[$gamePayload->getHashGame()] = $gamePayload;
    }

    /**
     * Génère et retourne le cookie à passer dans la réponse
     *
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function generateCookie()
    {
        $oCookie = Cookie::create(self::COOKIE_NAME)->withValue(json_encode($this->cookiePayload))
            ->withSecure(true)
            ->withHttpOnly(true)
            ->withSameSite('strict');
        return $oCookie;
    }

    public function getPlayerPosition(String $hashGame)
    {
        // TODO exception, instanciation objet GameCookiePayload
        return $this->cookiePayload[$hashGame]->playerPosition;
    }

    public function getGamesHashList()
    {
        return array_keys($this->cookiePayload);
    }
}

