<?php

include('../config/config.php');

$vue = 'addArticle.phtml'; 

$dateArticle="";
$timeArticle="";
$titre="";
$contenu="";
$categorie="";
$id="";

try
{
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    /** On va récupérer les catégories dans la bdd*/

    $sth = $dbh->prepare('SELECT * FROM b_categorie');
    $sth->execute();
    $categories = $sth->fetchAll(PDO::FETCH_ASSOC);
    

    if(array_key_exists('titre',$_POST)){
        
        var_dump($_POST);
        var_dump($_FILES);
        
        $errorForm=false;
        
        /* Récupération des données de l'article*/
        $titre=$_POST['titre'];
        $categories=$_POST['categorie'];
        $contenu=$_POST['contenu'];
        $picture=null;
        $auteur=$_POST['auteur'];
        $dateArticle = $_POST['date'];
        $timeArticle = $_POST['heure'];
        $datePubli = new DateTime($dateArticle.' '.$timeArticle);
        $dateCrea = new DateTime();
        
        
        if($titre == ""){
            $errorForm='Le titre ne peut-être vide !';
        }
        

        if($datePubli===false || $datePubli < $dateCrea){
            $errorForm='La date de publication est erronée ou antérieur à la date du jour !';
        }
        
        
         /** Récupérer l'image et la déplacer */
        if ((isset($_FILES['image'])&&($_FILES['image']['error'] == UPLOAD_ERR_OK))) {     
            $picture= $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], UPLOADS_DIR.'articles/'.$_FILES['image']['name']); 
        }
        else
        {
            $errorForm[] = 'Une erreur s\'est produite lors de l\'upload de l\'image !';
        }
        
        

        /** Si j'ai pas d'erreur j'insert dans la bdd */
        if($errorForm === false)
        {
            echo $datePubli->format('Y-m-d H:i:s');
            
            $sth = $dbh->prepare('INSERT INTO b_article(a_id,a_title,a_date_published,a_date_created,a_content,a_picture,a_categorie,a_author)
                                VALUES(null,:titre,:datePubli,:dateCrea,:contenu,:image,:categorie,:auteur)');
            $sth->bindValue('titre',$titre,PDO::PARAM_STR);
            $sth->bindValue('categorie',$categories,PDO::PARAM_STR);
            $sth->bindValue('contenu',$contenu,PDO::PARAM_STR);
            $sth->bindvalue('image',$picture,PDO::PARAM_STR);
            $sth->bindValue('auteur',$auteur,PDO::PARAM_STR);
            $sth->bindValue('datePubli',$datePubli->format('Y-m-d H:i:s'));
            $sth->bindValue('dateCrea',$dateCrea->format('Y-m-d H:i:s'));
            
            $sth->execute();
    
            //redirection vers la liste
            header('Location:listArticle.php');
        }
        
    }
    
}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    $messageErreur= 'Une erreur s\'est produite : '.$e->getMessage();
}

include('tpl/layout.phtml');