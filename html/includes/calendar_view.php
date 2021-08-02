
<?php
/**
 * UI calendar_view.php
 * Creates UI for the main calendar page, draws input boxes on the left the calendar on the right using a table
 * calls the Calendar.php class to draw the calendar based on User and month/year.
 * 1) Allows a user to interact with the calendar using java script to create and delete events,
 * 2) Draws buttons to approve,not approve and request approval for leaves with a check box for each leave on the calendar.
 * 3) List color index on the left
 * 4) List available user calendars on the left that the user has permission to view
 * 5) allow a user to share/unshare his calendar with other users 
 * 
 * @author Nevo Band
 */
$pastYearsViewLimit = 30;
$futureYearsViewLimit = 2;
$calendar = new Calendar($sqlDataBase);
$rules = new Rules($sqlDataBase);
$helper = new Helper($sqlDataBase);

$messageBox = "";

if(isset($_POST['requestApproval']))
{
	$messageBox = $helper->RequestLeavesApproval(@$_POST['leaveIds'],$loggedUser);
}

if(isset($_POST['approve']))
{
	$messageBox = $helper->ApproveLeaves(@$_POST['leaveIds'],$loggedUser);
}

if(isset($_POST['notApprove']))
{
	$messageBox = $helper->DoNotApproveLeaves(@$_POST['leaveIds'],$loggedUser);
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
                
               print_r($messageBox);
            
        }
           
}

if(isset($_POST['DeleteLeavePopup']))
{
	$helper = new Helper($sqlDataBase);
	$messageBox = $helper->DeleteLeave($_POST['editLeaveId'],$loggedUser);
}

if(isset($_POST['ModifyLeavePopup']))
{
	$helper = new Helper($sqlDataBase);
	$messageBox = $helper->ModifyLeave(
                $_POST['editLeaveId'],
                $_POST['editUserId'], 
                $_POST['editHours'],
                $_POST['editMinutes'],
                $_POST['editLeaveType'],
                $_POST['editLeaveTypeSpecial'],
                $_POST['editDescription'],
                $loggedUser);
}

if(isset($_POST['employeeids']))
{
	$employeeId = $_POST['employeeids'];
}
else
{
	$employeeId = array($loggedUser->getUserId());
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
	else
	{
		$year = Date('Y');
		$month = Date('n');
	}
}

if(isset($_POST['selectShareUser']))
{
	$userToShareWith = @$_POST['shareUser'];
	if($userToShareWith)
	{
                
                $loggedUser->addSharedCalendar($userToShareWith);

	}
}

if(isset($_POST['sharedUsers']))
{
	$unshareUsers = $_POST['sharedUsers'];
	foreach($unshareUsers as $unshareUser)
	{

                $loggedUser->removeSharedCalendar($unshareUser);

	}
}

