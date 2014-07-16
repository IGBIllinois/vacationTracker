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
	switch ($_POST['addToUser'])
	{
		case 0:
			//Add leave to all users including a template
			$queryUsersToAdd = "SELECT user_id,percent,start_date FROM users WHERE user_type_id=".$_POST['employeeType']." AND enabled=".ENABLED;
			$usersToAdd = $sqlDataBase->query($queryUsersToAdd);
			array_push($usersToAdd,array("user_id"=>0,"percent"=>100,"start_date"=>"0000-00-00"));
			break;
		case -1:
			//Add template
			$usersToAdd = array(0=> array("user_id"=>0,"percent"=>100,"start_date"=>"0000-00-00"));
			break;
		case -2:
			//Add to all current users without adding to template
			$queryUsersToAdd = "SELECT user_id,percent,start_date FROM users WHERE user_type_id=".$_POST['employeeType']." AND enabled=".ENABLED;
			$usersToAdd = $sqlDataBase->query($queryUsersToAdd);
			break;
		default:
			$queryUsersToAdd = "SELECT user_id,percent,start_date FROM users WHERE user_id=".$_POST['addToUser'];
			$usersToAdd = $sqlDataBase->query($queryUsersToAdd);			
	}

	$message = $helper->AddLeaveHours($_POST['year'],$_POST['leaveType'], $_POST['hoursToAdd'],$_POST['payPeriod'],$usersToAdd, $_POST['addLeavesDescription'],$_POST['hoursAddBegin'],$_POST['addToUser'],$_POST['yearType']);
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
			$queryUsersToAdd = "SELECT user_id,percent,start_date FROM users WHERE user_id=".$_POST['userToViewAddedLeaves'];
		}
		else
		{
			$queryUsersToAdd = "SELECT user_id,percent,start_date FROM users";
		}
		$usersToAdd = $sqlDataBase->query($queryUsersToAdd);
		if($leavesToAdd)
		{
			foreach($leavesToAdd as $leaveToAddId)
			{
				$queryAddedLeaveInfo = "SELECT ah.hours, ah.user_type_id, ah.pay_period_id, ah.leave_type_id, ah.description, ah.year_info_id, ah.begining_of_pay_period, yi.year_type_id FROM added_hours ah, year_info yi WHERE yi.year_info_id = ah.year_info_id AND added_hours_id=".$leaveToAddId;
				$addedLeaveInfo = $sqlDataBase->query($queryAddedLeaveInfo);
				$message = $helper->AddLeaveHours($addedLeaveInfo[0]['year_info_id'],$addedLeaveInfo[0]['leave_type_id'], $addedLeaveInfo[0]['hours'],$addedLeaveInfo[0]['pay_period_id'],$usersToAdd, $addedLeaveInfo[0]['description'],$addedLeaveInfo[0]['begining_of_pay_period'],$_POST['userToViewAddedLeaves'],$addedLeaveInfo[0]['year_type_id']);
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
			include "includes/add_leaves.php";
			?>
			</form>
			</td>
			<td class="content_bg" valign="top"><?php
			echo $message;
			include "includes/added_leaves_list.php";
			?>
			</td>
		</tr>
	</table>
