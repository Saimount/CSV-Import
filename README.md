CSV-Import
==========

##Initialization
* Initialize database with src/sql/products.sql : `mysql -u user -p database < src/sql/products.sql`
* Initialize vendors : `composer install`
* Install `php5-mysqlnd` : `sudo apt-get install php5-mysqlnd`
* Install phpunit

##Usage
`php ./src/csv_import.php -u user -p password -d database --delimiter ';' -f files/big_exemple.csv`

##Testing
* Install `phpunit`
* Configure `tests/phpunit.xml` file based on `tests/phpunit.sample.xml`
* Execute `phpunit --bootstrap src/classes/Import.php --configuration tests/phpunit.xml tests/`
