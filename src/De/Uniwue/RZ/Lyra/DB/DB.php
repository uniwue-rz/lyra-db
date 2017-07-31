<?php
/**
* Lyra DB Component
* 
* @author Pouyan Azari <pouyan.azari@uni-wuerzburg.de>
* @license MIT
*/
namespace De\Uniwue\RZ\Lyra\DB;

use Doctrine\DBAL\Configuration as DBALConfiguration;
use Doctrine\DBAL\DriverManager as DBALDriverManager;

class DB{

    /**
    * Placeholder for the last Id inserted or last Ids inserted when there are more.
    * @var int
    */
    private $lastInsertId;

    /**
    * Placeholder for the DBAL Configuration Object.
    * @var array
    */
    private $config;
    /**
    * Placeholder for the DBAL Database Connection
    * @var Connection
    */
    private $dbConnection;

    /**
    * Placeholder for the logger
    * @var Logger
    */
    private $logger;

    /**
    * Constructor
    *
    * @param array  $config     The configuration for the database
    * @param Logger $logger     The logger for the application
    */
    public function __construct($config, $logger = null){
        $this->logger = $logger;
        $this->config = $config;
        $this->createClient();
    }

    /**
    * Creates the database client from the given configuration
    */
    public function createClient(){
        $config = new DBALConfiguration();
        $this->dbConnection = DBALDriverManager::getConnection($this->config, $config);
    }

    /**
    * Logs the given data to the logger with the given level
    *
    * @param string $level      The log level for the given system
    * @param string $message    The message that should be logged
    * @param array  $context    The context of the given message
    */
    public function log($level, $message, $context = array()){
        if($this->logger !== null){
            $this->logger->log($level, $message, $context);
        }
    }

    /**
    * Runs the database query on the given server. The query should be built with queryBuilder.
    *
    * @param \Doctrine\DBAL\Query\Query $query  The query that should be sent to the database server.
    * @param bool                       $dryrun Run the command in dryrun mode.
    *
    * @return mix
    *
    * @throws \Exception
    */
    public function runQuery($query, $dryrun = false){
        $result = null;
        $successMessage= $this->getQueryRunMessage($query, $query->getParameters(), $dryrun);
        if($dryrun === true){
            $this->log("info", $successMessage);

            return;
        }
        $this->getDatabaseConnection()->setAutoCommit(true);
        $this->getDatabaseConnection()->beginTransaction();
        try{
            $result = $query->execute();
            $this->setLastInsertId($this->getDatabaseConnection()->lastInsertId());
            $this->getDatabaseConnection()->commit();
            $this->log("info", $successMessage);

            return $result;
        }catch(\Exception $e){
            $this->getDatabaseConnection()->rollBack();

            throw $e;
        }
    }

    /**
    * Creates a new query builder for the given application.
    * For every independent Query a new Query Builder should be created. An old QueryBuilder object
    * will have the old data still there so it can not be used for a new query.
    *
    * @return Doctrine\DBAL\Query\QueryBuilder
    **/
    public function getQueryBuilder(){
        return $this->getDatabaseConnection()->createQueryBuilder();
    }

    /**
    * Returns the last inserted element ID.
    *
    * @return long
    */
    public function getLastInsertId(){
        return $this->lastInsertId;
    }

    /**
    * Sets the last inserted id for the given database.
    *
    * @param long $lastInsertId The last id of the element inserted.
    */
    private function setLastInsertId($lastInsertId){
        $this->lastInsertId = $lastInsertId;
    }

    /**
    * Returns the query running message for the given query.
    * 
    * @param string $queryString The queryString that is used.
    * @param array  $queryParams The parameters for the query.
    * @param bool   $dryrun      The dryrun flag for the query.
    *
    * @return string
    */
    private function getQueryRunMessage($queryString, $queryParams = array(), $dryrun = false){
        $base = "Running ";
        if($dryrun){
            $base = $base."DRYRUN ";
        }
        $base = $base. "QUERY; $queryString with PARAMS: ". implode(', ', $queryParams);

        return $base;
    }


    /**
    * Runs a database statement string. This is when QueryBuilder is not helping. Normally using this method
    * is discouraged.
    *
    * @param string $statement The statement to be run on the server.
    * @param bool   $dryrun    The dryrun flag for the given system.
    *
    * @return mix
    *
    * @throws \Exception
    */
    public function runStatement($statement, $dryrun = false){
        $successMessage = $this->getQueryRunMessage($statement, array(), $dryrun);
        if ($dryrun === true) {
            $this->log("info", $successMessage);

            return;
        }
        $this->getDatabaseConnection()->beginTransaction();
        try {
            $stmt = $this->getDatabaseConnection()->prepare($statement);
            $result = $stmt->execute();
            $this->setLastInsertId($this->getDatabaseConnection()->lastInsertId());
            $this->log("info", $successMessage);

            return $result;

        } catch (\Exception $e) {
            $this->getDatabaseConnection()->rollBack();

            throw $e;
        }
    }

    /**
    * Sets the database connection for the given instance.
    *
    * @param Doctrine\DBAL\Driver\Connection $databaseConnection The database connection object instance.
    *
    */
    public function setDatabaseConnection($databaseConnection){
        $this->dbConnection = $databaseConnection;
    }

    /**
    * Returns the instance of database connection used in the DB.
    *
    * @return  Doctrine\DBAL\Driver\Connection || null
    **/
    public function getDatabaseConnection(){
        return $this->dbConnection;
    }

    /**
    * Creates a Database QueryBuilder for the given database connection
    *
    * @param DatabaseConnection $databaseConnection The database Connection object
    *
    * @return QueryBuilder
    */
    public function createQueryBuilder($databaseConnection){
        return $databaseConnection->getQueryBuilder();
    }
}