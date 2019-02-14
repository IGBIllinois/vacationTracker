<?php
/**
 * Class Helper.php
 * This class contains a bunch of functions which do
 * trivial jobs that do not justify putting them in any of the
 * other classes.
 *
 * @author nevoband
 *
 */
class Helper
{

/*
	const APPROVED = 2;
	const APPOINTMENT_YEAR = 1;
	const FISCAL_YEAR = 25;
	const AP_HOURS_BLOCK = 4;
	*/

	private $sqlDataBase;

	public function __construct(SQLDataBase $sqlDataBase)
	{
		$this->sqlDataBase = $sqlDataBase;
	}

	public function __destruct()
	{

	}

	/**
	 * Draws a table with all leaves created for a particular user
	 * on a given year with a certain status.
	 *
	 * @param int $userId Id of the user to draw table for
	 * @param int $statusId Status ID of the leaves
	 * @param int $yearId ID of the year to get leaves for 
	 * @param int $limit Maximum number of records to display, or 0 if no limit
         * 
         * @return string an HTML table displaying the Leave information
	 */
	public function DrawLeavesTableRows($userId, $statusId,$yearId, $limit=0)
	{
		$tableString = "";

                
                $queryLeaves = "SELECT li.leave_id, DATE_FORMAT(li.date,'%c-%e-%Y') as date, li.leave_hours, TIME_FORMAT(SEC_TO_TIME(li.time), '%kh %im') as time, li.description, lt.name, s.name as statusName, li.leave_type_id_special, lts.name as special_name
				FROM (leave_info li)
				JOIN leave_type lt ON li.leave_type_id=lt.leave_type_id
				JOIN status s ON li.status_id = s.status_id
				LEFT JOIN leave_type lts ON lts.leave_type_id = li.leave_type_id_special
				WHERE li.user_id = :userId AND li.status_id=:statusId AND li.year_info_id=:yearId
				ORDER BY li.date DESC";
                
                $params = array("userId"=>$userId, "statusId"=>$statusId, "yearId"=>$yearId);
                    
		if($limit>0)
		{
			$queryLeaves = $queryLeaves." LIMIT :limit";
                        $params["limit"] = $limit;
		}

                
                
                $leaves = $this->sqlDataBase->get_query_result($queryLeaves, $params);
                
		$tableString.= "<thead><tr>";
		$tableString.= "<td>Select</td>";
		$tableString.= "<th>Date</th>";
		$tableString.= "<th>Type</th>";
		$tableString.= "<th>Special</th>";
		$tableString.= "<th>Charge Time</th>";
		$tableString.= "<th>Actual Time</th>";
		$tableString.= "<th>Description</th>";
		$tableString.= "<th>Status</th>";
		$tableString.= "</tr></thead><tbody>";

		if(count($leaves) > 0)
		{
			foreach($leaves as $id => $leave)
			{
				$tableString.= "<tr><td><input type=checkbox name=\"leavesCheckBox[]\" value=\"".$leave['leave_id']."\"></td><td>".$leave['date']."</td><td>".$leave['name']."</td><td>".$leave['special_name']."</td><td>".$leave['leave_hours']."</td><td>".$leave['time']."</td><td>".$leave['description']."</td><td>".$leave['statusName']."</td></tr>";
			}
		}
		else
		{
			$tableString .= "<tr><td colspan=8><center>No Leaves</center></td></tr>";
		}
		$tableString.="</tbody>";
		return $tableString;
	}
        
