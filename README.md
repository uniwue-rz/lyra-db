# Lyra DB
This is the Lyra database component which is built on the top of the Doctrine/DBAL. With this there will be a log system available
for all database transaction. The queries or statement should be built with the help of QueryBuilder. It is also possible to simulate database commands
using the dryrun version of query runner.


## Configuration
On your unix operating system you need to install the `php-mysql`, `php-pdo` when you want the connection to the mysql server. Other Drivers need there own php extensions. Then you need to create a configuration array with the following data:

```lang=php
$databaseConfig = array(
    "driver" => "pdo_mysql",
    "user" => "testuser",
    "password" => "testpassword",
    "host" => "localhost",
    "dbname" => "testdb"
);
```
More information about the config array can be found in [Doctrine Website](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html).

## Installation
To install this component you can simply require it using composer. The package exists in [packagist](https://pacakagist.org) website.

```lang=bash
composer require rzuw/lyra-db
```

It is also possible to clone this repository and add the path to composer or load the contents with help of `autoload`.

```lang=json
{
    "repositories": [
        {
            "type": "path",
            "url": "/path-to-git-clone"
        }
    ],
    "require": {
        "rzuw/lyra-db": "*"
    }
}

```

The repository it self also can be added to composer and the data can be fetched directly from the repository.

```lang=json
{
    "require": {
        "rzuw/lyra-db": "*"
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "ssh://git@github.com/uniwue-rz/lyra-db.git"
        }
    ]
}

```

## Usage
To use this library simply create an instance of it using the configuration array and a logger which has the following method implemented. It should be said that logging is not necessary and null logger can also be used.

```lang=php
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
// Drop Table
$stmt = "DROP TABLE test_table";
$db->runStatement($stmt);
```

## Test and Development
The `phpunit` settings are written for this library, so you can test it without problem. Before the test make sure you have set the database server address and the user is allowed to create the test database. This configuration can be found in `/tests/bootstrap.php`

## License
See LICENSE file