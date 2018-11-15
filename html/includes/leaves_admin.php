<?php
/**
 * UI leaves_admin.php
 * Creates a UI for the main content page for the "leave type settings" tab under administration.
 * 
 * @author Nevo Band
 */
$appointmentYears=new Years($sqlDataBase);
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
        $message = $helper->AddLeaveHours($_POST['appointmentYear'],$_POST['payPeriod'], $_POST['employeeType'],$_POST['leaveType'], $_POST['hoursToAdd'],$_POST['hoursAddPayPeriod']);
}

?>
<table class="content">
<tr>
	<td class="page_title" width=300>
	</td>
	<td class="page_title">
	<br>
	</td>
</tr>
<tr>
<td colspan=2 class="content_bg" valign="top">
<?php
echo $message;
if($leaveId > -1)
{
	echo "<form action=\"index.php?view=adminLeaves\" method=\"post\"><input class=\"ui-state-default ui-corner-all\" type=\"submit\" value=\"back\" name=\"back\"></form>";
	require_once "includes/edit_leave.php";
}
else
{
	require_once "includes/leave_type_list.php";
}
?>
</td>
</tr>
</table>
