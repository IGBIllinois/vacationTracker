<?php
/**
 * UI manage_leaves.php
 * Creates a UI which a allows a user or supervisor to view user leaves status.
 * 1) Select year type to view leaves for
 * 2) select year to view leaves for
 * 3) View available leave hours
 * 4) view leaves according to their current status New/Approved/Waiting Approval/Not Approved
 * 5) Allow for common leave actions Request Approval/Delete
 * 
 * @author Nevo Band
 * 
 */

$years = new Years($sqlDataBase);
$helperClass = new Helper($sqlDataBase);
$rules = new Rules($sqlDataBase);
$messageBox ="";
if(isset($_POST['leave_day']))
{
	$defaultDate = $_POST['leave_day'];
}
else
{
	$defaultDate =Date('Y-m-d');
}

if(isset($_POST['requestApproval']))
{
	$messageBox = $helperClass->RequestLeavesApproval(@$_POST['leavesCheckBox'],$loggedUser);
}

if(isset($_POST['approveLeaves']))
{
	$messageBox = $helperClass->ApproveLeaves(@$_POST['leavesCheckBox'],$loggedUser);
}

if(isset($_POST['notApproveLeaves']))
{
	$messageBox = $helperClass->DoNotApproveLeaves(@$_POST['leavesCheckBox'],$loggedUser);
}

if(isset($_POST['deleteLeave']))
{
	$messageBox = $helperClass->DeleteLeave(@$_POST['leavesCheckBox'],$loggedUser);
}


if(isset($_POST['displayUserLeaves']))
{
	$leavesShowUser = $_POST['displayUserLeaves'];

}elseif(isset($_GET['userid']))
{
	if($loggedUser->isEmployee($_GET['userid']) || $loggedUser->GetUserPermId()==ADMIN)
	{
		$leavesShowUser = $_GET['userid'];
	}
	else
	{
		$leavesShowUser = $loggedUser->getUserId();
	}
}
else
{
	$leavesShowUser = $loggedUser->getUserId();
}

if(isset($_POST['leavesStatus']))
{
	$statusId = $_POST['leavesStatus'];
}
elseif(isset($_GET['leave_status']))
{
	$statusId = $_GET['leave_status'];
}
else
{
	$statusId = NEW_LEAVE;
}

if(isset($_POST['hours']))
{
	$hours=$_POST['hours'];
}
else
{
	$hours=0;
}

if(isset($_POST['minutes']))
{
	$minutes=$_POST['minutes'];
}
else
{
	$minutes=0;
}

if(isset($_POST['leaveType']))
{
	$leaveTypeSelected = $_POST['leaveType'];
}
else
{
	$leaveTypeSelected = 0;
}

if(isset($_POST['leaveTypeSpecial']))
{
	$leaveTypeSpecialSelected = $_POST['leaveTypeSpecial'];
}
else
{
	$leaveTypeSpecialSelected = 0;
}

if(isset($_POST['description']))
{
	$description = $_POST['description'];
}
else
{
	$description ="";
}

if(isset($_GET['email'])) {

    $user_id = $_GET['user_id'];
    $status_id = $_GET['status_id'];
    $app_year = $_GET['app_year_id'];
    $fisc_year = $_GET['fisc_year_id'];
    $pay_period = $_GET['pay_period'];

    $email =  new Email($sqlDataBase);
    $result = $email->sendReportEmail($user_id, $status_id, $app_year, $fisc_year, $pay_period);
    if($result['RESULT'] == TRUE) {
        echo("<div class='alert alert-success'>".$result['MESSAGE']."</div>");
    } else {
        echo("<div class='alert alert-danger'>".$result['MESSAGE']."</div>");
    }
}
if($loggedUser->GetUserPermId()==ADMIN)
{
	$employees = $loggedUser->GetAllEnabledUsers();
}else
{
	$employees = $loggedUser->GetEmployees();
}

?>