?>
<form name="calendar" id="calendar" action="index.php?view=calendar"
	method="post">
	<?php
	require_once "includes/create_leave_popup.php";
	require_once "includes/edit_leave_popup.php";
	?>
	<table class="content" height="100%">
		<tr>
			<td class="page_title"></td>
			<td class="page_title"><SPAN id="Title" onselectstart="return false">
					<input type="submit" value="" name="decMonth" class="left_button"><input
					type="submit" value="" name="incMonth" class="right_button"> <?php echo Date('F Y',mktime(0,0,0,$month,1,$year)); ?><input
					type="hidden" value="<?php echo $year;?>" name="yearHidden"><input
					type="hidden" value="<?php echo $month;?>" name="monthHidden"></SPAN>
					<img title="
							<b>Reserving leaves:</b>
							<br>Use the blue arrows to change the month you would like to reserve your leaves for.
							<br>Click on any day of the month or <b>Click + Drag</b> to select days to reserve identical leaves for.
							<br>Once leaves are created check the ones you would like to request supervisor approval for and click on the <b>Request Approval For Selected Leaves</b> button.
							<br><br><b>View another user's calendar:</b>
							<br>To view other user's calendars check the box next to the user's name under Calendars,
							<br>you may view multiple user calendars simultaneously.
							<br><br><b>Share calendar with other users:</b>
							<br>To share calendar with another user select the user from the dropdown list and click <b>Share</b>.
							<br>To unshare a calendar click on <b>(unshare)</b> next to the user name in the list.
							" src="css/images/question.png">
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
                                            </td><td>".$leaveType->getName()."</td></tr>";
					}

					?>
				</table>
				<table width="100%">
                                    <tr>
                                            <td colspan=2 class="col_title">Calendars</td>
                                    </tr>
                                    <tr>
                                            <td colspan=2></td>
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
                                                    
                                                            echo "<tr><td><input type=\"checkbox\" name=\"employeeids[]\" value=".
                                                                    $loggedUser->getUserId().
                                                                    " id=\"calendarchecbox_".
                                                                    $loggedUser->getUserId()."\"";

                                                            if(in_array($loggedUser->getUserId(),$employeeId))
                                                            {
                                                                    echo " CHECKED";
                                                            }
                                                            echo " onclick=\"this.form.submit();\" ></td><td><label for=\"calendarchecbox_".
                                                                    $loggedUser->getUserId().
                                                                    "\">".$loggedUser->getFirstName().
                                                                    " ".$loggedUser->getLastName()."</label></td></tr>";
                                                    }
                                                    if(isset($sharedCalendars))
                                                    {
                                                            foreach($sharedCalendars as $id=>$calendarUser)
                                                            {
                                                                    echo "<tr><td><input type=\"checkbox\" name=\"employeeids[]\" value=".
                                                                            $calendarUser['user_id'].
                                                                            " id=\"calendarchecbox_".
                                                                            $calendarUser['user_id']."\"";

                                                                    if(in_array($calendarUser['user_id'], $employeeId))
                                                                    {
                                                                            echo " CHECKED";
                                                                    }
                                                                    echo " onclick=\"this.form.submit();\"></td><td><label for=\"calendarchecbox_".$calendarUser['user_id']."\">".$calendarUser['first_name']." ".$calendarUser['last_name']."</label></td></tr>";
                                                            }
                                                    }

                                                    ?>
                                                    </table>
                                            </div> <?php

                                                ?></td>

                                    </tr>
                                    <tr>
                                            <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                            <td colspan="2" class="col_title">Share Calendar</td>
                                    </tr>
                                    <tr>
                                            <td class="form_field" valign="top" colspan=2>
                                                    <SELECT name="shareUser">
                                                    <?php

                                                    $allUnsharedUsers = $loggedUser->GetUnsharedUsers();
                                                    if(isset($allUnsharedUsers))
                                                    {
                                                            foreach($allUnsharedUsers as $id=>$user)
                                                            {
                                                                    if($loggedUser->getUserId()!=$user['user_id'])
                                                                    {
                                                                            echo "<option value=".$user['user_id'];
                                                                            if($user['user_id']==$loggedUser->getSupervisorId())
                                                                            {
                                                                                    echo " SELECTED";
                                                                            }
                                                                            echo ">".$user['first_name']." ".$user['last_name']."</option>";
                                                                    }
                                                            }
                                                    }
                                                    ?>
                                        </SELECT> <input class="ui-state-default ui-corner-all" type="submit" name="selectShareUser"
                                                value="Share"><br>
                                                <div id="share_calendar">
                                                    <table width="100%">
                                                    <?php

                                                    
                                                     $sharedUsers = $loggedUser->GetSharedUsers();
                                                    if(isset($sharedUsers))
                                                    {
                                                        foreach($sharedUsers as $id=>$sharedUser)
                                                        {
                                                                echo "<tr>
                                                                <td>".$sharedUser['first_name']." ".$sharedUser['last_name']."</td>
                                                                <td align=\"right\"><div id=hiddenArea><input type=\"checkbox\" id=\"sharedUsers_".$sharedUser['user_id']."\" value=\"".$sharedUser['user_id']."\" name=\"sharedUsers[]\"></div><a class=\"unshare_calendar_link\" href=\"#\" onclick=\"document.getElementById('sharedUsers_".$sharedUser['user_id']."').checked=true;document['calendar'].submit();\">(Unshare)</a></td>
                                                                </tr>";
                                                        }
                                                    }
                                                    ?>
                                                    </table>
                                                </div>
                                        </td>
                                    </tr>

				</table>
			</td>
			<td class="content_bg" valign="top"><?php echo $messageBox; ?> <input class="ui-state-default ui-corner-all"
				type="submit" name="requestApproval"
				value="Request Supervisor Approval"> 
                            <?php
				$checkUserHasEmployees = $loggedUser->GetEmployees();
				if($loggedUser->GetUserPermId() == ADMIN || count($checkUserHasEmployees) > 0)
				{
					echo "<input class=\"ui-state-default ui-corner-all\" type=\"submit\" name=\"approve\" value=\"Approve Selected Leaves\"> ".
                                                "<input class=\"ui-state-default ui-corner-all\" type=\"submit\" name=\"notApprove\" value=\"Do Not Approve Selected Leaves\">";
				}
                            ?> 
                            
                            <SPAN id="Title" onselectstart="return false"> 
                                
                            <?php
                                echo $calendar->Show($month,$year,$employeeId,$loggedUser);
                            ?> 
                                
                            </SPAN>
		
		</tr>
	</table>
</form>
<br>
<br>
<br>
