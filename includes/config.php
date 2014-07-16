<?php
/**
 * Utility config.php
 * Main configuration file, contains all passwords and global static variable definitions
 * 
 * @author Nevo Band
 */
/*
 Basic definitions
 */
define("NEW_LEAVE",1);
define("APPROVED",2);
define("WAITING_APPROVAL",3);
define("DELETED",4);
define("NOT_APPROVED",5);

define("ADMIN",1);
define("USER",2);
define("VIEWER",3);

define("ENABLED",1);
define("DISABLED",0);

define("FISCAL_YEAR",25);
define("APPOINTMENT_YEAR",1);

define("AP_HOURS_BLOCK",4);
/*
 Configuration for vacation tracker
 SQL authentication information
 */
$sqlUserName="vtuser";
$sqlPassword="password";
$sqlDataBase="vacationTracker";

/*
 LDAP settings
 */
$host="ldap.server.edu"; //ldap server
$peopleDN="ou=people,dc=department,dc=uiuc,dc=edu"; //people DN
$groupDN="ou=group,dc=department,dc=uiuc,dc=edu"; //group DN
$ssl="0";
$port="389";
$adminGroup="SomeLDAPGroup"; //Some group of admins in ldap
$ldapNetIdParam="uid="; //Ldap Parameter to search for when searching user netid
$searchTerms=array('uid','cn','mail'); //All parameters to search

/*
 Authentication
 */
$tokenTimeOut = 7; //days

