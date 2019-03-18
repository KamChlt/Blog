<?php

include('../config/config.php');

$vue='';

try
{
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if(array_key_exists('id',$_GET))
    {
        $id = $_GET['id'];

        $sth = $dbh->prepare('SELECT * FROM b_user WHERE u_id = :id');
        $sth->execute(['id'=>$id]);
        $user = $sth->fetch(PDO::FETCH_ASSOC);

        if($user)
        {
                $sth = $dbh->prepare('DELETE FROM b_user WHERE u_id = :id');
                $sth->execute(['id'=>$id]);
                
                header('Location:listUsers.php');
                exit(); 
        }
        else
        {
            //On a pas d'utilisateur correspondant
        }
                    
    }

}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    $messageErreur= 'Une erreur s\'est produite : '.$e->getMessage();
}

include('tpl/layout.phtml');