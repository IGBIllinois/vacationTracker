<?php
/**
 * UI edit_leave.php
 * Create a UI to edit or create leave types.
 * this can be found under the "Leave Type Settings" Tab under administration when you click on edit or create leave.
 * 
 * @author Nevo Band
 */
$editLeaveType = new LeaveType($sqlDataBase);

if($leaveId>0)
{
	$editLeaveType->LoadLeaveType($leaveId);
}

if(isset($_POST['createLeave']))
{
	$editLeaveType->CreateLeaveType(
                $_POST['leaveName'],
                $_POST['leaveDescription'],
                $_POST['leaveColor'],
                (isset($_POST['special']))?1:0,
                (isset($_POST['hidden']))?1:0,
                (isset($_POST['rollOver']))?1:0,
                $_POST['max'],
                $_POST['defaultValue'],
                $_POST['yearType']);
	$leaveId=$editLeaveType->getTypeId();
}

if(isset($_POST['applyEditLeave']))
{
	if($editLeaveType->getTypeId())
	{
		$editLeaveType->setName($_POST['leaveName']);
		$editLeaveType->setDescription($_POST['leaveDescription']);
		$editLeaveType->setColor($_POST['leaveColor']);
		$editLeaveType->setSpecial((isset($_POST['special']))?1:0);
		$editLeaveType->setHidden((isset($_POST['hidden']))?1:0);
		$editLeaveType->setDefaultValue($_POST['defaultValue']);
		$editLeaveType->setRollOver((isset($_POST['rollOver']))?1:0);
		$editLeaveType->setMax($_POST['max']);
		$editLeaveType->UpdateDb();
	}
}
if(isset($_POST['toShow']) && isset($_POST['hiddenLeaves']))
{
	$leavesToShow = $_POST['hiddenLeaves'];	
	if($leavesToShow)
	{	
		foreach($leavesToShow as $leaveToShow)
		{
                    $hideUser = new User($sqlDataBase);
                    $hideUser->LoadUser($leaveToShow);

                    $results = $hideUser->GetUserLeaves($editLeaveType->getTypeId());

                    foreach($results as $result) {
                        $id = $result[0];
                        $hideUser->UpdateLeaveVisibility($id, 0);
                    }
                    

		}
	}
}

if(isset($_POST['toHidden']) && isset($_POST['showLeaves']))
{
        $leavesToHidden = $_POST['showLeaves'];
        if($leavesToHidden)
        {
                foreach($leavesToHidden as $leaveToHidden)
                {

                    $hideUser = new User($sqlDataBase);
                    $hideUser->LoadUser($leaveToHidden);

                    $results = $hideUser->GetUserLeaves($editLeaveType->getTypeId());
                    foreach($results as $result) {
                        $id = $result[0];
                        $hideUser->UpdateLeaveVisibility($id, 1);
                    }
                }
        }
}

?>

<form action="index.php?view=adminLeaves&id=<?php echo $editLeaveType->getTypeId(); ?>" method="POST">
<table width="100%">
<tr>
	<td colspan=2 class="col_title">
	<?php
	if($leaveId)
	{
		echo "Edit Leave";
	}
	else
	{
		echo "Create Leave";
	}
	?>
	</td>
</tr>
<tr>
	<td class="form_field">
	Name:
	</td>
	<td class="form_field">
	<input type="text" name="leaveName" value="<?php echo $editLeaveType->getName(); ?>">
	</td>
</tr>
<tr>
        <td class="form_field">
        Description:
        </td>
        <td class="form_field">
        <textarea rows="2" cols="20" name="leaveDescription"><?php echo $editLeaveType->getDescription(); ?></textarea>
        </td>
</tr>
<tr>
        <td class="form_field">
        Calendar Color:
        </td>
        <td class="form_field">
        <input type="text" name="leaveColor" value="<?php echo $editLeaveType->getColor(); ?>" class="color" >
        </td>
</tr>
<tr>
	<td class="form_field">
	Special Leave:
	</td>
	<td class="form_field">
	<input type="checkbox" name="special" <?php echo ($editLeaveType->getSpecial())?"checked":""; ?> >
	</td>
