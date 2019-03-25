<?php

include('../config/config.php');

$vue='editCategory.phtml';

$titleCategory='';
$catParent='';

try{

    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    //Récupération des catégories parent
    $sth = $dbh->prepare('SELECT * FROM b_categorie');
    $sth->execute();
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);
    
    //récupération des données(titre et catégorie parent)
    if(array_key_exists('id',$_GET))
    {
        $id=$_GET['id'];
        
        $sth = $dbh->prepare('SELECT * FROM b_categorie WHERE c_id = :id');
        $sth->bindValue('id',$id,PDO::PARAM_INT);
        $sth->execute();
        $categorie = $sth->fetch(PDO::FETCH_ASSOC);
        
        $titleCategory = $categorie['c_title'];
        $catParent = $categorie['c_parent'];

    }  
    
     if(array_key_exists('titre',$_POST))
    {
        var_dump($_POST);
        $errorForm = [];

        $id = $_POST['id']; //on récupère l'id de la catégorie

        /* Récupération des données de la catégorie */
        $titleCategory = trim($_POST['titre']);
        $catParent = $_POST['categorie']; 
        
        //le formulaire est posté
        if($titleCategory == '')
            $errorForm[] = 'Le titre ne peut-être vide !';

        /** Si j'ai pas d'erreur j'insert dans la bdd */
        if(count($errorForm) == 0)
        {
            //Préparation requête
            $sth = $dbh->prepare('UPDATE b_categorie SET c_title = :titre, c_parent=:categorie WHERE c_id=:id');

            //Liage (bind) des valeurs
            $sth->bindValue('id',$id,PDO::PARAM_INT);
            $sth->bindValue('titre',$titleCategory,PDO::PARAM_STR);
            $sth->bindValue('categorie',$catParent,PDO::PARAM_STR);
            $sth->execute();

            //redirection vers la liste des articles
            header('Location:listCategory.php');
            exit(); //on arrête le script après redirection pour éviter que PHP ne continu son boulot inutilement !
        }
    }    
    
}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    $messageErreur= 'Une erreur s\'est produite : '.$e->getMessage();
}

include('tpl/layout.phtml');