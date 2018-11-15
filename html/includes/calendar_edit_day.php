<?php
/**
 * UI calendar_edit_day.php
 * Creates a UI with input boxes to edit/create a special day (holiday to show on the calendar or block from reservations).
 * 
 * @author Nevo Band
 */
if(isset($_POST['createDay']))
{
	$editSpecialDay->CreateSpecialDay($_POST['dayDescription'],$_POST['dayName'],$_POST['dayColor'],(isset($_POST['blocked']))?1:0,$_POST['month'],$_POST['day'],$_POST['year'],$_POST['priority'],$_POST['weekDay'],$editSpecialDay->getUserId());
	$dayId=$editSpecialDay->getDayId();
}

if(isset($_POST['applyEditDay']))
{
	if($editSpecialDay->getDayId())
	{
		$editSpecialDay->setName(mysqli_real_escape_string($sqlDataBase->getLink(), $_POST['dayName']));
		$editSpecialDay->setDescription(mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['dayDescription']));
		$editSpecialDay->setColor(mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['dayColor']));
		$editSpecialDay->setBlocked((isset($_POST['blocked']))?1:0);
		$editSpecialDay->setMonth($_POST['month']);
		$editSpecialDay->setDay($_POST['day']);
		$editSpecialDay->setYear($_POST['year']);
		$editSpecialDay->setPriority($_POST['priority']);
		$editSpecialDay->setWeekDay($_POST['weekDay']);
		$editSpecialDay->setUserId((isset($_POST['specialDayUsers']))?$_POST['specialDayUsers']:$loggedUser->getUserId());
		$editSpecialDay->UpdateDb();
	}
}

?>

<form
	action="index.php?view=adminCalendar&id=<?php echo $editSpecialDay->getDayId(); ?>"
	method="POST">
	<table>
	<?php
	if($loggedUser->getUserPermId()==1)
	{
		echo "<tr><td colspan=2 class=\"col_title\">Special Day Owner</td></tr>";
		echo "<tr><td>";
		echo "User:";
		echo "</td><td>";
		echo "<SELECT name=\"specialDayUsers\" onchange=\"this.form.submit()\">";
		echo "<option value=0>Global</option>";
		$usersInfo = $loggedUser->getAllUsers();
		foreach($usersInfo as $userInfo)
		{
			echo "<option value=".$userInfo['user_id'];
			if($userInfo['user_id']==$editSpecialDay->getUserId())
			{
				echo " SELECTED";
			}
			echo ">".$userInfo['first_name']." ".$userInfo['last_name']."</option>";
		}
		echo "</SELECT>";
		echo "</td></tr>";

	}
	?>
		<tr>
			<td colspan=2 class="col_title"><?php
			if($dayId>0)
			{
				echo "Edit Day";
			}
			else
			{
				echo "Create Day";
			}
			?>
			</td>
		</tr>
		<tr>
			<td class="form_field">Name:</td>
			<td class="form_field"><input type="text" name="dayName"
				value="<?php echo $editSpecialDay->getName(); ?>">
			</td>
		</tr>
		<tr>
			<td class="form_field" valign="top">Description:</td>
			<td class="form_field"><textarea name="dayDescription" cols="14"
					rows="2">
					<?php echo $editSpecialDay->getDescription(); ?>
				</textarea>
			</td>
		</tr>
		<tr>
			<td class="form_field">Calendar Color:</td>
			<td class="form_field"><input type="text" name="dayColor"
				value="<?php echo $editSpecialDay->getColor(); ?>" class="color">
			</td>
		</tr>
		<tr>
			<td class="form_field">Blocked:</td>
			<td class="form_field"><input type="checkbox" name="blocked"
			<?php echo ($editSpecialDay->getBlocked())?"checked":""; ?>>
			</td>
		</tr>
		<tr>
			<td class="form_field">Month:</td>
			<td class="form_field"><SELECT name="month">
					<option value=0>All</option>
					<?php
					for($i=1; $i<13; $i++)
					{
						echo "<option value=".$i;
						if($i==$editSpecialDay->getMonth())
						{
							echo " SELECTED";
						}
						echo ">".Date("F",mktime(0,0,0,$i,1,2010))."</option>";
					}
					?>
			</SELECT>
			</td>
		</tr>
		<tr>
			<td class="form_field">Day:</td>
			<td class="form_field"><SELECT name="day">
					<option value=0>All</option>
					<?php
					for($i=1; $i<32; $i++)
					{
						echo "<option value=".$i;
						if($i==$editSpecialDay->getDay())
						{
							echo " SELECTED";
						}
						echo ">".$i.Date("S",mktime(0,0,0,8,$i,2010))."</option>";
					}
					?>
			</SELECT>
			</td>

		</tr>
		<tr>
			<td class="form_field">Year:</td>
			<td class="form_field"><SELECT name="year">
					<option value=0>All</option>
					<?php
					for($i=-5; $i<5; $i++)
					{
						echo "<option value=".($i+date("Y"));
						if(($i+date("Y"))==$editSpecialDay->getYear())
						{
							echo " SELECTED";
						}
						echo ">".($i+date("Y"))."</option>";
					}
					?>
			</SELECT>
			</td>
		</tr>
		<tr>
			<td class="form_field">Week Day:</td>
			<td class="form_field"><SELECT name="weekDay">
					<option value=0>All</option>
					<?php
					for($i=1; $i<8; $i++)
					{
						echo "<option value=".$i;
						if($i==$editSpecialDay->getWeekDay())
						{
							echo " SELECTED";
						}
						echo ">".Date("l",mktime(0,0,0,8,$i,2010))."</option>";
					}
					?>
			</SELECT>
			</td>
		</tr>
		<tr>
			<td class="form_field">Priority:</td>
			<td class="form_field"><SELECT name="priority">
			<?php
			for($i=1; $i<21; $i++)
			{
				echo "<option value=".$i;
				if($i==$editSpecialDay->getPriority())
				{
					echo " SELECTED";
				}
				echo ">".$i."</option>";
			}
			?>
			</SELECT>
			</td>
		</tr>
		<tr>

			<td class="form_field" colspan=2>
				<center>
				<?php
				if($dayId>0)
				{
					echo "<input class=\"ui-state-default ui-corner-all\" type=\"submit\" name=\"applyEditDay\" value=\"Apply Changes\">";
					echo "<center><input class=\"ui-state-default ui-corner-all\" type=\"submit\" value=\"<< Create\" name=\"createSpecialDay\">";
				}else{
					echo "<input class=\"ui-state-default ui-corner-all\" type=\"submit\" name=\"createDay\" value=\"Create Day\">";
				}

				?>
				</center>
			</td>
		</tr>

	</table>
	<br>