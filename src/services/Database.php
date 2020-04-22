<?php

namespace Services;

/**
 * Classe d'accès à une Base de données (eg.
 * MySQL) via \PDO en UTF8
 *
 * Cette classe est utilisable de 2 façons :
 * 1) De manière classique en instanciant un objet Database
 * <code>$database = new Database("localhost","mabase","monuser","monmotdepasse);</code>
 *
 * 2) De manière statique avec la méthode statique \Services\Database::get()
 * Il faut dans ce cas au préalable avoir fait appel à la méthode statique
 * Database::setDBConnectorConfig($host,$base,$user,$password)
 * Ce mode de fonctionnement permet de ne pas avoir à gérer l'objet Database
 * lors de l'utilisation dans plusieurs classe. La méthode getDBConnector()
 * se charge de créer l'objet Database si besoin est en utilisation les paramètres
 * de connexion fournis via Database::setDBConnectorConfig($host,$base,$user,$password)
 * qui ne doit donc être appelé qu'une fois au début du script
 * <code> Database::setDBConnectorConfig("localhost","mabase","monuser","monmotdepasse); </code>
 *
 *
 * Dans les 2 cas les méthodes de requête à appeler sont
 * getData pour faire un SELECT
 * setData pour faire un INSERT, UPDATE ou DELETE
 *
 * Exemples d'appel à setData :
 * 1) <code>$database->setData("INSERT INTO matable (monchamp) VALUES (:mavaleur)", array(":mavaleur" => $valeur));</code>
 * 2) <code>\Services\Database::get()->setData("INSERT INTO matable (monchamp) VALUES (:mavaleur)", array(":mavaleur" => $valeur));</code>
 *
 * @author Johan GUILBAUD
 * @version 1.8-MegaDebrid - 28 Mars 2020
 */
class Database extends StaticAccessClass {

    // CONF :
    protected $host = '';
    protected $base = '';
    protected $user = '';
    protected $password = '';
    protected $PDODriver = 'mysql';
    protected $connected = false;

    /**
     *
     * @var \PDO
     */
    protected $PDOConnector = null;

    /**
     *
     * @var \Services\Database Instance/Objet BDD qui sera appelé lors des appel par voie statique
     */
    protected static $dBConnector = null;

    /**
     * Stocke la configuration de la BDD pour les appels par voie statique
     * host,base,user,password
     *
     * @var Array paramètres d'accès à la BDD MySQL
     */
    protected static $configDBConnector = array();

    /**
     * Nombre de requetes
     *
     * @var integer
     */
    public $nbRequests = 0;
    public $requests = array();

    /**
     * Nombre de lignes affectées par la dernière requête
     *
     * @var integer
     */
    private $affectedRowsCount = -1;

    /**
     * Création de l'objet Database
     * Les paramètres à passer au constructeur sont les suivants
     * Pour SQLite passer le nom du fichier dans $host et mettre $base, $user et $password à vide
     *
     * @param String $host Host de la base de donnée (généralement : localhost ou 127.0.0.1)
     * @param String $base Nom de la base/schéma à utiliser
     * @param String $user Nom de l'utilisateur pour se connecter à la base de donnée
     * @param String $password Mot de passe de l'utilisateur
     * @param string $driver Driver \PDO à utiliser
     */
    public function __construct($host = 'localhost', $base = '', $user = '', $password = '', $driver = false) {
        if (func_num_args() >= 1) {
            $this->host = $host;
            $this->base = $base;
            $this->user = $user;
            $this->password = $password;
            if ($driver) {
                $this->setPDODriver($driver);
            }
        } else {
            $this->host = static::$configDBConnector['host'];
            $this->base = static::$configDBConnector['base'];
            $this->user = static::$configDBConnector['user'];
            $this->password = static::$configDBConnector['password'];
            $this->setPDODriver($this->PDODriver);
        }
    }

