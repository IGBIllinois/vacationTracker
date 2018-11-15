<?php
/**
 * UI calendar_admin.php
 * Contains the main contents for the calendar administration page "Calendar Settings" Tab.
 * Includes calendar_edit_day.php, special_days_list.php
 * 
 * @author Nevo Band
 */
if(isset($_GET['id']) && !isset($_POST['createSpecialDay']))
{
	$dayId = $_GET['id'];
}
else
{
	$dayId = 0;
}

$editSpecialDay = new SpecialDay($sqlDataBase);

if($dayId>0)
{
	$editSpecialDay->LoadSpecialDay($dayId);
	if(isset($_POST['specialDayUsers']))
	{
		if($_POST['specialDayUsers']!=$editSpecialDay->getUserId())
		{
			$editSpecialDay = new SpecialDay($sqlDataBase);
			$editSpecialDay->setUserId($_POST['specialDayUsers']);
			$dayId=0;
		}
	}
}
elseif(isset($_POST['specialDayUsers']))
{
	$editSpecialDay->setUserId($_POST['specialDayUsers']);
}
else
{
	$editSpecialDay->setUserId($loggedUser->getUserId());
}


?>

<table class="content">
	<tr>
		<td class="page_title" width="200"></td>
		<td class="page_title"><br>
		</td>
	</tr>
	<tr>
		<td valign="top"><?php
		require_once "includes/calendar_edit_day.php";
		?>
		</td>
		<td class="content_bg" valign="top"><?php
		require_once "special_days_list.php";
		?>
		</td>
	</tr>
</table>

