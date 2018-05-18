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
    //$user_id, $status_id, $appointment_year_id, $fiscal_year_id, $db, $to, $from) 
    $user_id = $_GET['user_id'];
    $status_id = $_GET['status_id'];
    $app_year = $_GET['app_year_id'];
    $fisc_year = $_GET['fisc_year_id'];
    $to = $_GET['to'];
    //$from = $_GET['from'];
    $pay_period = $_GET['pay_period'];
    //echo("TO: $to<BR>");
    //echo("FROM: $from<BR>");
    //echo("EMAIL = ");
    sendEmail($user_id, $status_id, $app_year, $fisc_year, $to, $pay_period);
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
                                                    //echo("C");
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
                                echo "<form name=\"year_type_"."report"."\" action=\"index.php?view=create#yeartabs-"."report"."\" method=\"post\">";
					echo "<input type=\"hidden\" name=\"displayUserLeaves\" value=\"".$leavesShowUser."\">";
					echo "<div id=\"yeartabs-"."report"."\">";
                                        /*
                                        echo "<table class=\"hover_table\" id=\""."0"."_"."1"."_leaves_table\">";
                                        echo $helperClass->DrawLeavesTableRows($leavesShowUser,1,$yearSelected);
                                        echnao("</table>");
                                        echo "<table class=\"hover_table\" id=\""."0"."_"."25"."_leaves_table\">";
                                        echo $helperClass->DrawLeavesTableRows($leavesShowUser,25,$yearSelected);
                                        echo("</table");
                                        echo("TEST");*/
                                        $appointment_year_id = $years->GetYearId(Date('d'),Date('m'),Date('Y'),$yearTypes[0]['year_type_id']);
                                        $fiscal_year_id = $years->GetYearId(Date('d'),Date('m'),Date('Y'),$yearTypes[1]['year_type_id']);
                                        $dates = $years->GetYearDates($appointment_year_id);

                                        $start_date = $dates[0]['start_date'];
                                        $end_date = $dates[0]['end_date'];

                                        $mid_year = substr($end_date, 0, 4);

                                        $mid_date = $mid_year."-5-15";
                                        echo("<B><U>Pay Period 1 ($start_date - $mid_date)</U></B><BR><BR>");
                                        echo $helperClass->DrawLeavesTableRowsForReport($leavesShowUser, APPROVED, $appointment_year_id, $fiscal_year_id,1);
                                       
                                        $to = $loggedUser->getUserEmail(); 

                                        $from = $loggedUser->getUserEmail();
                                        $supervisor = $loggedUser->GetSupervisor();
                                        $supervisor_email = $supervisor->getUserEmail();
                                        
                                        echo("<BR><U><a href='includes/vacation_excel.php?excel=1&user_id=".$leavesShowUser. "&status_id=". APPROVED . "&app_year_id=". $appointment_year_id . "&fisc_year_id=". $fiscal_year_id . "&pay_period=1' target='_blank'>Download Excel file (Pay Period 1)</A></U>");
                                       //echo("<BR><BR><U><a href='index.php?view=create&email=true&user_id=".$leavesShowUser. "&status_id=". APPROVED . "&app_year_id=". $appointment_year_id . "&fisc_year_id=". $fiscal_year_id . "&from=". $from . "&to=". $to ."&supervisorEmail=".$supervisor_email."&pay_period=1' >Email Period 1 Notice</A></U>");

                                       
                                        
                                        echo("<BR><BR><HR><BR><BR>");
                                        echo("<B><U>Pay Period 2 ($mid_date - $end_date)</U></B><BR><BR>");
                                        echo $helperClass->DrawLeavesTableRowsForReport($leavesShowUser, APPROVED, $appointment_year_id, $fiscal_year_id,2);
                                        //echo("</table>");
                                        
                                       echo("<BR><U><a href='includes/vacation_excel.php?excel=1&user_id=".$leavesShowUser. "&status_id=". APPROVED . "&app_year_id=". $appointment_year_id . "&fisc_year_id=". $fiscal_year_id . "&pay_period=2' target='_blank'>Download Excel file (Pay Period 2)</A></U>");
                                       echo("</div></form>");
                                // end Report panel
                                
                                    //echo("<form action='index.php?view=create&excel=1' method='POST'>");
                                //echo("<form action='index.php?view=create&excel=1' method='POST'>");
                                    //echo "<BR><input class=\"ui-state-default ui-corner-all\" type=\"submit\" name=\"excel\" value=\"Export to Excel\">";
                                    //echo("</form>");
                                //echo("<a href='excel/vacation.xls' target=_blank>Download Excel file</A>");
                                }
				?>
				</div>
                                
		</td>
	</tr>
