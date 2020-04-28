<?php
// General
define('VERSION', '0.1-alpha1');

define('PATH', __DIR__ . '/..');
// Path
define('CONTROLLERS_PATH', PATH . '/controllers/');
define('SERVICES_PATH', PATH . '/services/');
define('REPOSITORIES_PATH', PATH . '/repositories/');
define('MODELS_PATH', PATH . '/models/');
define('VIEWS_PATH', PATH . '/templates/');

define('BELOTE_GAME_COOKIE_BASENAME', 'belote_game_');
// Mercure
define('MERCURE_JWT_ALGORITHM', 'HS256');
define('MERCURE_COOKIE_NAME', 'mercureAuthorization');


define('DECK_CARDS_LIST', array(
    'sa',
    's10',
    'sk',
    'sq',
    'sj',
    's9',
    's8',
    's7',
    'ha',
    'h10',
    'hk',
    'hq',
    'hj',
    'h9',
    'h8',
    'h7',
    'ca',
    'c10',
    'ck',
    'cq',
    'cj',
    'c9',
    'c8',
    'c7',
    'da',
    'd10',
    'dk',
    'dq',
    'dj',
    'd9',
    'd8',
    'd7'
));
define('CARDS_VALUES', array(
    'a' => 11,
    '10' => 10,
    'k' => 4,
    'q' => 3,
    'j' => 2,
    '9' => 0,
    '8' => 0,
    '7' => 0
));
define('CARDS_TRUMP_VALUES', array(
    'a' => 11,
    '10' => 10,
    'k' => 4,
    'q' => 3,
    'j' => 20,
    '9' => 14,
    '8' => 0,
    '7' => 0
));
define('CARDS_COLORS', array(
    'h' => 'heart',
    's' => 'spade',
    'd' => 'diamond',
    'c' => 'club'
));
define('PLAYERS', array(
    'n' => 'north',
    'e' => 'east',
    's' => 'south',
    'w' => 'west'
));