<table class="content" cellspacing="0">
	<tr>
		<td class="page_title"></td>
		<td class="page_title" style="align-text: right"></td>
	</tr>
	<tr>
		<td valign="top" width="200">
			<form name="users_select" action="index.php?view=create"
				method="post">
				<table width="100%">
					<tr>
						<td colspan=2 class="col_title">Users</td>
					</tr>
					<tr>
						<td>User:</td>
						<td><SELECT name="displayUserLeaves" onchange="this.form.submit()">
						<?php

						echo "<option value=".$loggedUser->getUserId().">".$loggedUser->getFirstName()." ".$loggedUser->getLastName()."</option>";
						if(isset($employees))
						{
							foreach($employees as $id => $employee)
							{
								echo "<option value=".$employee['user_id'];
								if($employee['user_id']==$leavesShowUser)
								{
									echo " SELECTED";
								}
								echo ">".$employee['first_name']." ".$employee['last_name']."</option>";
							}
						}
						?>
						</SELECT>
						</td>
					</tr>
				</table>
			</form>
		</td>
		<td class="content_bg" valign="top"><?php echo $messageBox; 
		$queryYearTypes = "SELECT year_type_id,name,description FROM year_type";
		$yearTypes = $sqlDataBase->query($queryYearTypes)
		?>
			<div id="yeartabs">
				<ul>
				<?php
				foreach($yearTypes as $id=>$yearType)
				{
					echo "<li><a onclick=\"uncheckAll(document.year_type_".$yearType['year_type_id'].".elements['leavesCheckBox[]'],0)\" href=\"#yeartabs-".$yearType['year_type_id']."\" style=\"font-size:12px\">".$yearType['name']."</a></li>";
				}
                                
                                if($loggedUser->getUserPermId() == ADMIN) {
                                    echo "<li><a href=\"#yeartabs-"."report"."\" style=\"font-size:12px\">"."Reports"."</a></li>";
                                }

				?>
				</ul>
                            
				<?php
				foreach($yearTypes as $id=>$yearType)
				{
                                   
					echo "<form name=\"year_type_".$yearType['year_type_id']."\" action=\"index.php?view=create#yeartabs-".$yearType['year_type_id']."\" method=\"post\">";
					echo "<input type=\"hidden\" name=\"displayUserLeaves\" value=\"".$leavesShowUser."\">";
					echo "<div id=\"yeartabs-".$yearType['year_type_id']."\">";
					?>
				<table width="100%">
					<tr>
						<td align="left"><?php
						
						echo "<input type=submit value=\"\" name=\"decYear-".$yearType['year_type_id']."\" class=\"left_button\"><input type=submit value=\"\" name=\"incYear-".$yearType['year_type_id']."\" class=\"right_button\">";
						
						
						if(isset($_POST["year-".$yearType['year_type_id']]))
						{
							$yearSelected= $_POST["year-".$yearType['year_type_id']];
                                                        
							if(isset($_POST["decYear-".$yearType['year_type_id']]))
							{
								$prevYearSelected = $years->PrevYearId($_POST["year-".$yearType['year_type_id']]);
								if($prevYearSelected)
								{
                                                                    
									$yearSelected = $prevYearSelected;
								}
                                                                
							}
							elseif(isset($_POST["incYear-".$yearType['year_type_id']]))
							{
								$nextYearSelected = $years->NextYearId($_POST["year-".$yearType['year_type_id']]);
								if($nextYearSelected)
								{
                                                                    
									$yearSelected = $nextYearSelected;
								}
							}
						}
						else
						{

							$yearSelected = $years->GetYearId(Date('d'),Date('m'),Date('Y'),$yearType['year_type_id']);
						}
                                                
						$yearInfo = $years->GetYearDates($yearSelected);
						echo "<b>".Date("F Y",strtotime($yearInfo[0]['start_date']))." - ".Date("F Y",strtotime($yearInfo[0]['end_date']))."<b>";
						echo "<input type=hidden value=".$yearSelected." name=\"year-".$yearType['year_type_id']."\">";
						?>
						</td>
						<td align="right">
						<?php 
						if(isset($_POST["refresh-".$yearType['year_type_id']]))
						{
							$helperClass->RunRulesYearType($leavesShowUser, $yearType['year_type_id'],true);
						}
						echo "<input class=\"refresh_button\" type=submit value=\"\" id=\"refresh\" name=\"refresh-".$yearType['year_type_id']."\" title=\"<b>Run Rules</b><br>All rules should run automatically when adding and removing leaves.<br>Should only need to run this if something weird happened.\">Run Rules";
						?>
						</td>
					</tr>
				</table>
				<br>
				<b>Leaves Available:<b>
				<table class="tabular">
				<?php
                                
				echo $helperClass->DrawLeaveHoursAvailable($yearSelected,$leavesShowUser,false);
				?>
				</table>
				<br><br>
				<b>Leaves Request Management:</b>
				<?php
				$queryStatus = "SELECT s.status_id,s.name, COUNT(li.status_id) as num_leaves FROM status s LEFT JOIN leave_info li ON (li.status_id = s.status_id AND user_id=".$leavesShowUser." AND  li.year_info_id = ".$yearSelected.") WHERE s.status_id!=".DELETED." GROUP BY s.status_id";
				$statusList = $sqlDataBase->query($queryStatus);
				?>
				<div id="leavetabs-<?php echo $yearType['year_type_id']; ?>">
					<ul>
					<?php
					foreach($statusList as $id=>$leaveTabType)
					{
						echo "<li><a onclick=\"uncheckAll(document.year_type_".$yearType['year_type_id'].".elements['leavesCheckBox[]'],0)\" href=\"#leavetabs-".$yearType['year_type_id']."-".$leaveTabType['status_id']."\" style=\"font-size:12px\">".$leaveTabType['name']."(".$leaveTabType['num_leaves'].")</a></li>";
					}
					?>
					</ul>
					<?php
					foreach($statusList as $id=>$leaveTabType)
					{
						echo "<div id=\"leavetabs-".$yearType['year_type_id']."-".$leaveTabType['status_id']."\">";
                                               

						if($leavesShowUser != $loggedUser->getUserId())
						{
							echo "<input class=\"ui-state-default ui-corner-all\" type=\"submit\" name=\"approveLeaves\" value=\"Approve\">";
							echo "<input class=\"ui-state-default ui-corner-all\" type=\"submit\" name=\"notApproveLeaves\" value=\"Do Not Approve\">";
						}
						elseif($loggedUser->getSupervisorId()!=0 && $leaveTabType['status_id']!=APPROVED)
						{
							echo "<input class=\"ui-state-default ui-corner-all\" type=\"submit\" name=\"requestApproval\" value=\"Request Approval\">";
						}
						if($loggedUser->getUserPermId()==ADMIN || $leaveTabType['status_id']==NEW_LEAVE || $leaveTabType['status_id']==WAITING_APPROVAL)
						{
							echo "<input class=\"ui-state-default ui-corner-all\" type=\"submit\" name=\"deleteLeave\" value=\"Delete\">";
						}
						echo "<input class=\"ui-state-default ui-corner-all\" type=\"button\" value=\"Check All\" onclick=\"checkByParent('leavetabs-".$yearType['year_type_id']."-".$leaveTabType['status_id']."',true)\">";
						echo "<input class=\"ui-state-default ui-corner-all\" type=\"button\" value=\"Uncheck All\" onclick=\"uncheckAll(document.year_type_".$yearType['year_type_id'].".elements['leavesCheckBox[]'],0)\">";

						echo "<table class=\"hover_table\" id=\"".$leaveTabType['status_id']."_".$yearType['year_type_id']."_leaves_table\">";
						echo $helperClass->DrawLeavesTableRows($leavesShowUser,$leaveTabType['status_id'],$yearSelected);
                                                
						echo "</table>";
                                                
                                                
                                                echo "</div>";
					}
                                        
                                        
					echo "</div>";
                                        

					echo "</div>";
                                        
					echo "</form>";
				}
                                
                                // Report panel
                                if($loggedUser->getUserPermId() == ADMIN) {
 
					echo "<input type=\"hidden\" name=\"displayUserLeaves\" value=\"".$leavesShowUser."\">";
					echo "<div id=\"yeartabs-"."report"."\">";

                                                                        echo "<form name=\"year_type_"."report"."\" action=\"index.php?view=create#yeartabs-"."report"."\" method=\"post\">";
                                // prev/ next arrows

                                $app_year_type_id=$yearTypes[0]['year_type_id']; 
                                $fisc_year_type_id=$yearTypes[1]['year_type_id']; 
                                
                                // default to today
                                $appointment_year_id = $years->GetYearId(Date('d'),Date('m'),Date('Y'),$yearTypes[0]['year_type_id']);
                                $fiscal_year_id = $years->GetYearId(Date('d'),Date('m'),Date('Y'),$yearTypes[1]['year_type_id']);
                                                        
                                echo "<input type=\"hidden\" name=\"displayUserLeaves\" value=\"".$leavesShowUser."\">";
                                echo "<input type=submit value=\"\" name=\"decYear-".$app_year_type_id."\" class=\"left_button\"><input type=submit value=\"\" name=\"incYear-".$app_year_type_id."\" class=\"right_button\">";
						
						
						if(isset($_POST["app-year-".$app_year_type_id]))
						{
							$appointment_year_id= $_POST["app-year-".$app_year_type_id];
                                                        
							if(isset($_POST["decYear-".$app_year_type_id]))
							{

								$prevYearSelected = $years->PrevYearId($_POST["app-year-".$app_year_type_id]);

								if($prevYearSelected)
								{
                                                                    
									$appointment_year_id = $prevYearSelected;
								}
                                                                
                                                                $prevYearSelected = $years->PrevYearId($_POST["fisc-year-".$fisc_year_type_id]);

								if($prevYearSelected)
								{
                                                                    
									$fiscal_year_id = $prevYearSelected;
								}
                                                                
                                                                
							}
							elseif(isset($_POST["incYear-".$app_year_type_id]))
							{
								$nextYearSelected = $years->NextYearId($_POST["app-year-".$app_year_type_id]);
								if($nextYearSelected)
								{
                                                                    
									$appointment_year_id = $nextYearSelected;
								}
                                                                
                                                                $nextYearSelected = $years->NextYearId($_POST["fisc-year-".$fisc_year_type_id]);
								if($nextYearSelected)
								{
                                                                    
									$fiscal_year_id = $nextYearSelected;
								}
							}
						}
						else
						{

							$appointment_year_id = $years->GetYearId(Date('d'),Date('m'),Date('Y'),$yearTypes[0]['year_type_id']);
                                                        $fiscal_year_id = $years->GetYearId(Date('d'),Date('m'),Date('Y'),$yearTypes[1]['year_type_id']);
						}
                                                
                                            echo "<input type=hidden value=".$appointment_year_id." name=\"app-year-".$app_year_type_id."\">";
                                            echo "<input type=hidden value=".$fiscal_year_id." name=\"fisc-year-".$fisc_year_type_id."\">";

                                        $dates = $years->GetYearDates($appointment_year_id);

                                        $start_date = $dates[0]['start_date'];
                                        $end_date = $dates[0]['end_date'];

                                        $mid_year = substr($end_date, 0, 4);

                                        $mid_date = $mid_year."-5-15";
                                        
                                        
                                        $yearInfo = $years->GetYearDates($appointment_year_id);
					echo "<b>".Date("F Y",strtotime($yearInfo[0]['start_date']))." - ".Date("F Y",strtotime($yearInfo[0]['end_date']))."<b>";
                                        echo("<BR><BR>");
                                        echo("<B><U>Pay Period 1 ($start_date - $mid_date)</U></B><BR><BR>");
                                        echo $helperClass->DrawLeavesTableRowsForReport($leavesShowUser, APPROVED, $appointment_year_id, $fiscal_year_id,1);
                                                                             
                                        $user = new User($sqlDataBase);
                                        $user->LoadUser($leavesShowUser);
                                        $user_email = $user->getUserEmail();
                                        
                                        $user_supervisor = $user->GetSupervisor();
                                        $user_supervisor_email = $user_supervisor->getUserEmail();
                                        if($debug) {
                                            $user_email = $loggedUser->getUserEmail();
                                            $user_supervisor_email = $loggedUser->getUserEmail();
                                        }
                                        $to = $user_email . "," . $user_supervisor_email;
                                        
                                       echo("<BR><U><a href='includes/vacation_excel.php?excel=1&user_id=".$leavesShowUser. "&status_id=". APPROVED . "&app_year_id=". $appointment_year_id . "&fisc_year_id=". $fiscal_year_id . "&pay_period=1' target='_blank'>Download Excel file (Pay Period 1)</A></U>");
                                       echo("<BR><BR><U><a href='index.php?view=create&email=true&user_id=".$leavesShowUser. "&status_id=". APPROVED . "&app_year_id=". $appointment_year_id . "&fisc_year_id=". $fiscal_year_id ."&pay_period=1' >Email Pay Period 1 Notice</A></U>");
                                       echo(" (Will send email to $to)<BR>");

                                        echo("<BR><BR><HR><BR><BR>");
                                        echo("<B><U>Pay Period 2 ($mid_date - $end_date)</U></B><BR><BR>");
                                        echo $helperClass->DrawLeavesTableRowsForReport($leavesShowUser, APPROVED, $appointment_year_id, $fiscal_year_id,2);

                                        
                                       echo("<BR><U><a href='includes/vacation_excel.php?excel=1&user_id=".$leavesShowUser. "&status_id=". APPROVED . "&app_year_id=". $appointment_year_id . "&fisc_year_id=". $fiscal_year_id . "&pay_period=2' target='_blank'>Download Excel file (Pay Period 2)</A></U>");
                                       echo("<BR><BR><U><a href='index.php?view=create&email=true&user_id=".$leavesShowUser. "&status_id=". APPROVED . "&app_year_id=". $appointment_year_id . "&fisc_year_id=". $fiscal_year_id ."&pay_period=2' >Email Pay Period 2 Notice</A></U>");
                                       echo(" (Will send email to $to)<BR>");
                                       echo("</div></form>");
                                // end Report panel
                                

                                }
				?>
				</div>
                                
		</td>
	</tr>
</table>
