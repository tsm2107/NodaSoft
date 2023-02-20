<?php

namespace Gateway;

use PDO;

class User
{
    /**
     * @var PDO
     */
    public static $instance;

    /**
     * Реализация singleton
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (is_null(self::$instance)) {
            $dsn = 'mysql:dbname=db;host=127.0.0.1';
            $user = 'dbuser';
            $password = 'dbpass';
            self::$instance = new PDO($dsn, $user, $password);
        }

        return self::$instance;
    }

    /**
     * Возвращает список пользователей старше заданного возраста.
     * @param int $ageFrom
     * @return array
     */
    public static function getUsers(int $ageFrom): array
    {
        $stmt = self::getInstance()->prepare("SELECT id, name, lastName, age FROM Users
WHERE age > :age LIMIT " . \Manager\User::$limit);
        $stmt->execute(['age' => $ageFrom]);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }

    /**
     * Возвращает пользователей по имени.
     * @param array $name
     * @param string $statement
     * @return array
     */
    public static function user(array $name, string $statement): array
    {
        $stmt = self::getInstance()->prepare("SELECT id, name, lastName, age FROM Users WHERE name IN (" . $statement . ")");
        $stmt->execute($name);
        $usersByName = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $usersByName;
    }

    /**
     * Добавляет пользователя в базу данных.
     * @param string $name
     * @param string $lastName
     * @param int $age
     * @return string
     */
    public static function add(string $name, string $lastName, int $age): int
    {
        try {
            $sth = self::getInstance()->beginTransaction();
            $sth->prepare("INSERT INTO Users (name, lastName, age) VALUES (:name, :age, :lastName)");
            $sth->execute(['name' => $name, 'age' => $age, 'lastName' => $lastName]);
            $sth->commit();
            return $sth->lastInsertId();
        } catch (\Exception $e) {
            $sth->rollBack();
            return 0;
        }

    }
}