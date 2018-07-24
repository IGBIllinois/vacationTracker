<?php 
/**
 * UI edit_user.php
 * Creates a UI with input boxes to edit user information
 * Can be found under "Manage Users" Tab under the administration section
 * 
 * @author Nevo Band
 */
?>
<table class="content">
<tr>	
	<td valign="top">
	<table width="100%">
	<tr>
		<td colspan=2 class="col_title">
		Edit User Information
		</td>
	</tr>
	<tr>
		<td class="form_field">
		First Name:
		</td>
		<td class="form_field">
		<input type="text" name="firstName" value="<?php echo $editUser->getFirstName();?>">
		</td>
	</tr>
	<tr>
	        <td class="form_field">
	        Last Name:
	        </td>
	        <td class="form_field">
	        <input type="text" name="lastName" value="<?php echo $editUser->getLastName();?>">
	        </td>
	</tr>
	<tr>
	        <td class="form_field">
	        Netid:
	        </td>
	        <td class="form_field">
	        <input type="text" name="netid" value="<?php echo $editUser->getNetId();?>">
	        </td>
	</tr>
        <tr>
	        <td class="form_field">
	        UIN:
	        </td>
	        <td class="form_field">
	        <input type="text" name="uin" value="<?php echo $editUser->getUIN();?>">
	        </td>
	</tr>
	<tr>
	        <td class="form_field">
	        E-Mail:
	        </td>
	        <td class="form_field">
	        <input type="text" name="email" value="<?php echo $editUser->getUserEmail();?>" size="30">
	        </td>
	</tr>
	<tr>
	        <td class="form_field">
	        Appointment:
	        </td>
	        <td class="form_field">
	        <input type="text" name="percent" value="<?php echo $editUser->getPercent();?>" size="3" maxlength="3">%
	        </td>
	</tr>
	<tr>
                <td class="form_field">
                Start Date:
                </td>
                <td class="form_field">
                <input type="text" name="startDate" id="datepickerStartDate" value="<?php list($year,$month,$day) = explode("-",$editUser->getStartDate()); echo $month."/".$day."/".$year; ?>">
                </td>
        </tr>
	<tr>
                <td class="form_field">
                Auto Approve:
                </td>
                <td class="form_field">
                <input type="checkbox" name="autoApprove" <?php echo (($editUser->getAutoApprove())?"checked":"unchecked"); ?>>
                </td>
        </tr>
	<tr>
                <td class="form_field">
                User Enabled:
                </td>
                <td class="form_field">
                <input type="checkbox" name="enabled" <?php echo (($editUser->getEnabled())?"checked":"unchecked"); ?>>
                </td>
        </tr>
        <tr>
                <td class="form_field">
                Include in Banner:
                </td>
                <td class="form_field">
                <input type="checkbox" name="banner_include" <?php echo (($editUser->getBannerInclude())?"checked":"unchecked"); ?>>
                </td>
        </tr>
	<tr>
	        <td class="form_field">
	        Supervisor:
	        </td>
	        <td class="form_field">
		<SELECT name="supervisor">
		<?php
	        $queryAllUsers = "SELECT user_id, first_name, last_name FROM users";
	        $allUsers = $sqlDataBase->query($queryAllUsers);
	        if(isset($allUsers))
	        {
	                echo "<option value=\"0\">No Supervisor</option>";
	                foreach($allUsers as $id=>$user)
	                {
	                        if($editUser->getUserId()!=$user['user_id'])
	                        {
	                                echo "<option value=".$user['user_id'];
	                                if($user['user_id']==$editUser->getSupervisorId())
	                                {
	                                        echo " SELECTED";
	                                }
	                                echo ">".$user['first_name']." ".$user['last_name']."</option>";
	                        }
	                }
	        }
		?>
		</SELECT>
	        </td>
	</tr>
	<tr>
		<td class="form_field">
		Employee Type:
		</td>
		<td class="form_field">
		<SELECT name="employeeType">
		<?php
		$queryEmployeeTypes = "SELECT user_type_id, name, description FROM user_type";
		$employeeTypes = $sqlDataBase->query($queryEmployeeTypes);
		foreach($employeeTypes as $id=>$employeeType)
		{
			echo "<option value=".$employeeType['user_type_id'];
			if($editUser->getUserTypeId()==$employeeType['user_type_id'])
			{
				echo " SELECTED";
			}
			echo ">".$employeeType['name']."</option>";
		}	
		?>	
		</SELECT>
		</td>
	
	</tr>
	<tr>
		<td class="form_field">
		Permissions:
		</td>
		<td class="form_field">
		<SELECT name="userPerm">
		<?php
		$queryUserPerms = "SELECT user_perm_id, name, description FROM user_perm";
		$userPerms = $sqlDataBase->query($queryUserPerms);
		foreach($userPerms as $id=>$userPerm)
		{
			echo "<option value=".$userPerm['user_perm_id'];
			if($editUser->getUserPermId()==$userPerm['user_perm_id'])
			{
				echo " SELECTED";
			}
			echo ">".$userPerm['name']."</option>";
		}
	
		?>
		</SELECT>
		</td>
	
	</tr>
	</table>
	</td>
</tr>
</table>
<center>
</form>

