<?php

namespace Manager;

class User
{
    public static $limit = 10;

    /**
     * Возвращает пользователей старше заданного возраста.
     * @param int $ageFrom
     * @return array
     */

    function getUsers(int $ageFrom): array
    {
        $ageFrom = (int)trim($ageFrom);
        return \Gateway\User::getUsers($ageFrom);
    }

    /**
     * Возвращает пользователей по списку имен.
     * @return array
     */
    public static function getByNames(): array
    {
        $len = (count(is_array($_GET['names']) ? $_GET['names'] : []) * 2) - 1;
        if ($len > 0) {
            $userStatement = str_pad('?', $len, '?,', STR_PAD_LEFT);
            $users = \Gateway\User::user($_GET['names'], $userStatement);
        }
        return $users ?: [];
    }

    /**
     * Добавляет пользователей в базу данных.
     * @param $users
     * @return array
     */
    public function users($users): array
    {
        $ids = [];
        foreach ($users as $user) {
            $usersAdd = \Gateway\User::add($user['name'], $user['lastName'], $user['age']);
            if ($usersAdd) $ids[] = $usersAdd;
        }
        return $ids;
    }
}