        /**
	 * Draws a table with all leaves created for a particular user
	 * on a given year with a certain status.
	 *
	 * @param int $userId Id of the user to draw table for
	 * @param int $statusId Status ID of the leaves
	 * @param int $appointment_year_id Id of the Appointment Year to get leaves for
         * @param int $fiscal_year_id ID of the Fiscal Year to get Floating Holidays for
         * $param int $pay_period Pay period id (1 = first pay period (8/15 - 5/15)
         *                                      2 = second pay period (5/15 - 8/15))
         *              Defaults to 1
         * 
	 * @param int $limit Limit of how many entries to display in each table, or 0 if no limit
	 */
	public function DrawLeavesTableRowsForReport($userId, $statusId,$appointment_year_id, $fiscal_year_id, $pay_period=1, $limit=0)
	{
            
            // Regular leave
            
           $years = new Years($this->sqlDataBase);
           $appYearInfo = $years->GetYearDates($appointment_year_id);
           $fiscYearInfo = $years->GetYearDates($fiscal_year_id);
           
           $start_year = Date("Y",strtotime($appYearInfo[0]['start_date']));
           $end_year = Date("Y",strtotime($appYearInfo[0]['end_date']));
           
           $start_date = $start_year . "-08-15";
           $end_date = $end_year. "-05-15";

           if($pay_period == 2) {
               $start_date = $end_year . "-05-15";
               $end_date = $end_year . "-08-15";
               
           }
           
	   $appYear = "<b>".Date("F Y",strtotime($appYearInfo[0]['start_date']))." - ".Date("F Y",strtotime($appYearInfo[0]['end_date']))."<b>";
           $fiscYear = "<b>".Date("F Y",strtotime($fiscYearInfo[0]['start_date']))." - ".Date("F Y",strtotime($fiscYearInfo[0]['end_date']))."<b>";
           
           
		$tableString = "Vacation: ( $start_date - $end_date ) <BR>";
    
                $tableString.= "<table class=\"hover_table\" id=\""."report_table\">";
		
                $user = new User($this->sqlDataBase);
                $user->LoadUser($userId);
                $leaves = $user->GetVacationLeaves($appointment_year_id, $pay_period, $statusId);
                
		$tableString.= "<thead><tr>";
		$tableString.= "<td>Select</td>";
		$tableString.= "<th>Date</th>";
		$tableString.= "<th>Type</th>";
		$tableString.= "<th>Special</th>";
		$tableString.= "<th>Charge Time</th>";
		$tableString.= "<th>Actual Time</th>";
		$tableString.= "<th>Description</th>";
		$tableString.= "<th>Status</th>";
		$tableString.= "</tr></thead><tbody>";

		if(count($leaves) > 0)
		{
			foreach($leaves as  $leave)
			{
                            $leaveType = new LeaveType($this->sqlDataBase);
                            $leaveType->LoadLeaveType($leave->getLeaveTypeId());
                            $tableString.= "<tr><td><input type=checkbox name=\"leavesCheckBox[]\" value=\"".
                                    $leave->getLeaveId()."\"></td><td>".
                                    $leave->GetDate()."</td><td>".
                                    $leaveType->getName()."</td><td>".
                                    $leaveType->getSpecial()."</td><td>".
                                    $leave->GetHours()."</td><td>".
                                    gmdate('g\h i\m',$leave->GetTime())."</td><td>".
                                    $leave->getDescription()."</td><td>".
                                    $leave->GetStatusString()."</td></tr>";
			}
		}
		else
		{
			$tableString .= "<tr><td colspan=8><center>No Leaves</center></td></tr>";
		}
		$tableString.="</tbody></table>";
		
                // Sick Leave
                $tableString .= "Sick Leave: ( $start_date - $end_date  )<BR>";

                $tableString .= "<table class=\"hover_table\" id=\""."report_table\">";

                $leaves = $user->GetSickLeaves($appointment_year_id, $pay_period, $statusId);
                
		$tableString.= "<thead><tr>";
		$tableString.= "<td>Select</td>";
		$tableString.= "<th>Date</th>";
		$tableString.= "<th>Type</th>";
		$tableString.= "<th>Special</th>";
		$tableString.= "<th>Charge Time</th>";
		$tableString.= "<th>Actual Time</th>";
		$tableString.= "<th>Description</th>";
		$tableString.= "<th>Status</th>";
		$tableString.= "</tr></thead><tbody>";

		if(count($leaves) > 0)
		{
			foreach($leaves as  $leave)
			{
			    $leaveType = new LeaveType($this->sqlDataBase);
                            $leaveType->LoadLeaveType($leave->getLeaveTypeId());
                            $tableString.= "<tr><td><input type=checkbox name=\"leavesCheckBox[]\" value=\"".
                                    $leave->getLeaveId()."\"></td><td>".
                                    $leave->GetDate()."</td><td>".
                                    $leaveType->getName()."</td><td>".
                                    $leaveType->getSpecial()."</td><td>".
                                    $leave->GetHours()."</td><td>".
                                    gmdate('g\h i\m',$leave->GetTime())."</td><td>".
                                    $leave->getDescription()."</td><td>".
                                    $leave->GetStatusString()."</td></tr>";
                            
                        }
		}
		else
		{
			$tableString .= "<tr><td colspan=8><center>No Leaves</center></td></tr>";
		}
		$tableString.="</tbody></table>";
                
                // Floating holidays
                                
		$tableString .= "Floating Holidays: ( $fiscYear ) <BR>";

                $leaves = $user->GetFloatingHolidays($fiscal_year_id, $pay_period, $statusId);
                $tableString .= "<table class=\"hover_table\" id=\""."report_table\">";
		$tableString.= "<thead><tr>";
		$tableString.= "<td>Select</td>";
		$tableString.= "<th>Date</th>";
		$tableString.= "<th>Type</th>";
		$tableString.= "<th>Special</th>";
		$tableString.= "<th>Charge Time</th>";
		$tableString.= "<th>Actual Time</th>";
		$tableString.= "<th>Description</th>";
		$tableString.= "<th>Status</th>";
		$tableString.= "</tr></thead><tbody>";

		if(count($leaves) > 0)
		{

			foreach($leaves as $leave)
			{
                            $leaveType = new LeaveType($this->sqlDataBase);
                            $leaveType->LoadLeaveType($leave->getLeaveTypeId());
				$tableString.= "<tr><td><input type=checkbox name=\"leavesCheckBox[]\" value=\"".
                                        $leave->getLeaveId()."\"></td><td>".
                                        $leave->GetDate()."</td><td>".
                                        $leaveType->getName()."</td><td>".
                                        $leaveType->getSpecial()."</td><td>".
                                        $leave->GetHours()."</td><td>".
                                        gmdate('g\h i\m',$leave->GetTime())."</td><td>".
                                        $leave->getDescription()."</td><td>".
                                        $leave->GetStatusString()."</td></tr>";
			}
		}
		else
		{
			$tableString .= "<tr><td colspan=8><center>No Leaves</center></td></tr>";
		}
                        if($pay_period == 2) {
            //write yearly totals
            $userLeavesHoursAvailable = new Rules($this->sqlDataBase);
            $leavesAvailable = $userLeavesHoursAvailable->LoadUserYearUsageCalc($userId,$appointment_year_id);
            
            $totalVacHours = round(($leavesAvailable[1]['initial_hours']+$leavesAvailable[1]['added_hours']-$leavesAvailable[1]['calc_used_hours']),2);
            $estimatedVacHours = round(($leavesAvailable[1]['initial_hours']+$leavesAvailable[1]['est_added_hours']-$leavesAvailable[1]['calc_used_hours']),2);
            
            $totalSickHours = round(($leavesAvailable[2]['initial_hours']+$leavesAvailable[2]['added_hours']-$leavesAvailable[2]['calc_used_hours']),2);
            $estimatedSickHours = round(($leavesAvailable[2]['initial_hours']+$leavesAvailable[2]['est_added_hours']-$leavesAvailable[2]['calc_used_hours']),2);
            
            $data[] = (array());
            $total_vac_hours = $leavesAvailable[1]['calc_used_hours'];
            $total_sick_hours = $leavesAvailable[2]['calc_used_hours'];
            
            $tableString.=("<BR>Yearly Total Vacation Hours Taken:". round($leavesAvailable[1]['calc_used_hours'],2));
            $tableString.=(("<BR>Yearly Total Sick Hours Taken:". round($leavesAvailable[2]['calc_used_hours'],2)));
            
            $tableString.=(("<BR>Vacation Hours Available:". $estimatedVacHours));
            $tableString.=(("<BR>Sick Hours Available:". $estimatedSickHours));
        }
        
		$tableString.="</tbody></table>";
                
                return $tableString;
	}

