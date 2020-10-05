<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if(isset($_POST['edit_max_rollover'])) {
    
    $year = new Years($sqlDataBase, $_POST['yearid']);
    $leave_types = LeaveType::GetLeaveTypes($sqlDataBase, $year->GetYearType());
    
    foreach($leave_types as $leave_type) {
        $rollover = $_POST['rollover-'.$leave_type->getTypeId()];
        if($rollover != 0 && $rollover != 9999) {
            $year->SetMaxRollover($leave_type->getTypeId(), $rollover);
        }
    }
    
}

$yearid = $_GET['id'];

$year = new Years($sqlDataBase, $yearid);
$leave_types = LeaveType::GetLeaveTypes($sqlDataBase, $year->GetYearType());

echo("<form action='index.php?view=adminYears&max_rollover=1&id=$yearid' method='POST'>");
echo("<table><tr>");

echo("<tr>
		<td colspan=2 class='col_title'>
		Rollover for Year ".$year->getStartDate() . " - ". $year->getEndDate().
		"</td>
	</tr>");
echo("<tr><td class='content_bg' valign='top'>");
echo("<table class=\"hover_table\">");
echo("<thead><tr><td>Leave Type</td><td>Max Rollover</td></tr></thead><tbody>");
foreach($leave_types as $leave_type) {
    echo("<tr><td>");
    echo($leave_type->GetName());
    echo("</td><td>");
    echo("<input type=integer name='rollover-".$leave_type->getTypeId()."' value='".$year->GetMaxRollover($leave_type->getTypeId())."'>");
    echo("</td>");
}
echo("</tr></tbody></table></td></tr></table>");
echo("<input type='hidden' name='yearid' value='".$year->getId(). "'>");
echo("<input type='submit' name='edit_max_rollover' value='Edit Max Rollover hours'>");
echo("</form>");