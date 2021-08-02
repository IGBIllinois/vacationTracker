
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

$confirmKey = $_GET['confirmtoken'];

$auth = new Auth($sqlDataBase);
$authenCodeInfo = $auth->GetAuthenCodeInfo($confirmKey);

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
            $messageBox = $helper->DeleteLeave($_POST['editLeaveId'],$loggedUser);
	}

	if(isset($_POST['ModifyLeavePopup']))
	{
            $helper = new Helper($sqlDataBase);
            $messageBox = $helper->ModifyLeave($_POST['editLeaveId'],
                    $_POST['editUserId'], 
                    $_POST['editHours'],
                    $_POST['editMinutes'],
                    $_POST['editLeaveType'],
                    $_POST['editLeaveTypeSpecial'],
                    $_POST['editDescription'],
                    $loggedUser);
	}

	if(isset($_POST['CreateLeavePopup']))
	{
            $helper = new Helper($sqlDataBase);
            if(isset($_POST['leaveDays']))
            {
                    $messageBox = $helper->CreateLeave($_POST['user_id'],
                            $_POST['leaveDays'],
                            $_POST['monthHidden'],
                            $_POST['yearHidden'],
                            $_POST['hours'],
                            $_POST['minutes'],
                            $_POST['leaveType'],
                            $_POST['leaveTypeSpecial'],
                            $_POST['description'], 
                            $loggedUser);
            }
	}
}
else
{
	if(isset($_POST['ModifyLeavePopup']) || isset($_POST['DeleteLeavePopup']) || isset($_POST['CreateLeavePopup']) )
	{
		$messageBox = $helper->MessageBox("(Modify / Delete / Create) Leave",
                        "You must authenticate in order to create, delete or modify a leave.".
                        "<br>Please click on the Authenticate link above to log in.",
                        "error");
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
        
} elseif(isset($_POST['decMonth']))
{
	$month = $_POST['monthHidden'] - 1;
	$year = $_POST['yearHidden'];

	if($month==0)
	{
		$month=12;
		$year = $year - 1;
	}
        
} elseif(isset($_POST['incMonth']))
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
	require_once "includes/create_leave_popup.php";
	require_once "includes/edit_leave_popup.php";
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

                                    $leaveTypes = LeaveType::GetLeaveTypes($sqlDataBase);
                                    foreach($leaveTypes as $leaveType)
                                    {
                                            echo "<tr><td><div id=\"leave_color_box\" style=\"background-color:#".$leaveType->GetColor()."\"></div>
                                    </td><td>".$leaveType->GetName()."</td></tr>";
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
 
                                                        $sharedCalendars = $loggedUser->GetAllViewableUsers();
                                                        
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
                    if($loggedUser->GetUserPermId() == ADMIN || count($checkUserHasEmployees)>0)
                    {
                            echo "<input class=\"ui-state-default ui-corner-all\" type=\"submit\" name=\"approve\" value=\"Approve Selected Leaves\"> "
                                . "<input type=\"submit\" name=\"notApprove\" value=\"Do Not Approve Selected Leaves\"> "
                                . "<input type=\"button\" value=\"Select All\" onclick=\"checkAll(document.calendar.elements['leaveIds[]'])\"> "
                                . "<input type=\"button\" value=\"Select All\" onclick=\"uncheckAll(document.calendar.elements['leaveIds[]'],0)\">";
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