	/**
	 *
	 * Draw leave types available hours for a given user on a given year
	 *
	 * @param int $yearId ID for the Year to get data from
	 * @param int $userId ID for the User to get data for
	 * @param unknown_type $edit
	 */
	public function DrawLeaveHoursAvailable($yearId,$userId, $edit)
	{
		$tableString ="";
		$currentDate = Date("Y-m-d");


		$years = new Years($this->sqlDataBase);
		$thisPayPeriodId = $years->GetPayPeriodId(Date("d"),Date("m"),Date("Y"),$yearId);
		$userLeavesHoursAvailable = new Rules($this->sqlDataBase);

		$leavesAvailable = $userLeavesHoursAvailable->LoadUserYearUsageCalc($userId,$yearId,$thisPayPeriodId);
		$tableString.= "<tr>";
		$tableString.= "<td class=\"col_title\">Name</td>";
		$tableString.= "<td class=\"col_title\">Initial Hours <img title=\"<b>Initial Hours:</b> <br>Hours accumulated during the previous year,\nrolled over into this this fiscal year.\" src=\"css/images/question.png\"></td>";
		$tableString.= "<td class=\"col_title\">Added Hours <img title=\"<b>Added Hours:</b> <br>Hours earned during this year until now.\" src=\"css/images/question.png\"></td>";
		$tableString.= "<td class=\"col_title\">Used Hours <img title=\"<b>Used Hours:</b> <br>Hours used during this year.\" src=\"css/images/question.png\"></td>";
		$tableString.= "<td class=\"col_title\">Available Hours <img title=\"<b>Available Hours:</b> <br>Hours which can be used during this year.\" src=\"css/images/question.png\"></td>";
		$tableString.= "<td class=\"col_title\">Est. Available Hours <img title=\"<b>Estimate Available Hours:</b> <br>Estimated available hours by end of year.\" src=\"css/images/question.png\"></td>";
		$tableString.= "</tr>";

		if(isset($leavesAvailable))
		{
			foreach($leavesAvailable as $id=>$leaveAvailable)
			{
                         // Leave Type 10 = Non-cumulative sick leave, and shouldn't be shown until after the current pay period has passed
                        if($thisPayPeriodId != 0 && $leaveAvailable['leave_type_id'] == 10) {
                        } else {
				$totalHours = round(($leaveAvailable['initial_hours']+$leaveAvailable['added_hours']-$leaveAvailable['calc_used_hours']),2);
				$estimatedHours = round(($leaveAvailable['initial_hours']+$leaveAvailable['est_added_hours']-$leaveAvailable['calc_used_hours']),2);

				if($totalHours<0)
				{
					$formFieldClass="col_notice";
				}
				else
				{
					$formFieldClass="col";
				}
				if($leaveAvailable['initial_hours'] || $leaveAvailable['added_hours'] || $leaveAvailable['calc_used_hours'])
				{
					$tableString .= "<tr>
                          	<td class=\"form_field\"><b>".$leaveAvailable['name']." Hours </b><img title=\"<b>".$leaveAvailable['name']." Leave</b> <br>".$leaveAvailable['description']."\" src=\"css/images/question.png\"></td>
                           	<td class=\"".$formFieldClass."\">".round($leaveAvailable['initial_hours'],2)."</td>
                       		<td class=\"".$formFieldClass."\">".round($leaveAvailable['added_hours'],2)."</td>
             				<td class=\"".$formFieldClass."\">".round($leaveAvailable['calc_used_hours'],2)."</td>
                          	<td class=\"".$formFieldClass."\" style=\"font-weight:bolder;\">".round($totalHours,2)."</td>
							<td class=\"".$formFieldClass."\" style=\"font-weight:bolder;\">".round($estimatedHours,2)."</td></tr>";
				}
			}
                        }
                        
		}

		return $tableString;
	}


	/**
	 * Create a leave
	 *
	 * @param unknown_type $userId
	 * @param unknown_type $days
	 * @param unknown_type $month
	 * @param unknown_type $year
	 * @param unknown_type $hours
	 * @param unknown_type $minutes
	 * @param unknown_type $leaveTypeId
	 * @param unknown_type $specialLeaveId
	 * @param unknown_type $description
	 * @param User $loggedUser
	 */
	public function CreateLeave($userId, $days,$month,$year, $hours, $minutes, $leaveTypeId, $specialLeaveId, $description, User $loggedUser)
	{

		$errors = 0;
		$success= 0;

		$years =  new Years($this->sqlDataBase);
		$leaveUser = new User($this->sqlDataBase);
		$leaveUser->LoadUser($userId);
		$rules = new Rules($this->sqlDataBase);
		$leaveType = new LeaveType($this->sqlDataBase);
		$leaveType->LoadLeaveType($leaveTypeId);

		$leaveBlockSec = AP_HOURS_BLOCK*60*60 * ($leaveUser->getPercent()/100);
		$minDate = 2147483647;

		$message = "";


		foreach($days as $day)
		{
			$date = $year."-".$month."-".$day;
			$hours = trim($hours," ");
			$minutes = trim($minutes," ");

			$hours = ($hours=="")?0:$hours;
			$minutes = ($minutes=="")?0:$minutes;

			if(is_numeric($hours) && is_numeric($minutes))
			{
				//Check to make sure logged user has permission to create leave for the selected user
				if($loggedUser->getUserId()==$userId || $loggedUser->isEmployee($userId) || ($loggedUser->getUserPermId()==ADMIN))
				{

					$timeSec = ($hours * 60 * 60) + ($minutes * 60);
					//if leave hours are less than 97.5% of a leave block then 0 hours charged
					//if leave hours are less than 98.75% of 2 leave blocks then 4 hours are charged
					//anything greater charge for the full day 2 hours blocks
					if(($timeSec / $leaveBlockSec) < .999)
					{
						$leaveDayHours = 0;
					} elseif($timeSec / (2*$leaveBlockSec) < .9995) {
						$leaveDayHours = $leaveBlockSec / 60 / 60;
					} else
					{
						$leaveDayHours = (2 * $leaveBlockSec) / 60 / 60;
					}

					$newLeave = new Leave($this->sqlDataBase);


					if($this->LeaveConflict($date,$leaveTypeId,$userId))
					{
						$message .= $this->MessageBox("Create Leave","There already exists a leave of the same type on ".$month."/".$day."/".$year.".","error");
					}
					else
					{
						list($year,$month,$day) = explode("-",$date);
						$yearId = $years->GetYearId($day,$month,$year,$leaveType->getYearTypeId());
						if($newLeave->CreateLeave($date,$timeSec,$leaveTypeId,$description,$userId,$specialLeaveId,$yearId, $leaveDayHours))
						{

							if($loggedUser->getAutoApprove() || $loggedUser->getSupervisorId()==0)
							{
								$newLeave->setStatusId(APPROVED);
								$newLeave->UpdateDb();
							}
							$this->RunRules($userId,$yearId);
						}
					}

				}
			}
		}

		return $message;
	}

