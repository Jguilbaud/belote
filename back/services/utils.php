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

    /**
     * Renvoi la taille en octets (bytes)
     *
     * @param int $size la taille
     * @param String $i l'unité (Mo, B, etc)
     * @return int La taille formattée en Octets
     */
    public static function getSizeInBytes($size, $unit) {
        $sizeInBytes = 0;
        switch (strtolower($unit)) {
            case "b" :
            case "o" :
                $sizeInBytes = ( int ) $size;
                break;
            case "kb" :
            case "ko" :
                $sizeInBytes = ( int ) $size * 1024;
                break;
            case "mb" :
            case "mo" :
                $sizeInBytes = ( int ) $size * 1024 * 1024;
                break;
            case "gb" :
            case "go" :
                $sizeInBytes = ( int ) $size * 1024 * 1024 * 1024;
                break;
        }

        return $sizeInBytes;
    }
}
