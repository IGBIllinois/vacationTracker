<?php
/**
 * NOT USED YET
 * UI report.php
 * Creates a UI which allows the user to export the leave reports for a user into excel.
 * 
 * @author Nevo Band
 * @todo configure the report to query the correct tables once the user is satisfied with the current state of the program and there are not more major database changes.
 */
if(isset($_POST['userReport']))
{
	$userToReport = $_POST['userReport'];
}
else
{
	$userToReport = $loggedUser->getUserId();
}

if(isset($_POST['genReport']))
{
	$startDate = $_POST['dob1'];
	$endDate = $_POST['dob2'];

	list($startMonth,$startDay, $startYear) = explode("-",$startDate);
        list($endMonth,$endDay,$endYear) = explode("-",$endDate);

        $sqlStartDate = Date("Y-m-d",mktime(0,0,0,$startMonth,$startDay,$startYear));
 	$sqlEndDate = Date("Y-m-d",mktime(0,0,0,$endMonth,$endDay,$endYear));
	
        $queryCountLeaveDays = "SELECT SUM(li.leave_hours) as sum_hours_used, lt.name FROM leave_info li, leave_type lt WHERE lt.leave_type_id = li.leave_type_id AND li.user_id=".$userToReport." AND li.date>=\"".$sqlStartDate."\" AND li.date<=\"".$sqlEndDate."\" GROUP BY li.leave_type_id";
	$countLeaveHours = $sqlDataBase->query($queryCountLeaveDays);
	
	$queryLeaveDays = "SELECT lt.name, li.date,li.leave_hours, TIME_FORMAT(SEC_TO_TIME(li.time), '%kh %im') as time FROM leave_info li, leave_type lt WHERE lt.leave_type_id=li.leave_type_id AND user_id=".$userToReport." AND li.date>=\"".$sqlStartDate."\" AND li.date<=\"".$sqlEndDate."\" ORDER BY li.date ASC";
	$leaveDays = $sqlDataBase->query($queryLeaveDays);
	
}
else
{
	$startDate = Date('m-d-Y');
	$endDate = Date('m-d-Y');
}


?>

<form action="index.php?view=report" method="POST">
<table class="content">
<tr>
	<td class="page_title" width="200">
	</td>
	<td class="page_title">
	<b>
        Leaves Details: <?php echo $startDate." ".$endDate; ?>
	</b> 
        </td>
</tr>
<tr>
	<td valign="top">	
	<table>
	<tr>
		<td class="report">
		User:
		</td>
		<td class="report">
		<select name="userReport">
		<?php
		if($loggedUser->GetUserPermId()==ADMIN)
		{
			$employees = $loggedUser->GetAllEnabledUsers();
		}else
		{
			$employees = $loggedUser->GetEmployees();
				echo "<option value=".$loggedUser->getUserId().">".$loggedUser->getFirstName()." ".$loggedUser->getLastName()."</option>";
		}
	
		if(isset($employees))
		{
			foreach($employees as $id=>$employee)
				{
				echo "<option value=".$employee['user_id'];
				if($userToReport == $employee['user_id'])
				{
					echo " SELECTED";
				}
				echo ">".$employee['first_name']." ".$employee['last_name']."</option>";
				}
		}
		?>
		</select>
		</td>
	</tr>
	<tr>
		<td class="report">
		Start Date:
		</td>	
		<td class="report">
		<input id="dob1" name="dob1" size="12" maxlength="10" type="text" value="<?php echo $startDate; ?>" readonly/><img src="/images/calendar.gif" onclick="showChooser(this, 'dob1', 'chooserSpan', <?php echo Date("Y")-5; ?>, <?php echo Date("Y")+3; ?>, 'm-d-Y', false);"/>
		<div id="chooserSpan" class="dateChooser select-free" style="display: none; visibility: hidden; width: 160px;">
		</div>
		</td>
	</tr>
	<tr>
	        <td class="report">
		        End Date:
       	 	</td>
        	<td class="report">
        	<input id="dob2" name="dob2" size="12" maxlength="10" type="text" value="<?php echo $endDate; ?>" readonly/><img src="/images/calendar.gif" onclick="showChooser(this, 'dob2', 'chooserSpan', <?php echo Date("Y")-5; ?>, <?php echo Date("Y")+3; ?>, 'm-d-Y', false);"/>
		<div id="chooserSpan" class="dateChooser select-free" style="display: none; visibility: hidden; width: 160px;">
		</div>
		        </td>
	</tr>
	<tr>
		<td class="report" colspan=2>
		<center><input class="ui-state-default ui-corner-all" type="submit" name="genReport" value="Show Report"></center>
		</td>
	</tr>
	<tr>
        	<td class="col_title">
        	Type
       		</td>
       	 	<td class="col_title">
        	Hours Used
        	</td>
	</tr>
	<?php
	if(isset($countLeaveHours))
	{
		foreach($countLeaveHours as $id=>$countLeaveHour)
		{
			echo "<tr><td class=\"report\">".$countLeaveHour['name'].":</td><td class=\"report\" style=\"text-align:center;\">".round($countLeaveHour['sum_hours_used'],2)."</td></tr>";
		}
	}

	?>
	</table>
</td>
<td class="content_bg" valign="top">
	<table class="tabular" cellspacing="1">
	<?php
	if(isset($leaveDays))
	{
	?>
	<tr>
	        <td class="col_title">
	        Type
       	 	</td>
        	<td class="col_title">
	        Date
	        </td>
	        <td class="col_title">
	        Block Time
	        </td>
	        <td class="col_title">
		    Real Time
	       	</td>
	</tr>
	<?php
	        foreach($leaveDays as $id=>$leaveDay)
	        {
	                echo "<tr><td class=\"col\">".$leaveDay['name']."</td><td class=\"col\" style=\"text-align:center;\">".$leaveDay['date']."</td><td class=\"col\">".$leaveDay['leave_hours']."</td><td class=\"col\">".$leaveDay['time']."</td></tr>";
	        }
	}

	?>
	</table>
	<center><input class="ui-state-default ui-corner-all" type="submit" value="Export To Excel" name="exportExcel"></center>
</td>
</tr>
</table>
</form>