	/**
	 * Delete a leave
	 *
	 * @param unknown_type $leaveIds
	 * @param User $loggedUser
	 */
	public function DeleteLeave($leaveIds, User $loggedUser)
	{
		$message = "";
		$leaveToDelete = new Leave($this->sqlDataBase);

		$years = new Years($this->sqlDataBase);
		$leaveType = new LeaveType($this->sqlDataBase);

		if(isset($leaveIds))
		{
			if(!is_array($leaveIds))
			{
				$leaveIds = array($leaveIds);
			}
			$message .= "<table width=\"100%\">";
			$rules = new Rules($this->sqlDataBase);
			foreach($leaveIds as $leaveId)
			{
				$leaveToDelete->LoadLeave($leaveId);
				$leaveType->LoadLeaveType($leaveToDelete->getLeaveTypeId());

				if($loggedUser->isEmployee($leaveToDelete->getUserId()) || ($loggedUser->getUserId()==$leaveToDelete->getUserId() && ($leaveToDelete->getStatusId()!=APPROVED || $loggedUser->getAutoApprove() || $loggedUser->getSupervisorId()==0 )) || ($loggedUser->getUserPermId()==ADMIN))
				{
					list($year,$month,$day) = explode("-",$leaveToDelete->getDate());
					$yearId = $years->GetYearId($day,$month,$year,$leaveType->getYearTypeId());
					$userId = $leaveToDelete->getUserId();
					$leaveToDelete->Delete();
					$this->RunRules($userId,$yearId);
					$message .= "<tr class=\"success_row\"><td>".Date('m/d/Y',strtotime($leaveToDelete->getDate()))."</td><td>".$leaveToDelete->getHours()." Hours</td><td>Deleted</td></tr>";
				}
				else
				{
					$message .="<tr class=\"failed_row\"><td>".Date('m/d/Y',strtotime($leaveToDelete->getDate()))."</td><td>".$leaveToDelete->getHours()." Hours</td><td>Permission Denied</td></tr>";
				}

			}
			$message .= "</table>";
			return $this->MessageBox("Delete Leave",$message);
		}
		else
		{
			return $this->MessageBox("Delete Leave","No leaves selected.","error");
		}
	}

	/**
	 * Modify a leave
	 *
	 * @param unknown_type $leaveId
	 * @param unknown_type $userId
	 * @param unknown_type $hours
	 * @param unknown_type $minutes
	 * @param unknown_type $leaveTypeId
	 * @param unknown_type $specialLeaveId
	 * @param unknown_type $description
	 * @param User $loggedUser
	 */
	public function ModifyLeave($leaveId, $userId, $hours, $minutes, $leaveTypeId, $specialLeaveId, $description, User $loggedUser)
	{
		$errors = 0;
		$success = 0;

		$leaveToModify = new Leave($this->sqlDataBase);
		$leaveToModify->LoadLeave($leaveId);

		$leaveUser = new User($this->sqlDataBase);
		$leaveUser->LoadUser($userId);

		$leaveType = new LeaveType($this->sqlDataBase);
		$leaveType->LoadLeaveType($leaveToModify->getLeaveTypeId());
                
                $newLeaveType = new LeaveType($this->sqlDataBase);
		$newLeaveType->LoadLeaveType($leaveTypeId);

		$leaveBlockSec = AP_HOURS_BLOCK*60*60 * ($leaveUser->getPercent()/100);

		if(($loggedUser->getUserId()==$leaveToModify->getUserId() && ($loggedUser->getSupervisorId()==0 || $leaveToModify->getStatusId()==NEW_LEAVE)) || $loggedUser->getUserPermId()==ADMIN || $loggedUser->isEmployee($leaveToModify->getUserId()))
		{
			$rules= new Rules($this->sqlDataBase);
			$years = new Years($this->sqlDataBase);

			$hours = trim($hours," ");
			$minutes = trim($minutes," ");

			$hours = ($hours=="")?0:$hours;
			$minutes = ($minutes=="")?0:$minutes;

			if(is_numeric($hours) && is_numeric($minutes))
			{
				$timeSec = ($hours * 60 * 60) + ($minutes * 60);

				if(($timeSec / $leaveBlockSec) < .999)
				{
					$leaveDayHours = 0;
				}
				elseif($timeSec / (2*$leaveBlockSec) < .9995)
				{
					$leaveDayHours = $leaveBlockSec / 60 / 60;
				}
				else
				{
					$leaveDayHours = (2 * $leaveBlockSec) / 60 / 60;
				}
                                list($year,$month,$day) = explode("-",$leaveToModify->getDate());
                                
                                $yearId = $years->GetYearId($day,$month,$year,$newLeaveType->getYearTypeId());
                                
				$leaveToModify->setTime($timeSec);
				$leaveToModify->setHours($leaveDayHours);
				$leaveToModify->setLeaveTypeId($leaveTypeId);
				$leaveToModify->setLeaveTypeIdSpecial($specialLeaveId);
				$leaveToModify->setUserId($userId);
				$leaveToModify->setDescription($description);
                                $leaveToModify->setYearId($yearId);
				$leaveToModify->UpdateDb();
				
				$this->RunRules($userId,$yearId);
			}

		}
		else
		{
			return $this->MessageBox("Modify Leave","You do not have permission to modify this leave.","error");
		}
	}

	/**
	 * Generate a pretty message box for error and informative messages
	 *
	 * @param unknown_type $title
	 * @param unknown_type $message
	 * @param unknown_type $type
	 */
	public static function MessageBox($title, $message,$type="info")
	{
		$messageString = "<div id=\"message_box\"><div class=\"ui-widget\">
					<div class=\"ui-state-";
		if($type=="error")
		{
			$messageString .="error ui-corner-all\" style=\"padding: 0 .7em;\">
					<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>";

		}
		elseif($type=="info")
		{
			$messageString .="highlight ui-corner-all\" style=\"padding: 0 .7em;\">
                                        <p><span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: .3em;\"></span>";
		}

		$messageString .= "<strong>".$title."</strong>: ".$message."</p></div></div></div>";

		return $messageString;
	}

