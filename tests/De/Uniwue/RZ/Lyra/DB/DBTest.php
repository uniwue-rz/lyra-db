<?php
/**
* Tests for the Lyra Database system.
*/

namespace De\Uniwue\RZ\Lyra\DB;

include "Logger.php";

class DBTest extends \PHPUnit_Framework_TestCase{

    public function setUp(){
        global $root;
        global $databaseConfig;
        $this->root = $root;
        $this->databaseConfig = $databaseConfig;
        $this->logger = new Logger("Name");
    }

    /**
    * This is the constructor test
    *
    */
    public function testInit(){
        $db = new DB($this->databaseConfig, $this->logger);
        $this->assertEquals(get_class($db),"De\Uniwue\RZ\Lyra\DB\DB");
    }

    /**
    * Test the database connection
    *
    **/
    public function testDatabaseConnection(){
        $db = new DB($this->databaseConfig, $this->logger);
        $conn = $db->getDatabaseConnection();
        $this->assertTrue($conn->connect());
    }

    /**
    * Tests the query builder generator
    *
    */
    public function testGetQueryBuilder(){
        $db = new DB($this->databaseConfig, $this->logger);
        $qb = $db->getQueryBuilder();
        $query = $qb->expr()->eq("hello", "world");
        $this->assertEquals($query, "hello = world");
    }

    /**
    * Test Run Query in Dryrun mode
    *
    */
    public function testRunQueryDryRun(){
        $db = new DB($this->databaseConfig, $this->logger);
        $qb = $db->getQueryBuilder();
        $query = $qb->select("uid")->from("test_table")->where($qb->expr()->eq("pid", "?"))->setParameters(array(0));
        $db->runQuery($query, true);
    }

    /**
    * Test the statement runner in dryrun mode
    *
    */
    public function testRunStatementDryrun(){
        $stmt = "SELECT * FROM test_table where uid = 1";
        $db = new DB($this->databaseConfig, $this->logger);
        $db->runStatement($stmt,true);
    }

    /**
    * Test the query runner
    *
    */
    public function testRunQuery(){
        $db = new DB($this->databaseConfig, $this->logger);
        // Create the table
        $stmt = "CREATE TABLE IF NOT EXISTS test_table(id MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY, uid int, pid int, data varchar(255))";
        $db->runStatement($stmt);
        $qb = $db->getQueryBuilder();
        $query = $qb->insert("test_table")->values(
            array("uid" => "?", "pid" => "?", "data" => "?")
            )->setParameters(array(0,0,"test"));
        $db->runQuery($query);
        $lastId = $db->getLastInsertId();
        $this->assertEquals($lastId, 1);
        // Drop Table
        $stmt = "DROP TABLE test_table";
        $db->runStatement($stmt);
    }

    /**
    * Tests if exception is thrown
    *
    * @expectedException Doctrine\DBAL\Exception\SyntaxErrorException
    */
    public function testRunQueryException(){
        $db = new DB($this->databaseConfig, $this->logger);
        // Create the table
        $stmt = "CREATE TABLE IF NOT EXISTS test_table(id MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY, uid int, pid int, data varchar(255))";
        $db->runStatement($stmt);
        $qb = $db->getQueryBuilder();
        $query = $qb->insert("test_table")->values(
            array("uid" => "?", "pid" => "?", "data" => "?")
            )->setParameters(array());
        $db->runQuery($query);
        $lastId = $db->getLastInsertId();
        $this->assertEquals($lastId, 1);
        // Drop Table
        $stmt = "DROP TABLE test_table";
        $db->runStatement($stmt);
    }
}