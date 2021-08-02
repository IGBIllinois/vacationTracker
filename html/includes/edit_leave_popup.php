<?php 
/**
 * UI edit_leave_popup.php
 * Create DIV popup which lets a user edit a leave.
 * This is used on the calendar view when a user clicks on a leave box this popup will appear with leave information to edit
 * 
 * @author Nevo Band
 */
?>
<div id="editLeavePopup" class="popupcontent">
<blockquote id="leave_bubble_edit">
<div id="close_button"><input class="close_button"
					type="button" value="" name="closePopUp"
					onclick="resetCalendarSelection()">Close</div>
<table width="100%">
        <tr>
                <td  align="left">
                <b>Leave Information:</b>
                <br><br>
		<input id="editLeaveId" type="hidden" name="editLeaveId" value="0">
                </td>
		<td align="right">
		</td>
        </tr>
        <tr>
                <td >
                For:
                </td>
                <td >
                <SELECT id="editUserId" name="editUserId">
                <?php

                        if($loggedUser->GetUserPermId()==ADMIN)
                        {
                                $employees = $loggedUser->GetAllEnabledUsers();
                        }else
                        {
                                $employees = $loggedUser->GetEmployees();
                                echo "<option value=".$loggedUser->getUserId().">".$loggedUser->getFirstName()." ".$loggedUser->getLastName()."</option>";
                        }
                        if($loggedUser->GetUserPermId() == ADMIN || count($employees)>0)
                        {
                                foreach($employees as $id => $employee)
                                {
                                        echo "<option value=".$employee['user_id'];
                                        echo ">".$employee['first_name']." ".$employee['last_name']."</option>";
                                }
                        }

                ?>
                </SELECT>
                </td>
        </tr>
        <tr>
                <td >
                Hours/Day:
                </td>
                <td >
                <input type="text" id="editHours" name="editHours" size=2 maxsize=2>Hrs. <input type="text" id="editMinutes" name="editMinutes" size=2 maxsize=2 onkeyup="TimeChooser(this)">Min.
                </td>
        </tr>
        <tr>
                <td >
                Leave Type
		</td>
                <td >
                <SELECT id="editLeaveType" name="editLeaveType">
                <?php

                $leaveTypes = $loggedUser->GetUserLeaveTypes();
                foreach($leaveTypes as $id => $assoc)
                {
                        echo "<option value=".$assoc['leave_type_id'].">".$assoc['name']."</option>";
                }
                ?>
                </SELECT>
                </td>
        </tr>
        <tr>
                <td >
                Special:
                </td>
                <td >
                <SELECT id="editLeaveTypeSpecial" name="editLeaveTypeSpecial">
                <option value=0></option>
                <?php

                $leaveTypeSpecial = $loggedUser->GetUserSpecialLeaves();
                
                if($leaveTypeSpecial)
                {
                        foreach($leaveTypeSpecial as $id => $assoc)
                        {
                                echo "<option value=".$assoc['leave_type_id'].">".$assoc['name']."</option>";
                        }
                }
                ?>
                </SELECT>
                </td>
        </tr>
        <tr>
                <td>
                Description
                </td>
                <td>
                <TEXTAREA row="3" cols="20" id="editDescription" name="editDescription"></TEXTAREA>
                </td>
        </tr>
        <tr>
                <td colspan="2">
                <center><input class="ui-state-default ui-corner-all" type="submit" value="Modify" name="ModifyLeavePopup"><input class="ui-state-default ui-corner-all" type="submit" value="Delete" name="DeleteLeavePopup"></center>
		
                </td>
        </tr>
</table>
</blockquote>
</div>