	/**
	 * Add leave hours for a user or group of users.
	 * leave hours will show as Added Leave hours for the user also known as available hours.
	 *
	 * @param int $yearId ID of the year to add hours to
	 * @param int $leaveTypeId ID of the Type of Leave to add
	 * @param int $hoursToAdd Number of hours to add
	 * @param int $payPeriodId ID of the Pay Period to add to
	 * @param array $users Array of user info 
	 * @param string $description Description
	 * @param boolean $hoursAddBegin True if you are adding hours to the beginning of the pay period,
         *                              otherwise add at the end
	 * @param bool $onlyCurrentUsers True if only adding to current users, 
         *                              otherwise add to all users
	 * @param string $yearType Year type string ("appointment" for appointment year)
	 */
	public function AddLeaveHours($yearId,$leaveTypeId,$hoursToAdd,$payPeriodId,$users,$description,$hoursAddBegin, $onlyCurrentUsers, $yearType="appointment")
	{
		$leaveType = new LeaveType($this->sqlDataBase);
		$leaveType->LoadLeaveType($leaveTypeId);

		if(is_numeric($hoursToAdd) && $yearId>0 && $leaveTypeId>0 && $payPeriodId>0 && $yearType >0)
		{

                        $queryPayPeriodRange = "SELECT start_date,end_date FROM pay_period WHERE pay_period_id=:payPeriodId";
                        $params = array("payPeriodId"=>$payPeriodId);
                        $payPeriodRange = $this->sqlDataBase->get_query_result($queryPayPeriodRange, $params);

			$payPeriodEndDateEpoch = strtotime($payPeriodRange[0]['end_date']);
			$payPeriodStartDateEpoch = strtotime($payPeriodRange[0]['start_date']);
			$payPeriodTimeSec = $payPeriodEndDateEpoch - $payPeriodStartDateEpoch;
			foreach($users as $id=>$userInfo)
			{
                            $user = new User($this->sqlDataBase);
                            $user->LoadUser($userInfo['user_id']);
				$startDateCalculation="";

                                if($yearType=='fiscal') {
                                    $hoursToAddUser = $hoursToAdd;
                                } else {
                                    $hoursToAddUser = $hoursToAdd*($userInfo['percent']/100);
                                }
				$userStartDateEpoch = strtotime($userInfo['start_date']);
				if($userStartDateEpoch >= $payPeriodStartDateEpoch && $userStartDateEpoch <= $payPeriodEndDateEpoch && ($leaveType->getYearTypeId() != FISCAL_YEAR) )
				{
					$holidays=array();
					$workDaysInPayPeriod = $this->getWorkingDays($payPeriodRange[0]['start_date'],$payPeriodRange[0]['end_date'],$holidays);
					$daysWorked = $this->getWorkingDays($userInfo['start_date'],$payPeriodRange[0]['end_date'],$holidays);
					//Give partial leave time since user did not work the entire pay period
					$payPeriodWorkPercent = $daysWorked / $workDaysInPayPeriod;
					$hoursToAddUser = $hoursToAddUser * $payPeriodWorkPercent;
					$showCalculation= "<br><br>Days worked this pay period: ".(((($payPeriodEndDateEpoch-$userStartDateEpoch)/60)/60)/24)
						."<br>Days in pay period: ".(((($payPeriodTimeSec)/60)/60)/24)
						."<br>Percent worked this month: ".($payPeriodWorkPercent*100)."%"
						."<br>Percent employment: ".$userInfo['percent']."%"
						."<br>".$payPeriodWorkPercent."*".$hoursToAdd."*".($userInfo['percent']/100)."=".$hoursToAddUser;

                                        $queryAddUserHours = "INSERT INTO added_hours (
                                            hours,
                                            pay_period_id,
                                            leave_type_id,
                                            user_id,
                                            description, 
                                            year_info_id,
                                            begining_of_pay_period, 
                                            user_type_id, 
                                            date)
						VALUES(
                                                :hours,".
                                                ":payPeriodId,".
                                                ":leaveTypeId,".
                                                ":userId,".
                                                ":description,".
                                                ":year_info_id, ".
                                                ":hoursAddBegin, ".
                                                ":year_type_id, ".
                                                "NOW())";
                                        
                                        $params = array("hours"=>$hoursToAddUser,
                                            "payPeriodId"=>$payPeriodId,
                                            "leaveTypeId"=>$leaveTypeId,
                                            "user_id"=>$user->getUserId(),
                                            "description"=>$description,
                                            "year_info_id",$yearId,
                                            "hoursAddBegin"=>$hoursAddBegin,
                                            "year_type_id"=>$user->getUserTypeId());

                                        $this->sqlDataBase->get_query_result($queryAddUserHours, $params);
				}
				elseif($userStartDateEpoch >= $payPeriodEndDateEpoch)
				{
					//Do nothing the user doesn't recieve vacation days if he wasn't working at the time

				}
				elseif($userStartDateEpoch <= $payPeriodStartDateEpoch || ($leaveType->getYearTypeId() == FISCAL_YEAR) )
				{

					//Give full leave time since user worked prior to start to pay period

					$queryAddUserHours = "INSERT INTO added_hours (
                                            hours,
                                            pay_period_id,
                                            leave_type_id,
                                            user_id,
                                            description, 
                                            year_info_id,
                                            begining_of_pay_period, 
                                            user_type_id, 
                                            date)
						VALUES(".
                                                ":hours, ".
                                                ":payPeriodId, ".
                                                ":leaveTypeId, ".
                                                ":user_id, ".
                                                ":description, ".
                                                ":yearId, ".
                                                ":hoursAddBegin, ".
                                                ":user_type_id, ".
                                                "NOW())";
					
                                        $params = array("hours"=>$hoursToAddUser,
                                            "payPeriodId"=>$payPeriodId,
                                            "leaveTypeId"=>$leaveTypeId,
                                            "user_id"=>$user->getUserId(),
                                            "description"=>$description,
                                            "yearId"=>$yearId,
                                            "hoursAddBegin"=>$hoursAddBegin,
                                            "user_type_id"=>$user->getUserTypeId()
                                            );
                                        
                                        $this->sqlDataBase->get_insert_result($queryAddUserHours, $params);

					if($userInfo['user_id'])
					{
						$this->RunRules($userInfo['user_id'],$yearId,true);
					}
				}
			}
			//If we are updating more than one user prevent from spamming
			//Only show calculation for a single user change
			if(count($users)>1)
			{
				$showCalculation="";
			}
			{
				$showCalculation="<br><br>Hours added may vary based on employment percent.";
			}
			return $this->MessageBox("Hours Added","<br>".$hoursToAddUser." Hours added to ".$leaveType->getName().".".$showCalculation,"info");
		}
		else
		{
			if(!is_numeric($hoursToAdd))
			{
				return $this->MessageBox("Hours field","<br>Hours value is not a number.","error");
			}
			if($yearType <=0)
			{   
				return $this->MessageBox("Year Type field","<br>Year type not selected.","error");
			}
			if($yearId <=0)
			{
				return $this->MessageBox("Year field","<br>Year not selected.","error");
			}
			if($payPeriodId <=0)
			{
				return $this->MessageBox("Pay Period field","<br>Pay period not selected.","error");
			}
			if($leaveTypeId <=0)
			{
                            
				return $this->MessageBox("Leave Type field","<br>Leave type not selected.","error");
			}

		}



	}

