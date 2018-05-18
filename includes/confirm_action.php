<script src="js/leave-selection.js" type="text/javascript"></script>
<?php
/**
 * UI confirm_action.php
 * Page which allows limited interaction with the calendar based on a confirmation token.
 * This is mainly used to allow users to log into the calendar and (approve/not approve) leaves 
 * without having to input their user information.
 * 
 * @author Nevo Band
 */
$pastYearsViewLimit = 30;
$futureYearsViewLimit = 2;
$calendar = new Calendar($sqlDataBase);
$rules = new Rules($sqlDataBase);
$helper = new Helper($sqlDataBase);

$messageBox = "";

$confirmKey = mysqli_real_escape_string($sqlDataBase->getLink(),$_GET['confirmtoken']);
$queryAuthenCodeInfo = "SELECT ak.status_id,ak.leave_id,ak.date_created,li.user_id,u.supervisor_id,u.netid, MONTH(li.date) as month, YEAR(li.date) as year FROM authen_key ak, leave_info li, users u WHERE ak.confirm_key=\"".$confirmKey."\" AND u.user_id=li.user_id AND li.leave_id=ak.leave_id";
$authenCodeInfo = $sqlDataBase->query($queryAuthenCodeInfo);
if(isset($authenCodeInfo))
{
	if($loggedUser->getUserId()==0)
	{
		$loggedUser->LoadUser($authenCodeInfo[0]['supervisor_id']);
		$authenticated = false;
	}
	else
	{
		$authenticated = true;
	}
	$employeeId = array($authenCodeInfo[0]['user_id']);
	$month = $authenCodeInfo[0]['month'];
	$year = $authenCodeInfo[0]['year'];
}

if($authenticated)
{
	if(isset($_POST['DeleteLeavePopup']))
	{
		$helper = new Helper($sqlDataBase);
		$messageBox = $helper->DeleteLeave(mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['editLeaveId']),$loggedUser);
	}

	if(isset($_POST['ModifyLeavePopup']))
	{
		$helper = new Helper($sqlDataBase);
		$messageBox = $helper->ModifyLeave(mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['editLeaveId']),mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['editUserId']), mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['editHours']),mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['editMinutes']),mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['editLeaveType']),mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['editLeaveTypeSpecial']),mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['editDescription']),$loggedUser);
	}

	if(isset($_POST['CreateLeavePopup']))
	{
            //echo("CREATELEAVEPOPUP");
		$helper = new Helper($sqlDataBase);
		if(isset($_POST['leaveDays']))
		{
			$messageBox = $helper->CreateLeave(mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['user_id']),$_POST['leaveDays'],mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['monthHidden']),mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['yearHidden']),mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['hours']),mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['minutes']),mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['leaveType']),mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['leaveTypeSpecial']),mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['description']), $loggedUser);
		}
	}
}
else
{
	if(isset($_POST['ModifyLeavePopup']) || isset($_POST['DeleteLeavePopup']) || isset($_POST['CreateLeavePopup']) )
	{
		$messageBox = $helper->MessageBox("(Modify / Delete / Create) Leave","You must authenticate in order to create, delete or modify a leave.<br>Please click on the Authenticate link above to log in.","error");
	}
}

if(isset($_POST['approve']))
{
	$messageBox = $helper->ApproveLeaves(@$_POST['leaveIds'],$loggedUser);
}

if(isset($_POST['notApprove']))
{
	$messageBox = $helper->DoNotApproveLeaves(@$_POST['leaveIds'],$loggedUser);
}

if(isset($_POST['employeeids']))
{
	$employeeId = $_POST['employeeids'];
}

if(isset($_POST['submitCalendarFilters']))
{
	$year = $_POST['yearHidden'];
	$month = $_POST['monthHidden'];
}elseif(isset($_POST['decMonth']))
{
	$month = $_POST['monthHidden'] - 1;
	$year = $_POST['yearHidden'];

	if($month==0)
	{
		$month=12;
		$year = $year - 1;
	}
}elseif(isset($_POST['incMonth']))
{
	$month = $_POST['monthHidden'] + 1;
	$year = $_POST['yearHidden'];

	if($month==13)
	{
		$month=1;
		$year = $year + 1;
	}
}
else
{
	if(isset($_POST['yearHidden']) && isset($_POST['monthHidden']))
	{
		$year = $_POST['yearHidden'];
		$month = $_POST['monthHidden'];
	}
}

