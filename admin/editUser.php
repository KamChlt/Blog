<?php

include('../config/config.php');

$vue='editUser.phtml';

$id=null;
$prenom='';
$nom='';
$mail='';
$mdp='';
$mdpconf='';
$roleUtilisateur = 'ROLE_AUTHOR';

try
{
    $dbh = new PDO(DB_SGBD.':host='.DB_SGBD_URL.';dbname='.DB_DATABASE.';charset='.DB_CHARSET, DB_USER, DB_PASSWORD);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if(array_key_exists('id',$_GET))
    {
        $id = $_GET['id'];
        
        /** On recherche l'article dans la base de données */
        $sth = $dbh->prepare('SELECT * FROM b_user WHERE u_id = :id');
        $sth->bindValue('id',$id,PDO::PARAM_INT);
        $sth->execute();
        $user = $sth->fetch(PDO::FETCH_ASSOC);
        
        $prenom=$user['u_firstname'];
        $nom=$user['u_lastname'];
        $mail=$user['u_email'];
        $roleUtilisateur=$user['u_role'];
    }
    
    /**S'il a des données en entrée */
    if(array_key_exists('id',$_POST))
    {
        var_dump($_POST);
        $errorForm = []; //Pas d'erreur pour le moment sur les données

        /* Récupération des données de l'article */
        $id = $_POST['id'];
        $prenom=trim($_POST['prenom']);
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
        $user = $sth->fetch(PDO::FETCH_ASSOC);
        if($user != false)
            $errorForm[] = 'Un utilisateur existe déjà avec cet email.';
            
        if(count($errorForm) == 0)
        {
            if($mdp != '')
            {
                $mdp = password_hash($mdp,PASSWORD_DEFAULT);
                $sth = $dbh->prepare('UPDATE b_user SET
                u_id=:id,u_firstname = :prenom,u_lastname=:nom,u_email=:mail,u_password=:password,u_role=:role 
                WHERE u_id=:id');
                $sth->bindValue('password',$mdp,PDO::PARAM_STR);
            }
            else
            {
                $sth = $dbh->prepare('UPDATE b_user SET 
                u_id=:id,u_firstname = :prenom,u_lastname=:nom,u_email=:mail,u_role=:role
                WHERE u_id=:id');
            }                          
                $sth->bindvalue('id',$id,PDO::PARAM_INT);
                $sth->bindValue('prenom',$prenom,PDO::PARAM_STR);
                $sth->bindValue('nom',$nom,PDO::PARAM_STR);
                $sth->bindValue('mail',$mail,PDO::PARAM_STR);
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