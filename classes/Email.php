<?php
/**
 * Used to generate the e-mails for requesting approval and approving leaves.
 * 
 * @author nevoband
 *
 */
class Email
{
	private $sqlDataBase;

	public function __construct(SQLDataBase $sqlDataBase)
	{
		$this->sqlDataBase = $sqlDataBase;
	}

	public function  __destruct()
	{

	}

	public function RequestLeaveApproval($leavesArray, $userid, $confirmCode)
	{
		if(count($leavesArray)>0)
		{
			$leavesInfoString = "";
			$userInfo = new User($this->sqlDataBase);
			$userInfo->LoadUser($userid);

			$leaveTypeInfo = new LeaveType($this->sqlDataBase);
			foreach($leavesArray as $leaveInfo)
			{
				$leaveTypeInfo->LoadLeaveType($leaveInfo->getLeaveTypeId());
				$leavesInfoString .= "\n".$leaveTypeInfo->getName()." On ".Date('n/j/Y',strtotime($leaveInfo->getDate()))." Actual Hours: ".round(($leaveInfo->getTime()/60/60),2).", Hours to Charge: ".$leaveInfo->getHours().", Description: ".$leaveInfo->getDescription()."\n";
			}
				
			$subject = $userInfo->getFirstName()." ".$userInfo->getLastName()." requested leave approval";
			$message = "This is an e-mail sent via Vacation Tracker:\n\n".$userInfo->getFirstName()." ".$userInfo->getLastName()." has requested approval for the following leave dates and times:\n\n".
			$leavesInfoString.
					"\n\nTo view calendar and approve or not approve these leaves please visit Vacation Tracker:\n".$this->CurrentPageURL()."&confirmtoken=".$confirmCode."\n\n".
					"\n\nTo approve these leaves please click the following link:\n".$this->CurrentPageURL()."&confirmtoken=".$confirmCode."&autoapprove=1\n\n".
					"\n\nTo NOT approve these leaves please click the following link:\n".$this->CurrentPageURL()."&confirmtoken=".$confirmCode."&autonotapprove=1\n\n".
					"Thank you";
			$userSupervisor = $userInfo->GetSupervisor();
			$header= "From: ".$userInfo->getUserEmail()." ".PHP_EOL .
				 "Reply-To: ".$userInfo->getUserEmail() ." " . PHP_EOL .
    				 'X-Mailer: PHP/' . phpversion();

			return $this->CreateMail($userSupervisor->getUserEmail(),$message, $subject,$header);
		}
		else
		{
			return false;
		}


	}

	public function ReplyLeaveApprovalStatus($leavesArray,$userid)
	{
		$leavesInfoString = "";

		$userInfo = new User($this->sqlDataBase);
		$userInfo->LoadUser($userid);
		$userSupervisor = $userInfo->GetSupervisor();

		$leaveInfo = new Leave($this->sqlDataBase);
		$leaveTypeInfo = new LeaveType($this->sqlDataBase);
		foreach($leavesArray as $leave)
		{
			$leaveInfo->LoadLeave($leave);
			$leaveTypeInfo->LoadLeaveType($leaveInfo->getLeaveTypeId());
			$leavesInfoString .= "\n".$leaveTypeInfo->getName()." On ".Date('n/j/Y',strtotime($leaveInfo->getDate()))." Actual Hours: ".round(($leaveInfo->getTime()/60/60),2).", Hours Charged: ".$leaveInfo->getHours()." --".$leaveInfo->getStatusString()."\n";
		}

		$subject = $userSupervisor->getFirstName()." ".$userSupervisor->getLastName()." has reviewed your leave requests.";
		$message = "This is an e-mail sent via Vacation Tracker,\n".$userSupervisor->getFirstName()." ".$userSupervisor->getLastName()." has reviewed your request and changed the following leave's status:\n\n ".$leavesInfoString."\n\nThank you ";

		$header= "From: ".$userSupervisor->getUserEmail()." ".PHP_EOL .
                         "Reply-To: ".$userSupervisor->getUserEmail()." " . PHP_EOL .
                         'X-Mailer: PHP/' . phpversion();
		
		return $this->CreateMail($userInfo->getUserEmail(),$message, $subject, $header);


	}

	private function CurrentPageURL()
	{
		$pageURL = 'https://';
		$pageURL .= $_SERVER['SERVER_PORT'] != '80' ? $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"] : $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		return $pageURL;
	}

	private function CreateMail($recipient, $mail_body, $subject,$header)
	{
		if(mail($recipient, $subject, $mail_body,$header))
		{
			return true;
		}

		return false;
	}
        
        public function SendReportEmail($user_id, $status_id, $appointment_year_id, $fiscal_year_id, $pay_period) {
    
    
            global $sqlDataBase;
            global $loggedUser;

            $from = $loggedUser->GetUserEmail();
            $cc = $loggedUser->GetUserEmail();


                   $years = new Years($sqlDataBase);
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
            $user_email = $user->getUserEmail();
            
            $supervisor = new User($sqlDataBase);
            $supervisor->LoadUser($user->getSupervisorId());
            $supervisor_email = $supervisor->GetUserEmail();
            
            $to = $user_email . "," .$supervisor_email;
            
            $due_date = date("l, F d, Y", mktime(0,0,0, 5, 15, date("Y") ));
            if($pay_period == 2) {
                $due_date = date("l, F d, Y", mktime(0,0,0, 8, 15, date("Y") ));
            }
            
            // Due date +5 days
            $due_date = strtotime($due_date);
            $due_date = strtotime("+5 day", $due_date);
            $due_date = date("l, F d, Y", $due_date);
                    
            $message = "Please find attached your Vacation & Sick leave usage for the period of $start_date-$end_date.\n

If you & your supervisor can forward me your confirmation no later than, ".$due_date.", that would be great.
If you have any questions, just let me know.\n

Thanks for your assistance in this process,\n"
                    
            . $loggedUser->getFirstName() . " " . $loggedUser->getLastName();

            $emailText = $message . "\n\n";
            $emailText .= "---------------\n";
            $titles = "Date\tType\tSpecial\tCharge Time\tActual Time\tDescription\tStatus\n\n";
            $emailText .= "Vacation Time\n\n";
            $emailText .= $titles;

                for ($i = 0; $i < count($vacation_results); $i++) {

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

                for ($i = 0; $i < count($floating_results); $i++) {

                    $emailText .= $floating_results[$i]['date']. "\t" .
                                    $floating_results[$i]['name']. "\t" .
                                    $floating_results[$i]['special_name']. "\t" .
                                    $floating_results[$i]['leave_hours']. "\t" .
                                    $floating_results[$i]['time']. "\t" .
                                    $floating_results[$i]['description']. "\t" .
                                    $floating_results[$i]['statusName']. "\n\n";

                }


                $subject = "Vacation/Sick Leave Usage for ".$user->GetNetid()." ( $start_date - $end_date ) TEST";

                $header= "From: ".$from." ".PHP_EOL .
				 "CC: ".$cc ." " . PHP_EOL .
    				 'X-Mailer: PHP/' . phpversion();
                $email_result =  $this->CreateMail($to, $emailText, $subject, $header);
                
                if($email_result == TRUE) {
                    $result = array("RESULT"=>TRUE,
                                    "MESSAGE"=>"Email successfully sent to: $to.");
                } else {
                    $result = array("RESULT"=>FALSE,
                                    "MESSAGE"=>"There was an error sending the email to: $to. Please try again.");
                }
                return $result;
                
        }

}

?>