</table>
<?php

/*
if(isset($_GET['excel'])) {
    $user_id = $_GET['user_id'];
    $status_id = $_GET['status_id'];
    $app_year_id = $_GET['app_year_id'];
    $fisc_year_id = $_GET['fisc_year_id'];
    $pay_period = $_GET['pay_period'];
    writeExcel($user_id, $status_id, $app_year_id, $fisc_year_id, $sqlDataBase, $pay_period);
}
*/
function sendEmail($user_id, $status_id, $appointment_year_id, $fiscal_year_id, $to, $pay_period) {
    
    
    global $sqlDataBase;
    global $loggedUser;
    
    $from = $loggedUser->GetUserEmail();

           $years = new Years($sqlDataBase);
           $appYearInfo = $years->GetYearDates($appointment_year_id);
           $fiscYearInfo = $years->GetYearDates($fiscal_year_id);
           
           $start_year = Date("Y",strtotime($appYearInfo[0]['start_date']));
           $end_year = Date("Y",strtotime($appYearInfo[0]['end_date']));
                   
           $start_date = $start_year . "-08-15";
           $end_date = $end_year. "-05-15";
           //echo("curr Pay period = $curr_pay_period<BR>");
           if($pay_period == 2) {
               $start_date = $end_year . "-05-15";
               $end_date = $end_year . "-08-15";
               
           }
    
     $vacationLeaves = "SELECT li.leave_id, DATE_FORMAT(li.date,'%c-%e-%Y') as date, li.leave_hours, TIME_FORMAT(SEC_TO_TIME(li.time), '%kh %im') as time, li.description, lt.name, s.name as statusName, li.leave_type_id_special, lts.name as special_name
				FROM (leave_info li)
				JOIN leave_type lt ON li.leave_type_id=lt.leave_type_id
				JOIN status s ON li.status_id = s.status_id
				LEFT JOIN leave_type lts ON lts.leave_type_id = li.leave_type_id_special
				WHERE li.user_id =".$user_id." AND li.status_id=".$status_id." AND li.year_info_id=".$appointment_year_id."
                                AND lt.name != 'Sick'
                                and date between '$start_date' and '$end_date' 
				ORDER BY li.date DESC";
    
    $sickLeaves = "SELECT li.leave_id, DATE_FORMAT(li.date,'%c-%e-%Y') as date, li.leave_hours, TIME_FORMAT(SEC_TO_TIME(li.time), '%kh %im') as time, li.description, lt.name, s.name as statusName, li.leave_type_id_special, lts.name as special_name
				FROM (leave_info li)
				JOIN leave_type lt ON li.leave_type_id=lt.leave_type_id
				JOIN status s ON li.status_id = s.status_id
				LEFT JOIN leave_type lts ON lts.leave_type_id = li.leave_type_id_special
				WHERE li.user_id =".$user_id." AND li.status_id=".$status_id." AND li.year_info_id=".$appointment_year_id."
                                AND lt.name = 'Sick'
                                and date between '$start_date' and '$end_date' 
				ORDER BY li.date DESC";
    
    $floatingLeaves = "SELECT li.leave_id, DATE_FORMAT(li.date,'%c-%e-%Y') as date, li.leave_hours, TIME_FORMAT(SEC_TO_TIME(li.time), '%kh %im') as time, li.description, lt.name, s.name as statusName, li.leave_type_id_special, lts.name as special_name
				FROM (leave_info li)
				JOIN leave_type lt ON li.leave_type_id=lt.leave_type_id
				JOIN status s ON li.status_id = s.status_id
				LEFT JOIN leave_type lts ON lts.leave_type_id = li.leave_type_id_special
				WHERE li.user_id =".$user_id." AND li.status_id=".$status_id." AND li.year_info_id=".$fiscal_year_id."
                                ORDER BY li.date DESC";
    $vacation_results = $sqlDataBase->query($vacationLeaves);
    $sick_results = $sqlDataBase->query($sickLeaves);
    $floating_results = $sqlDataBase->query($floatingLeaves);
    $user = new User($sqlDataBase);
    $user->LoadUser($user_id);
    $supervisor = new User($sqlDataBase);
    $supervisor->LoadUser($user->getSupervisorId());
    $supervisorEmail = $supervisor->GetUserEmail();
    $message = "TESTING: Would normally go to: ".$user->GetUserEmail() . "," .$supervisorEmail."\n";
    $message .= "Instead, going to: ".$to."\n";
    $message .= "". $user->getFirstName()." ".$user->getLastName().",\n
       
Please find attached your Vacation & Sick leave usage for the period of $start_date-$end_date.\n

If you & your supervisor can forward me your confirmation no later than, Monday, August 21, 2017, that would be great.
If you have any questions, just let me know.\n

Thanks for your assistance in this process,\n"
            . $loggedUser->getFirstName() . " " . $loggedUser->getLastName();
    
    $emailText = $message . "\n\n";
    $emailText .= "---------------\n";
    //$myArr=array("Date","Type","Special","Charge Time","Actual Time","Description","Status");
    $titles = "Date\tType\tSpecial\tCharge Time\tActual Time\tDescription\tStatus\n\n";
    $emailText .= "Vacation Time\n";
    $emailText .= $titles;
    
    	for ($i = 0; $i < count($vacation_results); $i++) {
		//$thisuser = new user($dbase, $search_results[$i]['user_id']);
            /*
		$line = array($search_results[$i]['first_name'] . " " . $search_results[$i]['last_name'],
					  $search_results[$i]['email'],
					  $search_results[$i]['theme_name'],
					  $search_results[$i]['type_name'],
					  $search_results[$i]['igb_room'],
					  get_address($db, $search_results[$i]['user_id'], "HOME")

					  );
             * 
             */
            $emailText .= $vacation_results[$i]['date'] . "\t" .
                            $vacation_results[$i]['name'] . "\t" .
                            $vacation_results[$i]['special_name'] . "\t" .
                            $vacation_results[$i]['leave_hours'] . "\t" .
                            $vacation_results[$i]['time'] . "\t" .
                            $vacation_results[$i]['description'] . "\t" .
                            $vacation_results[$i]['statusName'] . "\n\n";
                    
                  
		
	}
        
                $emailText .= "Sick Leave\n\n";
        
	$emailText .=  "Date\tType\tSpecial\tCharge Time\tActual Time\tDescription\tStatus\n\n";
	
        
	for ($i = 0; $i < count($sick_results); $i++) {
		//$thisuser = new user($dbase, $search_results[$i]['user_id']);
            /*
		$line = array($search_results[$i]['first_name'] . " " . $search_results[$i]['last_name'],
					  $search_results[$i]['email'],
					  $search_results[$i]['theme_name'],
					  $search_results[$i]['type_name'],
					  $search_results[$i]['igb_room'],
					  get_address($db, $search_results[$i]['user_id'], "HOME")

					  );
             * 
             */
            $emailText .= $sick_results[$i]['date'] . "\t" .
                            $sick_results[$i]['name'] . "\t" .
                            $sick_results[$i]['special_name'] . "\t" .
                            $sick_results[$i]['leave_hours'] . "\t" .
                            $sick_results[$i]['time'] . "\t" .
                            $sick_results[$i]['description'] . "\t" .
                            $sick_results[$i]['statusName'] . "\n\n";
                    
	}
        
        $emailText .= "Floating Holidays\n\n";
        
	$emailText .= $titles;
        
        //$excel->writeLine($queryLeaves);
	for ($i = 0; $i < count($floating_results); $i++) {
		//$thisuser = new user($dbase, $search_results[$i]['user_id']);
            /*
		$line = array($search_results[$i]['first_name'] . " " . $search_results[$i]['last_name'],
					  $search_results[$i]['email'],
					  $search_results[$i]['theme_name'],
					  $search_results[$i]['type_name'],
					  $search_results[$i]['igb_room'],
					  get_address($db, $search_results[$i]['user_id'], "HOME")

					  );
             * 
             */
            $emailText .= $floating_results[$i]['date']. "\t" .
                            $floating_results[$i]['name']. "\t" .
                            $floating_results[$i]['special_name']. "\t" .
                            $floating_results[$i]['leave_hours']. "\t" .
                            $floating_results[$i]['time']. "\t" .
                            $floating_results[$i]['description']. "\t" .
                            $floating_results[$i]['statusName']. "\n\n";
                    
	}
        
        echo($emailText);
        //mail($recipient, $subject, $mail_body,$header);
        $subject = "Vacation/Sick Leave Usage for ".$user->GetNetid()." ( $start_date - $end_date ) TEST";
        mail($to, $subject, $emailText, "From:$from");
}

?>