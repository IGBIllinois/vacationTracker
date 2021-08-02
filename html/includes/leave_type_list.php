<?php 
/**
 * UI leave_type_list.php
 * Creates a UI with a list of leave types available, a user can click on edit to edit
 * the leave type information.
 * 
 * @author Nevo Band
 */
?>
<form action="index.php?view=adminLeaves&id=0" method="POST">
<input class="ui-state-default ui-corner-all" type="submit" value="Create Leave" name="createLeaveType">
<table class="hover_table" id="hover_table">
<thead>
<tr>
<th>
Name
</th>
<td>
Description
</td>
<td>
Color
</td>
<td>
Options
</td>
</tr>
</thead>
<tbody>
<?php

$leaveTypes = LeaveType::GetLeaveTypes($sqlDataBase);

foreach($leaveTypes as $leaveType)
{
        echo "<tr><td>".$leaveType->GetName()."</td>"
                . "<td>".$leaveType->getDescription()."</td>"
                . "<td><center><div id=\"leave_color_box\" style=\"background-color:#".$leaveType->getColor()."\"></div></center> </td>"
                . "<td><a href=\"index.php?view=adminLeaves&id=".$leaveType->getTypeId()."\">Edit</a> </td></tr>";
}

?>
</tbody>
</table>
<br>
</form>

