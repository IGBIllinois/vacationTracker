<?php
/**
 * UI user_information.php
 * Creates UI containing the selected user information, used under "user accounts" tab under user view.
 * 
 * @author Nevo Band
 */
$viewUser = new User($sqlDataBase);
$viewUser->LoadUser($employeeId);

?>

<form action="index.php?view=adminUsers&form_field=<?php echo $viewUser->getUserId(); ?>" method="POST">
<table class="content">
<tr>
	<td class="content_bg" valign="top">
	<table class="form_field" width="100%">
	<tr>
		<td colspan=2 class="col_title">
		User Information
		</td>
	</tr>		
	<tr>
		<td class="form_field">
		First Name:
		</td>
		<td class="form_field">
		<input readonly type="text" name="firstName" value="<?php echo $viewUser->getFirstName();?>">
		</td>
	</tr>
	<tr>
	        <td class="form_field">
	        Last Name:
	        </td>
	        <td class="form_field">
	        <input readonly type="text" name="lastName" value="<?php echo $viewUser->getLastName();?>">
	        </td>
	</tr>
	<tr>
	        <td class="form_field">
	        Netid:
	        </td>
	        <td class="form_field">
	        <input readonly type="text" name="netid" value="<?php echo $viewUser->getNetId();?>">
	        </td>
	</tr>
	<tr>
	        <td class="form_field">
	        E-Mail:
	        </td>
	        <td class="form_field">
	        <input readonly type="text" name="email" value="<?php echo $viewUser->getUserEmail();?>">
	        </td>
	</tr>
	<tr>
	        <td class="form_field">
	        Appointment:
	        </td>
	        <td class="form_field">
	        <input readonly type="text" name="percent" value="<?php echo $viewUser->getPercent();?>" size="3" maxlength="3">%
	        </td>
	</tr>
	<tr>
	        <td class="form_field">
	        Supervisor:
	        </td>
	        <td class="form_field">
		<SELECT disabled="supervisor">
		<?php
	        $queryAllUsers = "SELECT user_id, first_name, last_name FROM users";
	        $allUsers = $sqlDataBase->get_query_result($queryAllUsers);
	        if(isset($allUsers))
	        {
	                echo "<option value=\"0\">No Supervisor</option>";
	                foreach($allUsers as $id=>$user)
	                {
	                        if($viewUser->getUserId()!=$user['user_id'])
	                        {
	                                echo "<option value=".$user['user_id'];
	                                if($user['user_id']==$viewUser->getSupervisorId())
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
		<SELECT disabled="employeeType">
		<?php
		$queryEmployeeTypes = "SELECT user_type_id, name, description FROM user_type";
		$employeeTypes = $sqlDataBase->get_query_result($queryEmployeeTypes);
		foreach($employeeTypes as $id=>$employeeType)
		{
			echo "<option value=".$employeeType['user_type_id'];
			if($viewUser->getUserTypeId()==$employeeType['user_type_id'])
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
		<SELECT disabled="userPerm">
		<?php
		$queryUserPerms = "SELECT user_perm_id, name, description FROM user_perm";
		$userPerms = $sqlDataBase->get_query_result($queryUserPerms);
		foreach($userPerms as $id=>$userPerm)
		{
			echo "<option value=".$userPerm['user_perm_id'];
			if($viewUser->getUserPermId()==$userPerm['user_perm_id'])
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
