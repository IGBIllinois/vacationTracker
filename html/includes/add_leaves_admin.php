<?php
/** UI add_leave_admin.php
 * The main content page for the "add leaves" Tab
 * Includes add_leaves.php, added_leaves_list.php
 * 
 * @author Nevo Band
 */
$years=new Years($sqlDataBase);
$helper = new Helper($sqlDataBase);
$message ="";

if(isset($_GET['id']))
{
	$leaveId = $_GET['id'];
}
else
{
	$leaveId = -1;
}
/**
 * Add new leaves using the add leaves form
 */
if(isset($_POST['addLeaveHours']))
{
	$addLeavesRules = new Rules($sqlDataBase);
	if(isset($_POST['resetHours']))
	{
		$resetHours = 1;
	}
	else
	{
		$resetHours = 0;
	}
        $usersToAdd = User::GetUsers($sqlDataBase, $_POST['addToUser'], $_POST['employeeType']);

	$message = $helper->AddLeaveHours($_POST['year'],
                $_POST['leaveType'], 
                $_POST['hoursToAdd'],
                $_POST['payPeriod'],
                $usersToAdd, 
                $_POST['addLeavesDescription'],
                $_POST['hoursAddBegin'],
                $_POST['addToUser'],
                $_POST['yearType']);
}
/**
 * Add new leaves based on an existing template
 */
if(isset($_POST['addNewLeavesToUser']))
{
	if(isset($_POST['addedLeavesCheckBox']))
	{
		$leavesToAdd = $_POST['addedLeavesCheckBox'];

                
		if($_POST['userToViewAddedLeaves'])
		{
			$queryUsersToAdd = "SELECT user_id,percent,start_date FROM users WHERE user_id=:user_id";
                        $params = array("user_id"=>$_POST['userToViewAddedLeaves']);
                        $usersToAdd = User::GetUsers($db, $_POST['userToViewAddedLeaves']);
		}
		else
		{
			$queryUsersToAdd = "SELECT user_id,percent,start_date FROM users";
                        $params = null;
                        $usersToAdd = $loggedUser->GetAllUsers($db, 0);
		}

		if($leavesToAdd)
		{
			foreach($leavesToAdd as $leaveToAddId)
			{

                            $addedLeaveInfo = Leave::GetAddedHours($sqlDataBase, $leaveToAddId);
				$message = $helper->AddLeaveHours($addedLeaveInfo[0]['year_info_id'],
                                        $addedLeaveInfo[0]['leave_type_id'], 
                                        $addedLeaveInfo[0]['hours'],
                                        $addedLeaveInfo[0]['pay_period_id'],
                                        $usersToAdd, 
                                        $addedLeaveInfo[0]['description'],
                                        $addedLeaveInfo[0]['begining_of_pay_period'],
                                        $_POST['userToViewAddedLeaves'],
                                        $addedLeaveInfo[0]['year_type_id']);
			} 
		}
	}
	else
	{
		$message = $helper->MessageBox("Add new leaves","No leave template selected.","error");
	}
}

/**
 * Delete the leaves selected in the checkboxes
 */
if(isset($_POST['deleteSelectedAddedLeaves']))
{
	if(isset($_POST['addedLeavesCheckBox']))
	{
		$addedLeavesToDelete = $_POST['addedLeavesCheckBox'];
		foreach($addedLeavesToDelete as $addedLeave)
		{
			$message = $helper->DeleteAddedHours($addedLeave,$loggedUser);
		}
	}
}
?>
	<table class="content">
		<tr>
			<td class="page_title" width=300></td>
			<td class="page_title"><br>
			</td>
		</tr>
		<tr>
			<td valign="top">
			<form action="index.php?view=adminAddLeaves" method="POST" name="add_leaves">
			<?php
			require_once "includes/add_leaves.php";
			?>
			</form>
			</td>
			<td class="content_bg" valign="top"><?php
			echo $message;
			require_once "includes/added_leaves_list.php";
			?>
			</td>
		</tr>
	</table>