	/**
	 * Add hours to a new user automatically according to their start date.
	 *
	 * @param unknown_type $userId
	 * @param unknown_type $startDate
	 */
	public function AddNewUserHours($userId,$startDate)
	{
		$queryNewUserAddedHours = "SELECT ah.pay_period_id, "
                        . "ah.leave_type_id, "
                        . "ah.hours, "
                        . "ah.description, "
                        . "ah.year_info_id, "
                        . "ah.begining_of_"
                        . "pay_period, "
                        . "pp.start_date, "
                        . "pp.end_date, "
                        . "pp.pay_period_id "
                        . "FROM added_hours ah, pay_period pp "
                        . "WHERE user_id=0 "
                        . "AND pp.pay_period_id=ah.pay_period_id "
                        . "WHERE pp.end_date>=:startDate";
                $params = array("startDate"=>$startDate);
		$newUserAddedHours = $this->sqlDataBase->get_query_result($queryNewUserAddedHours, $params);

		$queryUsersInfo = "SELECT percent, start_date, user_id FROM users WHERE user_id=:user_id";
                $user_params = array("user_id"=>$userId);
		$usersInfo = $this->sqlDataBase->get_query_result($queryUsersInfo, $user_params);

		foreach($newUserAddedHours as $id=> $newUserAddedHour)
		{
			if( strtotime($startDate)<=strtotime($newUserAddedHour['end_date']) && strtotime($startDate)>=strtotime($newUserAddedHour['start_date']))
			{
				$hoursToAdd = (strtotime($newUserAddedHour['end_date'])-strtotime($startDate)) / (strtotime($newUserAddedHour['end_date'])-strtotime($newUserAddedHour['start_date'])) * ah.hours;
			}
			else
			{
				$hoursToAdd = $newUserAddedHours['ah.hours'];
			}

			$this->AddLeaveHours($newUserAddedHour['year_info_id'],$newUserAddedHour['leave_type_id'],$hoursToAdd, $newUserAddedHour['pay_period_id'], $usersInfo, $newUserAddedHour['description'], $newUserAddedHour['begining_of_pay_period'], 1);
		}
	}


	/**
	 * Delete hours which were added to a user's available hours.
	 *
	 * @param unknown_type $addedHoursId
	 * @param User $loggedUser
	 */
	public function DeleteAddedHours($addedHoursId, User $loggedUser)
	{
		$years = new Years($this->sqlDataBase);
		$rules = new Rules($this->sqlDataBase);
		$queryAddedHours = "SELECT ah.user_id, pp.year_info_id "
                        . "FROM added_hours ah, pay_period pp "
                        . "WHERE pp.pay_period_id=ah.pay_period_id "
                        . "AND added_hours_id=:addedHoursId";
                $params = array("addedHoursId"=>$addedHoursId);

                $addedHours = $this->sqlDataBase->get_query_result($queryAddedHours, $params);

		if($loggedUser->getUserPermId()==ADMIN && isset($addedHours))
		{
			$queryDeleteAddedHours = "DELETE FROM added_hours WHERE added_hours_id=:addedHoursId";
                        $params = array("addedHoursId"=>$addedHoursId);
			$this->sqlDataBase->get_update_result($queryDeleteAddedHours, $params);
			if($addedHours[0]['user_id'])
			{
				$this->RunRules($addedHours[0]['user_id'],$addedHours[0]['year_info_id'],true);
			}
			return "";
		}
		else
		{
			return $this->MessageBox("Delete Leave","You do not have permission to delete this leave.","error");
		}

	}

