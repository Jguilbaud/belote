<?php

namespace Services;

class Utils {

    /**
     * Génère un hash alphanumérique aléatoire de la longueur souhaitée
     *
     * @param number $size Taille du hash souhaité
     * @return string Hash généré
     */
    public static function generateHash($size = 16) {
        return substr(bin2hex(openssl_random_pseudo_bytes($size)), 0, $size);
    }

    public static function secondsToHuman($seconds) {
        $hours = floor($seconds / 3600);

        $seconds -= $hours * 3600;

        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;

        $str = '';
        if ($hours > 0) {
            $str .= $hours . 'h ';
        }
        if ($minutes > 0) {
            $str .= $minutes . 'm ';
        }
        if ($seconds > 0) {
            $str .= $seconds . 's ';
        }
        return trim($str);
    }

    public static function getCardImgUrl(String $cardCode): String {
        return \Conf::BASE_URL . '/img/cards/' . strtolower($cardCode) . '.png';
    }


    /**
     * Récupérer le joueur suivant un joueur donné
     *
     * @param String $currentPlayer (n, e, s ou w)
     * @return string
     */
    public static function getNextPlayerFromOne(String $currentPlayer = 'N') {
        switch (strtolower($currentPlayer)) {
            case 'n' :
                return 'e';
            case 'e' :
                return 's';
            case 's' :
                return 'w';
            case 'w' :
                return 'n';
            default :
                throw new \Exceptions\BeloteException('Invalid player');
        }
    }

    /**
     * Récupérer le joueur précédent un joueur donné
     *
     * @param String $currentPlayer (n, e, s ou w)
     * @return string
     */
    public static function getPrecedentPlayerFromOne(String $currentPlayer = 'N') {
        switch (strtolower($currentPlayer)) {
            case 'n' :
                return 'w';
            case 'e' :
                return 'n';
            case 's' :
                return 'e';
            case 'w' :
                return 's';
            default :
                throw new \Exceptions\BeloteException('Invalid player');
        }
    }
}
