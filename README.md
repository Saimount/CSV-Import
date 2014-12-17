CSV-Import
==========

##Initialization
Initialize database with src/sql/products.sql :
`mysql -u user -p database < src/sql/products.sql`

Initialize vendors :
`composer install`

##Usage
`php ./src/csv_import.php -u user -p password -d database -f files/big_exemple.csv`
