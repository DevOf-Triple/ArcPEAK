<?php
// Шаблон конфигурации базы данных - если хочется запустить этот проект локально.
// Переименуйте этот файл на db.php и заполните своими значениями:
// Он должен лежать в папке config/
//

$host = 'x.x.x.x';        // адрес сервера БД
$dbname = 'name';       // имя вашей базы данных
$dbuser = 'name';           // пользователь БД
$dbpass = 'pass';               // пароль пользователя БД

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $dbuser,
        $dbpass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {

    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    exit(json_encode([
        'ok' => false,
        'error' => 'Ошибка подключения к базе данных',
    ]));
}