<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


date_default_timezone_set("America/Chicago");
session_start();
//Load the classes automatically without having to include them
function my_autoloader($class_name) {
    
    require_once '../libs/' . $class_name . '.php';
}
spl_autoload_register('my_autoloader');

require_once '../vendor/autoload.php';

//Load configuration file
require_once "../conf/config.php";

if($debug) {
error_reporting(E_ALL);
}
//Initialize database 
$sqlDataBase= new SQLDataBase('localhost',$sqlDataBase,$sqlUserName,$sqlPassword);

//initialize ldap authentication object
$authen=new Auth($sqlDataBase);
$authen->SetLdapVars($host,$peopleDN,$groupDN,$ssl,$port);
$loggedUser = new User($sqlDataBase);

//Authenticate user with LDAP and existing account
require_once "authenticate.php";