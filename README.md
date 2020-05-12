### Mysql Database PHP Library

- command
```
$: composer require wupz/database
```

- example
```php
include 'path/vendor/autoload.php';
$db = new wuweiit\Database(['host' => '127.0.0.1', 'database' => 'example', 'charset' => 'utf8', 'user' => 'root', 'password' => 'example', 'prefix' => 'dp_']);
$users = $db->table('users')->get();
```
