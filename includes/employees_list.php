<?php
/**
 * UI employees_list.php
 * Creates a UI with input boxes that shows the current users employees
 * and allows the user to select an employee from the list to view user information 
 * 
 * @author Nevo Band
 */
$employees = $loggedUser->GetEmployees();
if(isset($_POST['employee']))
{
	$employeeId = $_POST['employee'];
}
else
{
	$employeeId = $loggedUser->getUserId();
}

?>
<form action="index.php?view=employees" method="post">
<table class="content">
<tr>
	<td class="page_title" width="200">
	<br>
	</td>
	<td class="page_title">
	</td>
</tr>
<tr>
	<td valign="top">
	<table width="100%">
	<tr>
		<td colspan=2 class="col_title">
		Users
		</td>
	</tr>
	<tr>
		<td class="form_field">
		User:
		</td>
		<td class="form_field">
		<select name="employee" onchange="this.form.submit()">
		<?php
		echo "<option value=".$loggedUser->getUserId().">".$loggedUser->getFirstName()." ".$loggedUser->getLastName()."</option>";
		$employees = $loggedUser->GetEmployees();
		if(isset($employees))
		{
        		foreach($employees as $id=>$employee)
        		{	
				echo "<option value=".$employee['user_id'];
				if($employee['user_id'] == $employeeId)
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
	</table>
	</td valign="top">
	<td class="content_bg">
	<?php
	require_once "includes/user_information.php";
	?>
	</td>
</tr>
</table>
</form>
