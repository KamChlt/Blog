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

        $sth = $dbh->prepare('SELECT * FROM b_article WHERE a_id = :id');
        $sth->execute(['id'=>$id]);
        $result = $sth->fetch(PDO::FETCH_ASSOC);

        if($result)
        {
            //on a bien un article en base avec cet id

            //on supprime l'image si elle existe sur le disque
            if(file_exists(UPLOADS_DIR.'articles/'.$result['a_picture']))
                unlink(UPLOADS_DIR.'articles/'.$result['a_picture']);

            //On supprime l'article
            $sth = $dbh->prepare('DELETE FROM b_article WHERE a_id = :id');
            $sth->execute(['id'=>$id]);

            //redirection vers la liste des articles (PRG - Post Redirect Get)
            header('Location:listArticle.php');
            exit(); //on arrête le script après redirection pour éviter que PHP ne continu son boulot inutilement !
        }
        else
        {
            //on a pas d'article correspondant ! 

        }
    }
    
}

catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    $messageErreur= 'Une erreur s\'est produite : '.$e->getMessage();
}

include('tpl/layout.phtml');