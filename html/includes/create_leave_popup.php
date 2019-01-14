<?php
/**
 * UI create_leave_popup.php
 * Create DIV popup which lets a user create a leave.
 * This is used on the calendar view when a user clicks on a date box this popup will appear with leave input boxes.
 * 
 * @author Nevo Band
 */ 

?>
<div id="createLeavePopup" class="popupcontent" onclick="">
	<blockquote id="leave_bubble_create" onclick="">
	<div id="close_button"><input class="close_button"
					type="button" value="" name="closePopUp"
					onclick="resetCalendarSelection()">Close</div>
		<table>
			<tr>
				<td id="createLeave" align="left"><b>Create Leave:</b><br><br></td>
				<td id="createLeave" align="right"></td>
			</tr>
			<tr>
				<td id="createLeave">For:</td>
				<td id="createLeave"><SELECT name="user_id">
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
					foreach($employees as $id => $employee)
					{
						echo "<option value=".$employee['user_id'];
						if($loggedUser->getUserId()==$employee['user_id'])
						{
							echo " SELECTED";
						}
						echo ">".$employee['first_name']." ".$employee['last_name']."</option>";
					}
				}

				?>
				</SELECT></td>
			</tr>
			<tr>
				<td id="createLeave">Hours/Day:</td>
				<td id="createLeave"><input type="text" name="hours" size=2
					maxsize=2>Hrs. <input type="text" name="minutes" size=2 maxsize=2
					onkeyup="TimeChooser(this)">Min.</td>
			</tr>
			<tr>
				<td id="createLeave">Leave Type</td>
				<td id="createLeave"><SELECT name="leaveType">
				<?php
				$queryLeaveTypes = "SELECT distinct "
                                        . "lt.leave_type_id, "
                                        . "lt.name "
                                        . "FROM leave_type lt, leave_user_info lui "
                                        . "WHERE lt.leave_type_id=lui.leave_type_id "
                                        . "AND lui.user_id=:user_id "
                                        ." AND lt.special=0 AND lui.hidden=0";
                                
                                $params = array("user_id"=>$loggedUser->getUserId());
                                
				$leaveTypes = $sqlDataBase->get_query_result($queryLeaveTypes, $params);

				foreach($leaveTypes as $id => $assoc)
				{
					echo "<option value=".$assoc['leave_type_id'].">".$assoc['name']."</option>";
				}
				?>
				</SELECT></td>
			</tr>
			<tr>
				<td id="createLeave">Special:</td>
				<td id="createLeave"><SELECT name="leaveTypeSpecial">
						<option value=0></option>
						<?php
						$queryLeaveTypeSpecial = "SELECT distinct "
                                                        . "lt.leave_type_id, "
                                                        . "lt.name "
                                                        . "FROM leave_type lt, leave_user_info lui "
                                                        . "WHERE lt.leave_type_id=lui.leave_type_id "
                                                        . "AND lui.user_id=:user_id "
                                                        . "AND lt.special=1 AND lui.hidden=0";
                                                
                                                $params = array("user_id"=>$loggedUser->getUserId());
                                                
						$leaveTypeSpecial = $sqlDataBase->get_query_result($queryLeaveTypeSpecial, $params);
						if($leaveTypeSpecial)
						{
							foreach($leaveTypeSpecial as $id => $assoc)
							{
								echo "<option value=".$assoc['leave_type_id'].">".$assoc['name']."</option>";
							}
						}
						?>
				</SELECT></td>
			</tr>
			<tr>
				<td id="createLeave">Description</td>
				<td id="createLeave"><TEXTAREA row="3" cols="20" name="description"></TEXTAREA>
				</td>
			</tr>
			<tr>
				<td id="createLeave" colspan="2">
					<center>
						<input class="ui-state-default ui-corner-all" type="submit" value="Create Leave" name="CreateLeavePopup">
					</center></td>
			</tr>
		</table>
	</blockquote>
</div>
