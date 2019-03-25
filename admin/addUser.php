<?php

include('../config/config.php');

$vue = 'addUser.phtml'; 

$id=null;
$prenom='';
$nom='';
$mail='';
$mdp='';
$mdpconf='';
$roleUtilisateur = 'ROLE_AUTHOR';
$errorForm =[];

try
{
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    //récupération des données
    if(array_key_exists('prenom',$_POST)){
    
        var_dump($_POST);
        
        $prenom=$_POST['prenom'];
        $nom=$_POST['nom'];
        $mail=$_POST['mail'];
        $mdp=$_POST['password'];
        $mdpconf=$_POST['passwordconf'];
        $roleUtilisateur=$_POST['role'];
        
        //le formulaire est posté
        if($mail == '')
           $errorForm[]= 'L\'email ne peut-être vide !';
           
        //Vérifier que le mot de passe et confirmation soit identique   
        if($mdp != $mdpconf || $mdp == '')
            $errorForm[] = 'Le mot de passe ou sa confimation ne sont pas corrects !';   
        
        /** On vérifie qu'un utilisateur n'est pas déjà dans la base de données*/
        $sth = $dbh->prepare('SELECT u_email FROM b_user WHERE u_email = :mail');
        $sth->bindValue('mail',$mail,PDO::PARAM_STR);
        $sth->execute();
        $user = $sth->fetch(PDO::FETCH_ASSOC);
        if($user != false)
            $errorForm[] = 'Un utilisateur existe déjà avec cet email.';
            
        /** Si j'ai pas d'erreur j'insert dans la bdd */
        if(count($errorForm) == 0)
        {
            //HAsh du mot de passe
            $mdp = password_hash($mdp,PASSWORD_DEFAULT);
            
            $sth = $dbh->prepare('INSERT INTO b_user(u_id,u_firstname,u_lastname,u_email,u_password,u_role)
                                  VALUES (NULL,:prenom,:nom,:mail,:password,:role)');
            $sth->bindValue('prenom',$prenom,PDO::PARAM_STR);
            $sth->bindValue('nom',$nom,PDO::PARAM_STR);
            $sth->bindValue('mail',$mail,PDO::PARAM_STR);
            $sth->bindvalue('password',$mdp,PDO::PARAM_STR);
            $sth->bindvalue('role',$roleUtilisateur,PDO::PARAM_STR);
            
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
