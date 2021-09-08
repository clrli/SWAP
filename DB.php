<?php
class DB {
    private static function connect() {
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=swap;charset-utf8', 'root', 'asdfjkl;1234');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
    public static function query($query, $params) {
        $statement = self::connect()->prepare($query);
        $statement->execute($params);

        if (explode(' ', $query)[0] == 'SELECT') {
            $data = $statement->fetchAll();
            return $data;
        }
    }
}
?>