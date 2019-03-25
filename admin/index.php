<?php
session_start();

if(!isset($_SESSION['connected']) || $_SESSION['connected'] !== true){
    header('location:../login.php');
    exit();
}


include('../config/config.php');

$vue = 'index.phtml';


include('tpl/layout.phtml');