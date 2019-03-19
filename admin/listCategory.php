<?php

include('../config/config.php');

$vue='listCategory.phtml';

try{

    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sth = $dbh->prepare('SELECT c1.c_id, c1.c_title, c2.c_title as parent, COUNT(a.a_id) as articles  FROM b_categorie c1 LEFT JOIN b_categorie c2 ON c1.c_parent=c2.c_id LEFT JOIN b_article a ON c1.c_id = a.a_categorie GROUP BY c1.c_id ORDER BY c1.c_parent');
    $sth->execute();
   
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);

}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    $messageErreur= 'Une erreur s\'est produite : '.$e->getMessage();
}

include('tpl/layout.phtml');