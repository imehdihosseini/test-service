[![Build Status](https://travis-ci.org/coffeetalkh/test-service.svg?branch=master)](https://travis-ci.org/coffeetalkh/test-service)
[![Code Coverage](https://scrutinizer-ci.com/g/coffeetalkh/test-service/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/coffeetalkh/test-service/?branch=master)

# The RESTful API

Laravel based RESTful APO

## How to serve

```
    $ php artisan serve
```

The request will be running on ```localhost:8000```, You can also see All API list with following blew command.

```
    $ php artisan route:list
```

### Result
```
+--------+----------+-------------------------------------+------+----------------------------------------------------+------------+
| Domain | Method   | URI                                 | Name | Action                                             | Middleware |
+--------+----------+-------------------------------------+------+----------------------------------------------------+------------+
|        | GET|HEAD | transactionservice/sum/{id}         |      | App\Http\Controllers\TransactionController@sum     | api        |
|        | GET|HEAD | transactionservice/transaction      |      | App\Http\Controllers\TransactionController@index   | api        |
|        | GET|HEAD | transactionservice/transaction/{id} |      | App\Http\Controllers\TransactionController@show    | api        |
|        | PUT      | transactionservice/transaction/{id} |      | App\Http\Controllers\TransactionController@update  | api        |
|        | DELETE   | transactionservice/transaction/{id} |      | App\Http\Controllers\TransactionController@destroy | api        |
|        | GET|HEAD | transactionservice/types/{id}       |      | App\Http\Controllers\TransactionController@types   | api        |
+--------+----------+-------------------------------------+------+----------------------------------------------------+------------+
```

## Curl request

```
    $ curl -H 'content-type: application/json' -v -X PUT -d '{"amount":"5000","type":"cars","parent_id":"1"}' http://localhost:8000/transactionservice/transaction/:id
    $ curl -H 'content-type: application/json' -v -X DELETE http://localhost:8000transactionservice/transaction/:id
    $ curl -H 'content-type: application/json' -v -X GET http://localhost:8000/transactionservice/transaction/:id
    $ curl -H 'content-type: application/json' -v -X GET http://localhost:8000/transactionservice/types/:id
    $ curl -H 'content-type: application/json' -v -X GET http://localhost:8000/transactionservice/sum/:id
```
