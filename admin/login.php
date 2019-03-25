<?php

session_start();

include('../config/config.php');


$mail='';
$mdp='';
$errorForm=[];

try{
    
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if(array_key_exists('email',$_POST))
    {
        var_dump($_POST);
        
        $mail=$_POST['email'];
        $mdp=$_POST['motdepasse'];
        
        if($mail == '')
           echo ' L\'email ne peut-être vide ! '; 
        
        //On vérifie que l'utilisateur existe en base (email)
        
        $sth = $dbh->prepare('SELECT u_email FROM b_user WHERE u_email = :email');
        $sth->bindValue('email',$mail,PDO::PARAM_STR);
        $sth->execute();
        $user = $sth->fetch(PDO::FETCH_ASSOC);
        
        if($user == false)
            echo ' Cet Email ne correspond a aucun utilisateur ! ';
            
        //Comparaison mot de passe avec celui en base

        if(password_verify($mdp, $user['u_password']))
        {
                $_SESSION['connected'] = true;
                $_SESSION['user'] = ['id'=>,'name'=>...,'role'=>...];
        }    
    
        
        /*if ($isPasswordCorrect){
            session_start();
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $mail;
            echo 'Vous êtes connecté !';
        }
        else {
        echo 'Mauvais identifiant ou mot de passe !';
        } */ 
                
        //on compare ensuite sont mot de passe avec celui en base (password_verify())
        //si tout est ok on créer 2 index dans $_SESSION - $_SESSION['connected'] = true; $_SESSION['user'] = ['id'=>...,'name'=>...,'role'=>...];
        //sinon on affiche de nouveau le formulaire avec un message d'erreur (Impossible de se connecter ! Vérfier login + mot de passe)
    }
    
}
catch(PDOException $e)
{
    $vue = 'erreur.phtml';
    $messageErreur= 'Une erreur s\'est produite : '.$e->getMessage();
}

include('tpl/login.phtml');