	/**
	 * Check if a leave already exists for a user at a certain time which may cause a conflict.
	 *
	 * @param unknown_type $date
	 * @param unknown_type $leaveTypeId
	 * @param unknown_type $userId
	 */
	public function LeaveConflict($date,$leaveTypeId,$userId)
	{
		$queryLeaveConflicts = "SELECT leave_id FROM leave_info "
                        . "WHERE user_id=:userId "
                        . "AND date=:date "
                        . "AND leave_type_id=:leaveTypeId";
                
                $params = array("userId"=>$userId,
                                "date"=>$date,
                                "leaveTypeId"=>$leaveTypeId);

		$conflictingLeave = $this->sqlDataBase->get_query_result($queryLeaveConflicts, $params);

		if(isset($conflictingLeave) && count($conflictingLeave) > 0)
		{
			return $conflictingLeave[0]['leave_id'];
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Request approval for leave for a particular user.
	 *
	 * @param unknown_type $leaveIds
	 * @param User $loggedUser
	 */
	public function RequestLeavesApproval($leaveIds,User $loggedUser)
	{
		$mailError = 0;
		$approvedStatusError =0;
		$mailSentSuccess = 0;
		$message = "";
		$leaveUserId = 0;

		$requestApprovalMail =  new Email($this->sqlDataBase);

		if(isset($leaveIds))
		{
			$leavesToApprove = array();
			$confirmCode = md5(uniqid(rand()));
			$message .= "<table width=\"100%\">";
			$rules = new Rules($this->sqlDataBase);
			foreach($leaveIds as $leaveToApproveId)
			{
				$leaveToApprove = new Leave($this->sqlDataBase);
				$leaveToApprove->LoadLeave($leaveToApproveId);
				if($leaveToApprove->getStatusId()!=APPROVED && $leaveToApprove->getStatusId()!=NOT_APPROVED && $loggedUser->getUserId()==$leaveToApprove->getUserId())
				{
					$leavesToApprove[] = $leaveToApprove;
					$leaveUserId = $leaveToApprove->getUserId();
					$leaveToApprove->setStatusId(WAITING_APPROVAL);
					$leaveToApprove->UpdateDb();
					$queryAddAuthenKey = "INSERT INTO authen_key ("
                                                . "confirm_key,"
                                                . "leave_id,"
                                                . "status_id,"
                                                . "date_created,"
                                                . "supervisor_id,"
                                                . "cookie_created,"
                                                . "cookie)"
                                                . "VALUES(".
                                                    ":confirmKey, ".
                                                    ":leave_id, ".
                                                    ":status_id, ".
                                                    "NOW(), ".
                                                    ":supervisor_id," .
                                                    "0," .
                                                    "'')";
                                        $params = array("confirmKey"=>$confirmCode,
                                                        "leave_id"=>$leaveToApprove->getLeaveId(),
                                                        "status_id"=>APPROVED,
                                                        "supervisor_id"=>$loggedUser->getSupervisorId());
                                            
                                       // echo("addauthquery = $queryAddAuthenKey<BR>");
                                        //print_r($params);
                                        $this->sqlDataBase->get_insert_result($queryAddAuthenKey, $params);

					$message .= "<tr class=\"success_row\"><td>".Date('m/d/Y',strtotime($leaveToApprove->getDate()))."</td><td>".$leaveToApprove->getHours()." Hours</td><td>Requested</td></tr>";

				}
				else
				{
					$approvedStatusError++;
					$message .="<tr class=\"failed_row\"><td>".Date('m/d/Y',strtotime($leaveToApprove->getDate()))."</td><td>".$leaveToApprove->getHours()." Hours</td><td> Failed (Leave status is approved)</td></tr>";
				}
			}

			$message .= "</table>";

			if($requestApprovalMail->RequestLeaveApproval($leavesToApprove, $leaveUserId, $confirmCode))
			{
				$message = "E-Mail message was sent to supervisor".$message;
				$messageBox = $this->MessageBox("Approval Request",$message,"info");
			}
			else
			{
				$mailError++;
				$message = "No E-Mail request wast sent to supervisor.".$message;
				$messageBox = $this->MessageBox("Approval Request",$message,"error");
			}
		}else{
			$messageBox = $this->MessageBox("Approval Request","No leaves were selected.","error");

		}
		return $messageBox;
	}

	/**
	 * Set a leave status to approved and return a nice message box showing
	 * which leaves were approved.
	 *
	 * @param unknown_type $leaveIds
	 * @param User $loggedUser
	 */
	public function ApproveLeaves($leaveIds ,User $loggedUser)
	{
		$messageBox = "";
		$message = "";
		$leaveUserId = 0;

		if(isset($leaveIds))
		{
			if(!is_array($leaveIds))
			{
				$leaveIds = array($leaveIds);
			}
                        // Separate by user
                        $userLeaves = array(); // Array of array of user leaves. Key is the user id, value is list of leaves
                        foreach($leaveIds as $leaveSelected)
			{
				$leaveToApprove = new Leave($this->sqlDataBase);
				$leaveToApprove->LoadLeave($leaveSelected);
                                $userid = $leaveToApprove->getUserId();
                                if(array_key_exists($userid, $userLeaves)) {
                                    $userLeaves[$userid][] = $leaveSelected;
                                } else {
                                    $userLeaves[$userid] = array($leaveSelected);
                                }
                        }
                        
                                
			$message .= "<table width=\"100%\">";
			$rules = new Rules($this->sqlDataBase);
                        foreach($userLeaves as $userid=>$userLeaveList) {
                            
                            $leaveUserId = $userid;
                            $user = new User($this->sqlDataBase);

                            $user->LoadUser($leaveUserId);

                            foreach($userLeaveList as $leaveSelected)
                            {

                                    $leaveToApprove = new Leave($this->sqlDataBase);
                                    $leaveToApprove->LoadLeave($leaveSelected);

                                    //Check To Make sure that the logged in user is the supervisor of the user the leave belongs to
                                    if($loggedUser->isEmployee($leaveToApprove->getUserId()) || ($loggedUser->getUserPermId()==ADMIN))
                                    {
                                            $leaveUserId = $leaveToApprove->getUserId();
                                            $leaveToApprove->setStatusId(APPROVED);
                                            $leaveToApprove->UpdateDb();
                                            $this->RunRules($leaveToApprove->getUserId(),$leaveToApprove->getYearId());
                                            
                                            $message .= "<tr class=\"success_row\"><td>".$user->getNetid()."</td><td>".Date('m/d/Y',strtotime($leaveToApprove->getDate()))."</td><td>".$leaveToApprove->getHours()." Hours</td><td>Approved</td></tr>";
                                    }
                                    else
                                    {
                                            $message .="<tr class=\"failed_row\"><td>".$user->getNetid()."</td><td>".Date('m/d/Y',strtotime($leaveToApprove->getDate()))."</td><td>".$leaveToApprove->getHours()." Hours</td><td>Permission Denied</td></tr>";
                                    }
                            }

                            $replyApprovalMail =  new Email($this->sqlDataBase);
                            if($leaveUserId)
                            {
                                    $replyApprovalMail->ReplyLeaveApprovalStatus($userLeaveList,$leaveUserId,$loggedUser);
                            }
                        }
                        $message .= "</table>";
			$messageBox = $this->MessageBox("Approve Leaves",$message);
		}
                
		else
		{
			$messageBox = $this->MessageBox("Approve Leaves","No leaves were selected.","error");
		}
		return $messageBox;
			
	}

	/**
	 * Do not approve a leave and return a message box listing which leaves were not approved.
	 *
	 * @param unknown_type $leaveIds
	 * @param User $loggedUser
	 */
	public function DoNotApproveLeaves($leaveIds,User $loggedUser)
	{
		$messageBox = "";
		$message = "";
		$leavesUserId = 0;
		if(isset($leaveIds))
		{
			if(!is_array($leaveIds))
			{
				$leaveIds = array($leaveIds);
			}
                        // Separate by user
                        $userLeaves = array(); // Array of array of user leaves. Key is the user id, value is list of leaves
                        foreach($leaveIds as $leaveSelected)
			{
				$leaveToApprove = new Leave($this->sqlDataBase);
				$leaveToApprove->LoadLeave($leaveSelected);
                                $userid = $leaveToApprove->getUserId();
                                if(array_key_exists($userid, $userLeaves)) {
                                    $userLeaves[$userid][] = $leaveSelected;
                                } else {
                                    $userLeaves[$userid] = array($leaveSelected);
                                }
                        }
                        
			$message .= "<table width=\"100%\">";
			$rules = new Rules($this->sqlDataBase);
                        foreach($userLeaves as $userid=>$userLeaveList) {
                            
                            $leaveUserId = $userid;
                            $user = new User($this->sqlDataBase);
                            $user->LoadUser($leaveUserId);
                            
                            foreach($userLeaveList as $leaveSelected)
                            {
                                    $leaveToNotApprove = new Leave($this->sqlDataBase);
                                    $leaveToNotApprove->LoadLeave($leaveSelected);

                                    //Check To Make sure that the logged in user is the supervisor of the user the leave belongs to
                                    if($loggedUser->isEmployee($leaveToNotApprove->getUserId()) || ($loggedUser->getUserPermId()==ADMIN))
                                    {	
                                            $leaveUserId = $leaveToNotApprove->getUserId();
                                            $leaveToNotApprove->setStatusId(NOT_APPROVED);
                                            $leaveToNotApprove->UpdateDb();
                                            $this->RunRules($leaveToNotApprove->getUserId(),$leaveToNotApprove->getYearId());
                                            $message .= "<tr class=\"success_row\"><td>".$user->getNetid()."</td><td>".Date('m/d/Y',strtotime($leaveToNotApprove->getDate()))."</td><td>".$leaveToNotApprove->getHours()." Hours</td><td>Not Approved</td></tr>";

                                    }
                                    else
                                    {
                                            $message .="<tr class=\"failed_row\"><td>".$user->getNetid()."</td><td>".Date('m/d/Y',strtotime($leaveToNotApprove->getDate()))."</td><td>".$leaveToNotApprove->getHours()." Hours</td><td>Permission Denied</td></tr>";
                                    }
                            }
                            $replyApprovalMail =  new Email($this->sqlDataBase);
                            if($leaveUserId)
                            {
                                    $replyApprovalMail->ReplyLeaveApprovalStatus($userLeaveList,$leaveUserId,$loggedUser);
                            }
                        }
                        
			$message .= "</table>";
			

			$messageBox = $this->MessageBox("Do Not Approve Leaves",$message);
		}
		else
		{
			$messageBox = $this->MessageBox("Do Not Approve Leaves","No leaves were selected.","error");
		}
		return $messageBox;
	}

	/**
	 * Run rules for a user on a prticular year.
	 *
	 * @param unknown_type $userId
	 * @param unknown_type $yearId
	 */
	public function RunRules($userId,$yearId,$force=false)
	{
		$rules = null;
		$year = new Years($this->sqlDataBase);
		$yearTypeId = $year->GetYearTypeId($yearId);
		
		switch($yearTypeId)
		{
			case APPOINTMENT_YEAR:
				$rules = new AppointmentYearRules($this->sqlDataBase);
				break;
			case FISCAL_YEAR:
				$rules = new FiscalYearRules($this->sqlDataBase);
				break;
			default:
				$rules = null;
		}
	
	
                $rules->SetForceApplyRules($force);
              
		
		if($rules!=null)
		{
			if($rules->RunRules($userId,$yearId))
			{
				return 1;
			}
		}

		return 0;
	}

	public function RunRulesYearType($userId,$yearTypeId,$force=false)
	{
		$year = new Years($this->sqlDataBase);
		switch($yearTypeId)
		{
			case APPOINTMENT_YEAR:
				$rules = new AppointmentYearRules($this->sqlDataBase);
				break;
			case FISCAL_YEAR:
				$rules = new FiscalYearRules($this->sqlDataBase);
				break;
			default:
				$rules = null;
		}

		$rules->SetForceApplyRules($force);

		if($rules!=null)
		{
			$firstYearId = $year->GetFirstYearId($yearTypeId);
			$rules->SetForceApplyRules(true);
			if($rules->RunRules($userId,$firstYearId));
			{
				return 1;
			}
		}
		return 0;
	}

	//The function returns the no. of business days between two dates and it skips the holidays
	public function getWorkingDays($startDate,$endDate,$holidays)
	{
    		// do strtotime calculations just once
    		$endDate = strtotime($endDate);
    		$startDate = strtotime($startDate);


   		//The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
  		//We add one to inlude both dates in the interval.
    		$days = ($endDate - $startDate) / 86400 + 1;
	
	    	$no_full_weeks = floor($days / 7);
	    	$no_remaining_days = fmod($days, 7);
	
	    	//It will return 1 if it's Monday,.. ,7 for Sunday
	    	$the_first_day_of_week = date("N", $startDate);
	    	$the_last_day_of_week = date("N", $endDate);
	
	    	//---->The two can be equal in leap years when february has 29 days, the equal sign is added here
	    	//In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
	    	if ($the_first_day_of_week <= $the_last_day_of_week) 
		{
	        	if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) $no_remaining_days--;
	        	if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) $no_remaining_days--;
	    	}
	    	else 
		{
	        	// (edit by Tokes to fix an edge case where the start day was a Sunday
	        	// and the end day was NOT a Saturday)
	
	        	// the day of the week for start is later than the day of the week for end
	        	if ($the_first_day_of_week == 7) 
			{
	            		// if the start date is a Sunday, then we definitely subtract 1 day
	            		$no_remaining_days--;

	            		if ($the_last_day_of_week == 6) 
				{
	                		// if the end date is a Saturday, then we subtract another day
	                		$no_remaining_days--;
	            		}
	        	}
	       		else 
			{
	            		// the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
	            		// so we skip an entire weekend and subtract 2 days
	            		$no_remaining_days -= 2;
	        	}
	    	}

	    	//The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
		//---->february in none leap years gave a remainder of 0 but still calculated weekends between first and last day, this is one way to fix it
	   	$workingDays = $no_full_weeks * 5;
	    	if ($no_remaining_days > 0 )
	    	{
	      		$workingDays += $no_remaining_days;
	    	}
	
	    	//We subtract the holidays
	    	foreach($holidays as $holiday)
		{
	        	$time_stamp=strtotime($holiday);
	        	//If the holiday doesn't fall in weekend
	        	if ($startDate <= $time_stamp && $time_stamp <= $endDate && date("N",$time_stamp) != 6 && date("N",$time_stamp) != 7)
	            		$workingDays--;
	    	}

	    	return $workingDays;
	}



/*
 * Updates a users sick and vacation hours in Banner
 * 
 * $userUin: A user's UIN
 * $vacHours: Vacation Hours used
 * $sickHours: Sick hours used. 
 * $date: Date for this transaction (MM/DD/YYYY)
 * 
 */
function apiUpdateUserHours($userUin, $vacHours, $sickHours, $date, $validateOnly="N") {

    global $bannerUrl;
    global $senderAppId;
    $apiURL = $bannerUrl;

    if($userUin == 0) {
        return "";
    }

       $fields = "senderAppId=".$senderAppId."&institutionalId=$userUin&vacTaken=".$vacHours."&sicTaken=".$sickHours."&dateAvailable=".$date."&sbTaken=-1&separationFlag=N&conversionFlag=N&validateOnlyFlag=".$validateOnly."";

       $fieldsArray = array('senderAppId'=>$senderAppId,
                       'institutionalId'=>$userUin,
                       'vacTaken'=>$vacHours,
                       'sickTaken'=>$sickHours,
                       'dateAvailable'=>$date,
                       'sbTaken'=>-1,
                       'separationFlag'=>'N',
                       'conversionFlag'=>'N',
                       'validateOnlyFlag'=>'N' 
               );
        
       $fullURL = $bannerUrl ."?". $fields;

       $result = $this->get_curl_result($fullURL, $fields, $fieldsArray);
       
       $xml = new SimpleXMLElement($result);
       

       foreach($xml->children() as $attr) {

           $updateResult = $attr->attributes()->value;

           if($updateResult == "success") {
                echo("<div class='alert alert-success'>User $userUin updated successfully.</div>");
           } else {
               $error = $attr->Error[0];
               echo("<div class='alert alert-warning'>Error updating $userUin: $error.</div>");
           }
       }
       return $result;    
}
    
    /* Get user info from Banner
     * 
     * @param $userUin The user's university id number
     * 
     * @return Array of Vacation Leave data from Banner
     */
    function apiGetUserInfo($userUin) {
        global $bannerUrl;
        global $senderAppId;
        $apiURL = $bannerUrl . "?senderAppId=$senderAppId&institutionalId=$userUin";

           $curl = curl_init();
           curl_setopt($curl, CURLOPT_URL, $apiURL);

           curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
           curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

           $result = curl_exec($curl);
           curl_close($curl);

           return $result;       
    }
	
    
    function get_curl_result($url, $fields, $fields_array=null) {
        
       $curl = curl_init();

       curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
       
       curl_setopt($curl, CURLOPT_URL, $url);

       if($fields_array != null) {
        curl_setopt($curl,CURLOPT_POST, count($fields_array));
       }
       curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
       
       $result = curl_exec($curl);
       
       curl_close($curl);
       
       return $result;
    }
    
}
?>
