<?php

include('../config/config.php');
include('../lib/bdd.lib.php');

$vue='editArticle.phtml';

$dateArticle='';
$timeArticle='';
$categorie='';
$result='';

$pictureDisplay = false;

try{

    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sth = $dbh->prepare('SELECT * FROM b_categorie');
    $sth->execute();
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);
    
    if(array_key_exists('id',$_GET))
    {
        $id=$_GET['id'];
        
        $sth = $dbh->prepare('SELECT * FROM b_article WHERE a_id = :id');
        $sth->bindValue('id',$id,PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetch(PDO::FETCH_ASSOC);
        
        $titre = $result['a_title'];
        $categorieArticle = $result['a_categorie'];
        $contenu = $result['a_content'];
        $picture = $result['a_picture'];
        $auteur = $result['a_author'];
        $datePubli = new DateTime($result['a_date_published']);
    }   
    
    if(array_key_exists('titre',$_POST))
    {
        var_dump($_POST);
        $errorForm = [];

        $id = $_POST['id']; //on récupère l'id de l'article

        /* Récupération des données de l'article */
        $titre = trim($_POST['titre']);
        $dateArticle = $_POST['date'];
        $timeArticle = $_POST['heure'];
        $datePubli = new DateTime($dateArticle.' '.$timeArticle);
        $categorieArticle = $_POST['categorie'];
        $contenu = trim($_POST['contenu']);
        $auteur = $_POST['auteur'];
        $picture = isset($_POST['oldPicture'])?$_POST['oldPicture']:null; 
        
        //le formulaire est posté
        if($titre == '')
            $errorForm[] = 'Le titre ne peut-être vide !';

        if($datePubli===false)
            $errorForm[] = 'La date de publication est erronée ou antérieur à la date du jour !';
    
        if($_FILES['image']["tmp_name"]!= '')
        {
            $tmpNewPicture = uploadFile('image','articles');
            if(!$tmpNewPicture)
                $errorForm[] = 'Erreur !';
            else
            {
                //On supprime l'ancienne image
                delFile(UPLOADS_DIR.'articles/'.$picture);
                
                $picture = $tmpNewPicture;
               
            }
        }
        
        /** Si j'ai pas d'erreur j'insert dans la bdd */
        if(count($errorForm) == 0)
        {
            //Préparation requête
            $sth = $dbh->prepare('UPDATE b_article SET a_title = :titre ,a_date_published=:datePublished,
            a_content=:contenu,a_picture=:image,a_categorie=:categorie WHERE a_id=:id');

            //Liage (bind) des valeurs
            $sth->bindValue('id',$id,PDO::PARAM_INT);
            $sth->bindValue('titre',$titre,PDO::PARAM_STR);
            $sth->bindValue('datePublished',$datePubli->format('Y-m-d H:i:s'));
            $sth->bindValue('contenu',$contenu,PDO::PARAM_STR);
            $sth->bindValue('image',$picture,PDO::PARAM_STR);
            $sth->bindValue('categorie',$categorieArticle,PDO::PARAM_INT);
            $sth->execute();

            //redirection vers la liste des articles
            header('Location:listArticle.php');
            exit(); //on arrête le script après redirection pour éviter que PHP ne continu son boulot inutilement !
        }
        
        
    }
    
    if(file_exists(UPLOADS_DIR.'articles/'.$picture) && !is_dir(UPLOADS_DIR.'articles/'.$picture))
    {
        $pictureDisplay = true;
    }
}

catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    $messageErreur= 'Une erreur s\'est produite : '.$e->getMessage();
}

include('tpl/layout.phtml');