    /**
     * Initialise la connexion \PDO au serveur SGBD
     *
     * @throws DatabaseException
     */
    protected function connect() {
        $this->requests[] = "connect";
        try {
            if ($this->PDODriver == 'sqlite') {
                $this->PDOConnector = new \PDO($this->PDODriver . ':' . $this->host);
            } else {
                $this->PDOConnector = new \PDO($this->PDODriver . ':host=' . $this->host . ';dbname=' . $this->base, $this->user, $this->password);
            }

            $this->PDOConnector->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            if ($this->PDODriver == 'mysql') {
                $this->PDOConnector->exec('SET NAMES utf8');
                $this->nbRequests++;
            }
            $this->connected = true;
        } catch ( \Exception $e ) {
            $this->connected = false;
            throw new DatabaseException('[Database.connect]Could not connect to database :' . $e->getMessage());
        }
    }

    /**
     * Utilisation par voie statique :
     * Retourne l'instance commune Database
     * La configuration doit avoir été renseignée au préalable
     * avec la méthode statique Database::setDBConnectorConfig
     *
     * @deprecated Déprécié pour Mega débrid
     * @return Database Retourne l'instance Database
     * @throws DatabaseException
     */
    public static function getDBConnector() {
        if (static::$dBConnector == null) {

            // Si on a pas les paramètres d'accès à la BDD on lève une exception
            if (!(isset(static::$configDBConnector['host']) && isset(static::$configDBConnector['base']) && isset(static::$configDBConnector['user']) && isset(static::$configDBConnector['password']))) {
                throw new DatabaseException('[Database.getDBConnector] Error : Missing parameter(s), have you called Database::setDBConnectorConfig ?');
            }

            // On créé l'objet Database
            static::$dBConnector = new static(static::$configDBConnector['host'], static::$configDBConnector['base'], static::$configDBConnector['user'], static::$configDBConnector['password']);
        }
        return static::$dBConnector;
    }

    /**
     * Utilisation par voie statique :
     * Initialise les paramètres d'accès à la BDD
     *
     * @param host <b>String</b> URL/IP du serveur BDD (généralement localhost)
     * @param base <b>String</b> Base MYSQL où sont stockées les tables
     * @param user <b>String</b> Utilisateur d'accès à la base
     * @param password <b>String</b> Mot de passe de l'utilisateur
     */
    public static function setDBConnectorConfig($host, $base, $user, $password) {
        static::$configDBConnector = array(
            'host' => ( string ) $host,
            'base' => ( string ) $base,
            'user' => ( string ) $user,
            'password' => ( string ) $password
        );
    }

    /**
     * Récupère des données dans le SGBD.
     *
     * @param String $query
     * @param Array $args
     * @param Array $types Permet de forcer le type de chaque paramètre, par défaut c'est \PDO::PARAM_STR
     * @return \SplFixedArray|False si aucune ligne n'a été trouvée, sinon retour la liste des résultats dans un tableau multidimensionnel avec index autoincrémenté
     * @throws DatabaseQueryException
     */
    public function getData($query, $args = null, $types = null) {
        if ($pq = $this->prepareQuery($query, $args, $types)) {

            $results = new \SplFixedArray(0); // SplFixedArray gère mieux la mémoire en cas de fort volume de données remontées par le sgbd
            while ( $row = $pq->fetch(\PDO::FETCH_ASSOC) ) {
                $currMaxIndex = $results->getSize();
                $results->setSize($currMaxIndex + 1);
                $results[$currMaxIndex] = $row;
            }

            $this->affectedRowsCount = $pq->rowCount();
            if (count($results) > 0) {
                return $results;
            } else {
                return false;
            }
        } else {
            // Une erreur s'est produite lors de la préparation de la requête
            $PDOError = $pq->errorInfo();
            throw new DatabaseQueryException('[Database.getData] Error : ' . $PDOError[2]);
        }
    }

    /**
     * Modifie des données dans le SGBD.
     *
     * @param String $query
     * @param Array $args
     * @param Array $types Permet de forcer le type de chaque paramètre, par défaut c'est \PDO::PARAM_STR
     * @throws DatabaseQueryException
     * @return boolean retourne true ou false selon si la mise à jour a réussi ou non
     */
    public function setData($query, $args = null, $types = null) {
        if ($pq = $this->prepareQuery($query, $args, $types)) {
            $this->affectedRowsCount = $pq->rowCount();
            return $pq;
            // Une erreur s'est produite lors de la préparation de la requête
        } else {
            $PDOError = $pq->errorInfo();
            throw new DatabaseQueryException('[Database.setData] Error : ' . $PDOError[2]);
        }
    }

