<?php

include('../config/config.php');

$vue = 'addCategory.phtml'; 

$id=null;
$titleCategory = '';
$catParent = null;

try
{
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sth = $dbh->prepare('SELECT * FROM b_categorie');
    $sth->execute();
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);
    
    //récupération des données
    if(array_key_exists('titre',$_POST)){

        $errorForm =false;
        
        $titleCategory=$_POST['titre'];
        $catParent=$_POST['categorie'];
        
        //le formulaire est posté
        if($titleCategory == '')
           $errorForm= 'Le titre ne peut être vide !';
        
        /** Si j'ai pas d'erreur j'insert dans la bdd */
        if($errorForm === false)
        {
            if($catParent=='')
                $catParent = null;
            
            $sth = $dbh->prepare('INSERT INTO b_categorie(c_id,c_title,c_parent)
                                  VALUES (NULL,:titre,:parent)');
            $sth->bindValue('titre',$titleCategory,PDO::PARAM_STR);
            $sth->bindValue('parent',$catParent,PDO::PARAM_STR);
            
            $sth->execute();
    
            //redirection vers la liste
            header('Location:listCategory.php');
            exit();
        }   
        
    }
}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    $messageErreur= 'Une erreur s\'est produite : '.$e->getMessage();
}

include('tpl/layout.phtml');