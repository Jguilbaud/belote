<?php
// General
define('VERSION', '0.1-alpha1');
define('DEBUG', true);
define('PATH', '/home/johan/eclipseworkspace/Belote/src');
// Path
define('CONTROLLERS_PATH', PATH . '/controllers/');
define('SERVICES_PATH', PATH . '/services/');
define('REPOSITORIES_PATH', PATH . '/repositories/');
define('MODELS_PATH', PATH . '/models/');
define('VIEWS_PATH', PATH . '/templates/');

define('BELOTE_GAME_COOKIE_BASENAME','belote_game_');
//Mercure
define('MERCURE_URL','http://localhost:3000/.well-known/mercure');
define('MERCURE_JWT_KEY','myjwt');
define('MERCURE_JWT_ALGORITHM','HS256');
define('MERCURE_COOKIE_NAME','mercureAuthorization');
define('MERCURE_COOKIE_DOMAIN','localhost');

define('DECK_CARDS_LIST',array(
    'sA',
    's10',
    'sR',
    'sD',
    'sV',
    's9',
    's8',
    's7',
    'hA',
    'h10',
    'hR',
    'hD',
    'hV',
    'h9',
    'h8',
    'h7',
    'cA',
    'c10',
    'cR',
    'cD',
    'cV',
    'c9',
    'c8',
    'c7',
    'dA',
    'd10',
    'dR',
    'dD',
    'dV',
    'd9',
    'd8',
    'd7'
));
define('CARDS_VALUES', array(
    'A' => 11,
    '10' => 10,
    'R' => 4,
    'D' => 3,
    'V' => 2,
    '9' => 0,
    '8' => 0,
    '7' => 0
));
define('CARDS_TRUMP_VALUES', array(
    'A' => 11,
    '10' => 10,
    'R' => 4,
    'D' => 3,
    'V' => 20,
    '9' => 14,
    '8' => 0,
    '7' => 0
));
define('PLAYERS',array(
    'N',
    'E',
    'S',
    'O'
));