    /**
     * Prépare la requête
     * Cette méthode est interne et est appelée depuis setData ou getData
     *
     * @param String $query
     * @param Array $args Valeurs des variables
     * @param Array $types Types \PDO des variables
     * @throws DatabaseQueryException
     * @throws DatabaseException
     * @return boolean retourne true ou false selon si la requête a réussi ou non
     */
    protected function prepareQuery($query, $args = null, $types = null) {
        if (!$this->connected) {
            $this->connect();
        }

        // On gère le cas où l'arg est un tableau (cas du [NOT] IN () )
        if (is_array($args)) {
            foreach ( $args as $argName => $argValue ) {
                if (is_array($argValue)) {
                    $argNames = $this->_transformQueryParamArrayIntoStringList($argValue, $argName, $args);
                    $query = str_replace($argName, $argNames, $query);
                }
            }
        }

        // On prépare la requête
        $pq = $this->PDOConnector->prepare($query);

        if (is_array($args)) {
            foreach ( $args as $argName => $argValue ) {
                $type = \PDO::PARAM_STR;
                if ($types != null && array_key_exists($argName, $types)) {
                    $type = $types[$argName];
                }
                $pq->bindValue($argName, $argValue, $type);
            }
        }
        // On execute la requête et en cas d'erreur on gènère une exception
        if (!$pq->execute($args)) {
            $PDOError = \PDOStatement::errorInfo();
            throw new DatabaseQueryException('[Database.prepareQuery.execute] \PDO Error : {' . $PDOError[0] . '-' . $PDOError[1] . '} ' . $PDOError[2]);
        }
        $this->requests[] = $query;
        $this->nbRequests++;

        return $pq;
    }

    /**
     * Subdivision du code de PrepareQuery pour le cas du "[NOT] IN" ()
     */
    private function _transformQueryParamArrayIntoStringList($argValue, $argName, &$args) {
        $nbValues = count($argValue);
        $argNames = array();
        // on va remplacer :params par :param_0,:param_1, etc
        for($i = 0; $i < $nbValues; $i++) {
            $args[$argName . '_' . $i] = $argValue[$i];
            $argNames[] = $argName . '_' . $i;
        }
        unset($args[$argName]);
        $argNames = implode(',', array_values($argNames));
        return $argNames;
    }

    /**
     * Retourne l'id de la dernière ligne insérée
     *
     * @return integer
     */
    public function getLastInsertRowId($pgSeqName = null) {
        return $this->PDOConnector->lastInsertId($pgSeqName);
    }

    /**
     * Retourne le nombre de lignes affectées par la dernière requête.
     */
    public function getAffectedRows() {
        return $this->affectedRowsCount;
    }

    /**
     * Permet de choisir le driver/connecteur \PDO (défaut : \PDO_MYSQL)
     * Liste disponible ici : http://php.net/manual/fr/\PDO.drivers.php
     *
     * @param String $PDODriverName Nom du driver \PDO, utuilsier de préférence les constantes \PDO
     * @param Bool $force Force l'utilisation du driver même s'il est considéré comme absent du système
     * @throws DatabaseException Lève une exception si le driver n'est pas supporté, outrepassable en mettant $force à true
     */
    public function setPDODriver($PDODriverName, $force = false) {
        if (!$force && !in_array($PDODriverName, \PDO_drivers())) {
            /** @phpstan-ignore-next-line */
            throw new DatabaseException('[Database.set\PDODriver] Driver ' . $PDODriverName . ' not supported on this Operating System');
        }
        $this->PDODriver = $PDODriverName;
    }

    /**
     * Retourne la version du schéma de la base de donnée
     *
     * @param String $table Nom de la table dans laquelle l'information est stockée
     * @param String $field Nom du champ dans lequel la version est stockée
     */
    public static function getSchemaVersion($table = 'version', $field = 'version') {
        $res = static::getDBConnector()->getData('SELECT ' . $field . ' as v FROM ' . $table);
        if ($res) {
            return $res[0]['v'];
        } else {
            return false;
        }
    }
}

// Exceptions
class DatabaseException extends \PDOException {
}
class DatabaseQueryException extends DatabaseException {
}
