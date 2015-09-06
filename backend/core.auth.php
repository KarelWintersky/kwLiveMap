<?php
/**
 * User: Arris
 * Date: 25.08.15, time: 1:30
 */

function auth_CanIEdit()
{
    return true;
}

/**
 * Проверяет наличие пользователя с таким емейлом в системе
 * @param $dbh      - хэндлер соединения с базой
 * @param $email    - емейл
 * @return mixed    - возвращает количество пользователей с таким емейлом в системе: 0 или 1
 */
function auth_getUsersByEmail($dbh, $email)
{
    try {
        $request = "SELECT count(id) AS userscount from lme_list_users WHERE email = :email";

        $sth = $dbh->prepare($request);

        $sth->execute(array(
                ':email'    =>  $email
            ));

        $row = $sth->fetch(PDO::FETCH_ASSOC);
        $userscount = $row['userscount'];
    }
    catch (PDOException $e) {
        die(__FUNCTION__.$e->getMessage());
    }
    return $userscount;
}

function auth_tryRegisterUser($dbh, $data)
{
    try {
        $request = "INSERT INTO lme_list_users
        (email, password, regip, regdate, username)
        VALUES (:email, :password, :regip, :regdate, :username)
        ";

        $sth = $dbh->prepare($request);
        $sth->execute($data);

    }catch (PDOException $e){
        die(__FUNCTION__.$e->getMessage() . '<br>Request = '. $request);
    }
    return $dbh->lastInsertId();
}