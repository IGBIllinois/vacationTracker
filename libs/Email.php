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

        /** Send an email request for leave approval
         * 
         * @param Leave[] $leavesArray An array of Leave objects to get approval for
         * @param int $userid User of the Leaves
         * @param in5 $confirmCode Authenticaion code
         * @return boolean True if the email was sent properly, else false
         */
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

                        if($userSupervisor == null) {
                            // no supervisor to request from
                            return false;
                        }
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

        /** Send an email regarding the approval or denial of a Leave request
         * 
         * @param Leave[] $leavesArray An array of Leave objects
         * @param int $userid ID of the user who has requested the Leaves
         * @param User $loggedUser The User object of the currently logged in user
         * @return boolean True if the email was sent properly, else false
         */
	public function ReplyLeaveApprovalStatus($leavesArray,$userid,$loggedUser)
	{
		$leavesInfoString = "";

		$userInfo = new User($this->sqlDataBase);
		$userInfo->LoadUser($userid);
		
                // the currently logged in user should be the supervisor 
                // ( or an Admin who can approve  the leaves)
                $userSupervisor = $loggedUser;

                
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

        /** Gets the URL the user is currently viewing. Used in ReplyLeaveApprovalStatus()
         *  to send a link to the supervisor. 
         * 
         * @return type
         */
	private function CurrentPageURL()
	{
		$pageURL = 'https://';
		$pageURL .= $_SERVER['SERVER_PORT'] != '80' ? $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"] : $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

		return $pageURL;
	}

        /** Send an email
         * 
         * @param string $recipient Email addres of the recipient
         * @param string $mail_body Text of the message body
         * @param string $subject Text of the email Subject
         * @param string $header Extra email parameters like "From: ", "Reply-To:", etc.
         * @return boolean
         */
	private function CreateMail($recipient, $mail_body, $subject,$header)
	{
		if(mail($recipient, $subject, $mail_body,$header))
		{
			return true;
		}

		return false;
	}
        
        /** Sends an email to the given user and the users' supervisor regarding vacation taken for a given year
         *  and pay period
         * 
         * @global SQLDataBase $sqlDataBase The SQLDataBase object
         * @global User $loggedUser The User currently logged in and sending the email
         * @param int $user_id The id for the User to get vacation data for
         * @param int $status_id The status of the Vacation data (2 = APPROVED). See config.php for full list 
         * @param int $appointment_year_id ID of the Appointment Year
         * @param int $fiscal_year_id ID of the Fiscal Year (for Floating Holidays)
         * @param int $pay_period 1 for first Pay Period of the year (8-15 to 5-15) or 2 for the second Pay Period (5-15 to 8-15)
         * @return array An array of the form:
         * [ "RESULT"=>[TRUE|FALSE],
             "MESSAGE"=>[MESSAGE])];
         *  where RESULT is TRUE if the email was sent successfully, else false, 
         * and MESSAGE is an output message.
         */
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
            
            $user = new User($sqlDataBase);
            $user->LoadUser($user_id);
            $user_email = $user->getUserEmail();
            
            $supervisor = new User($sqlDataBase);
            if($user->getSupervisorId() != 0) {
                $supervisor->LoadUser($user->getSupervisorId());
                $supervisor_email = $supervisor->GetUserEmail();
            } else {
                // if user has no supervisor, user currently logged user
                // (If they're sending a report, they should be an Admin)
                $supervisor_email = $loggedUser->getUserEmail();
            }

            
            
            
            if(DEBUG) {
                
                $user_email = $loggedUser->getUserEmail();
                $supervisor_email = $loggedUser->getUserEmail();
            }
                                        
            $to = $user_email . "," .$supervisor_email;
            
            $due_date = date("l, F d, Y", mktime(0,0,0, 5, 15, date("Y") ));
            if($pay_period == 2) {
                $due_date = date("l, F d, Y", mktime(0,0,0, 8, 15, date("Y") ));
            }
            
            // Due date +5 days
            $due_date = strtotime($due_date);
            $due_date = strtotime("+5 day", $due_date);
            $due_date = date("l, F d, Y", $due_date);
                    
            $message = "Please find attached your Vacation & Sick leave usage for the period of $start_date to $end_date.\n

            If you & your supervisor can forward me your confirmation no later than, ".$due_date.", that would be great.
            If you have any questions, just let me know.\n

            Thanks for your assistance in this process,\n"
                    
            . $loggedUser->getFirstName() . " " . $loggedUser->getLastName();

            $emailText = $message . "\n\n";
            $emailText .= "---------------\n";
            $titles = "Date\tType\tSpecial\tCharge Time\tActual Time\tDescription\tStatus\n\n";
            $emailText .= "Vacation Time\n\n";
            $emailText .= $titles;
            $total_vac = 0;
                $vacation_results = $user->GetVacationLeaves($appointment_year_id, $pay_period, $status_id);
                foreach($vacation_results as $leave) {
                    $leaveType = new LeaveType($this->sqlDataBase);
                    $leaveType->LoadLeaveType($leave->getLeaveTypeId());
                    $emailText .=
                            $leave->GetDate(). "\t" .
                            $leaveType->getName(). "\t" .
                            $leaveType->getSpecial(). "\t" .
                            $leave->GetHours(). "\t" .
                            gmdate('g\h i\m',$leave->GetTime()). "\t" .
                            $leave->getDescription(). "\t" .
                            $leave->GetStatusString(). "\n\n" ;
                    
                    $total_vac += $leave->GetHours();
                }
            
            // Sick Leave
                $emailText .= "Sick Leave\n\n";

                $emailText .=  "Date\tType\tSpecial\tCharge Time\tActual Time\tDescription\tStatus\n\n";
                $total_sick = 0;
                
                $sick_results = $user->GetSickLeaves($appointment_year_id, $pay_period, $status_id);
                foreach($sick_results as $leave) {
                    $leaveType = new LeaveType($this->sqlDataBase);
                    $leaveType->LoadLeaveType($leave->getLeaveTypeId());
                    $emailText .=
                            $leave->GetDate(). "\t" .
                            $leaveType->getName(). "\t" .
                            $leaveType->getSpecial(). "\t" .
                            $leave->GetHours(). "\t" .
                            gmdate('g\h i\m',$leave->GetTime()). "\t" .
                            $leave->getDescription(). "\t" .
                            $leave->GetStatusString(). "\n\n" ;
                    
                    $total_sick += $leave->GetHours();
                }
                
                $emailText .= "Floating Holidays\n\n";

                $emailText .= $titles;
                $total_float = 0;

                $floating_results = $user->GetFloatingHolidays($fiscal_year_id, $pay_period, $status_id);
                foreach($floating_results as $leave) {
                    $leaveType = new LeaveType($this->sqlDataBase);
                    $leaveType->LoadLeaveType($leave->getLeaveTypeId());
                    $emailText .=
                            $leave->GetDate(). "\t" .
                            $leaveType->getName(). "\t" .
                            $leaveType->getSpecial(). "\t" .
                            $leave->GetHours(). "\t" .
                            gmdate('g\h i\m',$leave->GetTime()). "\t" .
                            $leave->getDescription(). "\t" .
                            $leave->GetStatusString(). "\n\n" ;
                    
                    $total_float += $leave->GetHours();
                }
                
                $emailText .= "\n\n".
                    "Total Vacation Hours: ".$total_vac."\n".
                    "Total Sick Hours: ".$total_sick."\n".
                    "Total Floating Holiday Hours: ".$total_float."\n\n";
                
                if($pay_period == 2) {
                    //write yearly totals
                    $userLeavesHoursAvailable = new Rules($sqlDataBase);
                    $leavesAvailable = $userLeavesHoursAvailable->LoadUserYearUsageCalc($user_id,$appointment_year_id);

                    $totalVacHours = round(($leavesAvailable[1]['initial_hours']+$leavesAvailable[1]['added_hours']-$leavesAvailable[1]['calc_used_hours']),2);
                    $estimatedVacHours = round(($leavesAvailable[1]['initial_hours']+$leavesAvailable[1]['est_added_hours']-$leavesAvailable[1]['calc_used_hours']),2);

                    $totalSickHours = round(($leavesAvailable[2]['initial_hours']+$leavesAvailable[2]['added_hours']-$leavesAvailable[2]['calc_used_hours']),2);
                    $estimatedSickHours = round(($leavesAvailable[2]['initial_hours']+$leavesAvailable[2]['est_added_hours']-$leavesAvailable[2]['calc_used_hours']),2);

                    $data[] = (array());
                    $total_vac_hours = $leavesAvailable[1]['calc_used_hours'];
                    $total_sick_hours = $leavesAvailable[2]['calc_used_hours'];

                    $emailText .= "Yearly Total Vacation Hours Taken: ". round($leavesAvailable[1]['calc_used_hours'],2)."\n";
                    $emailText .= "Yearly Total Sick Hours Taken: ". round($leavesAvailable[2]['calc_used_hours'],2)."\n";

                    $emailText .= "Vacation Hours Available: ". $estimatedVacHours."\n";
                    $emailText .= "Sick Hours Available: ". $estimatedSickHours."\n";
                } 

                        

                $subject = "Vacation/Sick Leave Usage for ".$user->GetNetid()." ( $start_date - $end_date )";

                if(DEBUG) {
                    $subject .= " TEST";
                }
                $header= "From: ".$from." ".PHP_EOL .
				 "CC: ".$cc ." " . PHP_EOL .
    				 "X-Mailer: PHP/" . phpversion(). PHP_EOL .
                                 "Return-Path: <".$from.">"."\n";
                
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
