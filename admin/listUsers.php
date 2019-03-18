<?php

include('../config/config.php');

$vue='listUsers.phtml';


try
{
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    
        $sth = $dbh->prepare('SELECT *
                              FROM `b_user`');

        $sth->execute();

        $users = $sth->fetchAll(PDO::FETCH_ASSOC);
        

}
catch(PDOException $e)
{
    echo 'Une erreur s\'est produite : '.$e->getMessage();
}

include('tpl/layout.phtml');