</tr>
<tr>
        <td class="form_field">
        Default Hidden:
        </td>
        <td class="form_field">
        <input type="checkbox" name="hidden" <?php echo ($editLeaveType->getHidden())?"checked":""; ?> >
        </td>
</tr>
<tr>
        <td class="form_field">
        Default Value:
        </td>
        <td class="form_field">
	<input type="text" name="defaultValue" value="<?php echo $editLeaveType->getDefaultValue(); ?>">
        </td>
</tr>
<tr>
        <td class="form_field">
        Roll Over:
        </td>
        <td class="form_field">
        <input type="checkbox" name="rollOver" <?php echo ($editLeaveType->getRollOver())?"checked":""; ?> >
        </td>
</tr>
<tr>
        <td class="form_field">
        Max Roll Over Hours:
        </td>
        <td class="form_field">
        <input type="text" name="max" value="<?php echo $editLeaveType->getMax(); ?>">
        </td>
</tr>
<tr>
	<td class="form_field">
	Year Type:
	</td>
	<td class="form_field">
	<SELECT name="yearType" <?php echo (($leaveId > 0)?"disabled":"") ?> >
	<?php

	$yearTypes = Years::GetYearTypes($sqlDataBase);
	foreach($yearTypes as $id=>$yearType)
	{
		echo "<option value=".$yearType['year_type_id'];
		if($editLeaveType->getYearTypeId()== $yearType['year_type_id'])
		{
			echo " SELECTED";
		}
		echo ">".$yearType['name']."</option>";
		
	}
	?>
	</SELECT>
	</td>
	
</tr>


<tr>

	<td colspan=2>
	<center>
	<?php
		if($leaveId>0)
		{
			echo "<input class=\"ui-state-default ui-corner-all\" type=\"submit\" name=\"applyEditLeave\" value=\"Apply Changes\">";
		}else{
			echo "<input class=\"ui-state-default ui-corner-all\" type=\"submit\" name=\"createLeave\" value=\"Create Leave\">";
		}
		
	?>
	</center>
	</td>
</tr>
</table>
</form>
<br>
<?php
if($leaveId>0)
{
?>
<form action="index.php?view=adminLeaves&id=<?php echo $editLeaveType->getTypeId(); ?>" method="POST">
<table width="100%">
<tr><td class="col_title" width="48%">Hidden</td><td width="20"></td><td class="col_title" width="48%">Shown</td></tr>
<tr>
	<td valign="top" class="content_bg">
	<SELECT name="hiddenLeaves[]" class="leaves_user" size=15 multiple>
	<?php


        $hiddenLeaveTypes = User::GetUsersForLeaveTypes($sqlDataBase, $editLeaveType->getTypeId(), 1);

        
	if($hiddenLeaveTypes)
	{
		foreach($hiddenLeaveTypes as $id=>$hiddenLeave)
		{
			echo "<option value=".$hiddenLeave['user_id'].">".$hiddenLeave['first_name']." ".$hiddenLeave['last_name']."</option>";
		}
	}
	?>
	</SELECT>
	</td>
	<td class="content_bg" width="20" align="center" style="text-align:center;">
	<input type="submit" name="toShow" value="" class="right_button"><br><br>
	<input type="submit" name="toHidden" value="" class="left_button">
        </td>
	<td valign="top" class="content_bg">
	<SELECT name="showLeaves[]" class="leaves_user" size=15 multiple>
	<?php

        $showenLeaveTypes = User::GetUsersForLeaveTypes($sqlDataBase, $editLeaveType->getTypeId(), 0);
        
        
        
	if($showenLeaveTypes)
	{
        	foreach($showenLeaveTypes as $id=>$showenLeave)
        	{
        	        echo "<option value=".$showenLeave['user_id'].">".$showenLeave['first_name']." ".$showenLeave['last_name']."</option>";
        	}
	}
        ?>
        </SELECT>

        </td>
</table>
</form>
<?php
}
?>
