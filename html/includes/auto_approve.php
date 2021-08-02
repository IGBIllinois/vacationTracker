<?php
/**
 * UI auto_approve.php
 * Class used to automatically approve all leaves attached to a confirmation token.
 * The token is recieved via GET and is compared against the database.
 * 
 * @author Nevo Band
 */
$pastYearsViewLimit = 30;
$futureYearsViewLimit = 2;
$calendar = new Calendar($sqlDataBase);
$rules = new Rules($sqlDataBase);
$helper = new Helper($sqlDataBase);

$messageBox = "";

$confirmKey = $_GET['confirmtoken'];

$cofirmKeyLeaves = Auth::getConfirmKeyLeaves($sqlDataBase, $confirmKey);

$leaveIds = array();
if(isset($confirmKeyLeaves))
{
	foreach($confirmKeyLeaves as $id=>$confirmKeyLeave)
	{
		$leaveIds[] = $confirmKeyLeave['leave_id'];
	}
	$loggedUser->LoadUser($confirmKeyLeaves[0]['supervisor_id']);

	if(isset($_GET['autoapprove']))
	{
		echo $helper->ApproveLeaves($leaveIds,$loggedUser);
	}
	if(isset($_GET['autonotapprove']))
	{
		echo $helper->DoNotApproveLeaves($leaveIds,$loggedUser);
	}
}
else
{
	echo "no leaves found";
}

?>
