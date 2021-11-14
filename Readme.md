#### Overview:
* Minimal requirements to run application is [Git](https://git-scm.com/book/en/v2/Getting-Started-Installing-Git), [PHP](http://php.net/manual/en/install.php) and [composer](https://getcomposer.org/download/) and [Symfony-cli](https://symfony.com/download) installed
* Additionally install [Symfony-cli](https://symfony.com/download)
* Optionaly install [Xdebug](https://xdebug.org/docs/install) for running unit tests with code coverage
* Rename dist.env to .env and keep SQLite DB credentials
* With Git, composer, PHP and symfony-cli installed run from project direcotry `composer install` and finally to start server `symfony server:start`
* Built-in Symfony server will be running under: `http://127.0.0.1:8000`
* Api is available under http://127.0.0.1:8000/threshold, http://127.0.0.1:8000/debit, http://127.0.0.1:8000/credit
* Postman payload, cUrl:
```
curl --location --request POST 'http://127.0.0.1:8000/threshold' \
--form 'threshold="300"' \
--form 'user="3da5f545-89f3-403f-9b8d-fc752d369208"'
```
```
curl --location --request POST 'http://127.0.0.1:8000/credit' \
--form 'user="3da5f545-89f3-403f-9b8d-fc752d369208"' \
--form 'refund="200"'
```
```
curl --location --request POST 'http://127.0.0.1:8000/debit' \
--form 'user="3da5f545-89f3-403f-9b8d-fc752d369208"' \
--form 'ammount="100"'
```
* UnitTest can be executed with: `php bin/phpunit`
* Code coverage for UnitTest can be executed with: `php -dxdebug.mode=coverage bin/phpunit --do-not-cache-result --debug --coverage-html reports/coverage --coverage-clover reports/coverage/coverage.xml` (if Xdebug is intalled)

* Sample uuids to be used
```
007f13ff-ce26-11e4-8e3d-a0b3cce9bb7e
00805a63-ce26-11e4-8e3d-a0b3cce9bb7e
0b1b6ca9-d178-11e4-8e3d-a0b3cce9bb7e
```
* SQLite local databse can be preview with Valentina Studio or any comparable GUI, if neccessary can be deleted from /var/data.db direcotry and recreted with `php bin/console doctrine:database:create`, `php bin/console make:migration` and ` php bin/console doctrine:migrations:migrate`

#### Assumptions:
* I assumed service is part of dispersed system and authentication is handled by another component which will be integrated with current one in case of going live
* I assumed service is user info agnostic, it gets unique user id from external system
* I assumed user id cannot be email address due to GDPR, so I use UUID instead
* I assumed Thershold can be only positive (it is a limit of expenses, not expenses debit)
* User record in service will be created on every new user UUID comming to the system
* For performance reasons I added Balance table / entity to store total debit / credit info for the user (to not to query for all debits by user and sum them)

#### Final info:
* Message about overspending is printed in terminal on unit tests execution and saved to public/overspending.log on http execution
* Code coverage reports are in reports/coverage, index.html file 