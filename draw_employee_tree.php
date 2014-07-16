<?php
/** 
 * Image draw_employee_tree.php
 * This file is used to display the supervisor tree base on users supervisor id columns from the database
 * it will post to screen a PNG image code
 */
error_reporting(E_ALL);
ini_set('display_errors', '1');

function __autoload($class_name) {
    require_once 'classes/' . $class_name . '.php';
}


include "includes/config.php";

//Initialize database
$sqlDataBase= new SQLDataBase('localhost',$sqlDataBase,$sqlUserName,$sqlPassword);


$objTree =  new GDRenderer(30,10,20,50,20,20,20);

$queryUsers = "SELECT user_id, first_name, last_name, supervisor_id, email FROM users";
$users = $sqlDataBase->query($queryUsers);
//print_r($users);

foreach($users as $id => $user)
{

	$firstLastName = " ".$user['first_name']." ".$user['last_name'];

        $objTree->add($user['user_id'],$user['supervisor_id'],$firstLastName,' ', (9*strlen($firstLastName)+2), 18, 'nothing');
	
}


$objTree->setBGColor(array(187, 204, 255));
$objTree->setNodeTitleColor(array(227, 233, 255));
$objTree->setNodeMessageColor(array(227, 233, 255));
$objTree->setLinkColor(array(0, 0, 0));
//$objTree->setNodeLinks(GDRenderer::LINK_BEZIER);
$objTree->setNodeBorder(array(0, 0, 0), 1);
$objTree->setTextTitleColor(array(0, 0, 0));
$objTree->setFTFont('/usr/share/fonts/truetype/msttcorefonts/arial.ttf', 11, 0, GDRenderer::CENTER);
$objTree->stream();
?>