?>
<form name="calendar"
	action="index.php?view=calendar&confirmtoken=<?php echo $_GET['confirmtoken']; ?>"
	method="post">
	<?php
	include "includes/create_leave_popup.php";
	include "includes/edit_leave_popup.php";
	?>
	<a
		href="index.php?view=calendar&confirmtoken=<?php echo $_GET['confirmtoken']; ?>&logout=1"><?php echo (isset($_SESSION['vacation_user_id']))?"logout":"Authenticate"; ?>
	</a>
	<table class="content" height="100%">
		<tr>
			<td class="page_title"></td>
			<td class="page_title"><SPAN id="Title" onselectstart="return false">
					<input class="ui-state-default ui-corner-all" type="submit" value="" name="decMonth" class="left_button"><input
					class="ui-state-default ui-corner-all" type="submit" value="" name="incMonth" class="right_button"> <?php echo Date('F Y',mktime(0,0,0,$month,1,$year)); ?><input
					type="hidden" value="<?php echo $year;?>" name="yearHidden"><input
					type="hidden" value="<?php echo $month;?>" name="monthHidden"> </SPAN>
			</td>
		</tr>
		<tr>
			<td width="200" valign="top">
				<table class="calendar_days" width="100%">
					<tr>
						<td colspan="2" class="col_title">Color Index</td>
					</tr>
					<?php
					$queryLeaveTypes = "SELECT name, calendar_color FROM leave_type";
					$leaveTypes = $sqlDataBase->query($queryLeaveTypes);
					foreach($leaveTypes as $id=>$leaveType)
					{
						echo "<tr><td><div id=\"leave_color_box\" style=\"background-color:#".$leaveType['calendar_color']."\"></div>
	                                </td><td>".$leaveType['name']."</td></tr>";
					}

					?>
				</table>
				<table width="100%">
					<tr>
						<td colspan=2 class="col_title">Calendars</td>
					</tr>
					<tr>
						<td colspan=2>
							<div id="view_calendar">
								<table width="100%">
								<?php
								if($loggedUser->GetUserPermId()==ADMIN)
								{
									$sharedCalendars = $loggedUser->GetAllEnabledUsers();
								}else
								{
									$querySharedCalendars = "SELECT u.user_id, u.first_name, u.last_name
								FROM users u LEFT JOIN shared_calendars sc 
								ON u.user_id = sc.owner_id WHERE sc.viewer_id = ".$loggedUser->getUserId()." OR (u.supervisor_id=".$loggedUser->getUserId().")";
									$sharedCalendars = $sqlDataBase->query($querySharedCalendars);
									echo "<tr><td><input type=\"checkbox\" name=\"employeeids[]\" value=".$loggedUser->getUserId();
									if(in_array($loggedUser->getUserId(),$employeeId))
									{
										echo " CHECKED";
									}
									echo "></td><td>".$loggedUser->getFirstName()." ".$loggedUser->getLastName()."</td></tr>";
								}
								if(isset($sharedCalendars))
								{
									foreach($sharedCalendars as $id=>$calendarUser)
									{
										echo "<tr><td><input type=\"checkbox\" name=\"employeeids[]\" value=".$calendarUser['user_id'];
										if(in_array($calendarUser['user_id'], $employeeId))
										{
											echo " CHECKED";
										}
										echo "></td><td>".$calendarUser['first_name']." ".$calendarUser['last_name']."</td></tr>";
									}
								}
								?>
								</table>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<center>
								<input class="ui-state-default ui-corner-all" type="submit" value="View Selected Calendars"
									name="submitCalendarFilters">
								<center>
						
						</td>
					</tr>
				</table>
			</td>
			<td class="content_bg" valign="top"><?php echo $messageBox; ?> <?php
			$checkUserHasEmployees = $loggedUser->GetEmployees();
			if(isset($checkUserHasEmployees))
			{
				echo "<input class=\"ui-state-default ui-corner-all\" type=\"submit\" name=\"approve\" value=\"Approve Selected Leaves\"> <input type=\"submit\" name=\"notApprove\" value=\"Do Not Approve Selected Leaves\"> <input type=\"button\" value=\"Select All\" onclick=\"checkAll(document.calendar.elements['leaveIds[]'])\"> <input type=\"button\" value=\"Select All\" onclick=\"uncheckAll(document.calendar.elements['leaveIds[]'],0)\">";
			}
			?> <SPAN id="Title" onselectstart="return false"> <?php
			echo $calendar->Show($month,$year,$employeeId,$loggedUser);
			?> </SPAN>
		
		</tr>
	</table>
</form>
<br>
<br>
<br>
