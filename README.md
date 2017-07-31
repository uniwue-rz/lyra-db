# Lyra DB
This is the Lyra database component which is built on the top of the Doctrine/DBAL. With this there will be a log system available
for all database transaction. The queries or statement should be built with the help of QueryBuilder. It is also possible to simulate database commands
using the dryrun version of query runner.


## Configuration
On your unix operating system you need to install the `php-mysql`, `php-pdo` when you want the connection to the mysql server. Other Drivers need there own php extensions. Then you need to create a configuration array with the following data:

```lang=php
```

## Installation
To install this component you can simply require it using composer. The package exists in [packagist](https://pacakagist.org) website.

```lang=bash
```

It is also possible to clone this repository and add the path to composer or load the contents with help of `autoload`.

```lang=json
```

The repository it self also can be added to composer and the data can be fetched directly from the repository.

```lang=json
```

## Usage
To use this library simply create an instance of it using the configuration array and a logger which has the following method implemented. It should be said that logging is not necessary and null logger can also be used.

## Test and Development
The `phpunit` settings are written for this library, so you can test it without problem. Before the test make sure you have set the database server address and the user is allowed to create the test database. This configuration can be found in `/tests/bootstrap.php`

## License
See LICENSE file