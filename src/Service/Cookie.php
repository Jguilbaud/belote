<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\Cookie as SymfonyCookie;
use App\Entity\GameCookiePayload;
use App\Repository\GameRepository;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\InputBag;

class Cookie
{

    const MERCURE_COOKIE_NAME = 'mercureAuthorization';

    const GAME_COOKIE_NAME = 'belote';

    private array $gameCookiePayload = array();

    public function __construct(InputBag $cookies, GameRepository $repoGame)
    {
        // On récupère les cookies de Jeu
        $this->gameCookiePayload = json_decode($cookies->get(self::GAME_COOKIE_NAME), true) ?? array();


        $gameList = $repoGame->findBy([
            'hash' => $this->getGamesHashList()
        ]);
        $updatedGameList = [];
        foreach ($gameList as $hash) {
            $cookiePayload = new GameCookiePayload();
            $cookiePayload->setHashGame($hash->getHash());
            $cookiePayload->setPlayerPosition($this->getPlayerPosition($hash->getHash()));
            $updatedGameList[] = $cookiePayload;
        }
        $this->gameCookiePayload = $updatedGameList;
    }

    /**
     * Ajoute dans le cookie le joueur à une position pour une partie.
     */
    public function addGame(GameCookiePayload $gamePayload): void
    {
        $this->gameCookiePayload[$gamePayload->getHashGame()]['playerPosition'] = $gamePayload->getPlayerPosition();
    }

    /**
     * Ajoute dans le cookie le joueur à une position pour une partie.
     */
    public function setGames(array $gamesPayload): void
    {
        $this->gameCookiePayload = array();
        foreach ($gamesPayload as $gamePayload) {
            $this->addGame($gamePayload);
        }
    }

    /**
     * Génère et retourne le cookie de jeu à passer dans la réponse
     *
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function generateGameCookie(): SymfonyCookie
    {
        $oCookie = SymfonyCookie::create(self::GAME_COOKIE_NAME)->withValue(json_encode($this->gameCookiePayload))
            ->withSecure(true)
            ->withHttpOnly(true)
            ->withSameSite('strict');
        return $oCookie;
    }

    /**
     * Génère et retourne le cookie de jeu à passer dans la réponse
     *
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function generateMercureCookie(String $jwtKey): SymfonyCookie
    {
        $subscribes = [];
        // On ajoute la souscription pour chaque partie
        foreach ($this->gameCookiePayload as $hashGame => $game) {
            $subscribes[] = 'game/' . $hashGame;
            $subscribes[] = 'game/' . $hashGame . '/' . $game->getPlayerPosition();
        }

        $jwt = JWT::encode([
            'mercure' => [
                'subscribe' => $subscribes
            ]
        ], $jwtKey, 'HS256');

        $oCookie = SymfonyCookie::create(self::MERCURE_COOKIE_NAME)->withValue($jwt)
            ->withPath('/.well-known/mercure')
            ->withSecure(true)
            ->withHttpOnly(true)
            ->withSameSite('strict');
        return $oCookie;
    }

    public function getPlayerPosition(String $hashGame)
    {
        // TODO exception, instanciation objet GameCookiePayload
        return $this->gameCookiePayload[$hashGame]['playerPosition'] ?? null;
    }

    public function getGamesHashList()
    {
        return array_keys($this->gameCookiePayload);
    }
}

