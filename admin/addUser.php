<?php

include('../config/config.php');

$vue = 'addUser.phtml'; 

$id=null;
$prenom='';
$nom='';
$mail='';
$mdp='';
$id='';

try
{
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    //récupération des données
    if(array_key_exists('prenom',$_POST)){
    
        var_dump($_POST);
        $errorForm =false;
        
        $prenom=$_POST['prenom'];
        $nom=$_POST['nom'];
        $mail=$_POST['mail'];
        $mdp=$_POST['password'];
        
        //le formulaire est posté
        if($mail == '')
           $errorForm= 'L\'email ne peut-être vide !';
        
        /** Si j'ai pas d'erreur j'insert dans la bdd */
        if($errorForm === false)
        {
            $sth = $dbh->prepare('INSERT INTO b_user(u_id,u_firstname,u_lastname,u_email,u_password)
                                  VALUES (NULL,:prenom,:nom,:mail,:password)');
            $sth->bindValue('prenom',$prenom,PDO::PARAM_STR);
            $sth->bindValue('nom',$nom,PDO::PARAM_STR);
            $sth->bindValue('mail',$mail,PDO::PARAM_STR);
            $sth->bindvalue('password',$mdp,PDO::PARAM_STR);
            
            $sth->execute();
    
            //redirection vers la liste
            header('Location:listUsers.php');
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
