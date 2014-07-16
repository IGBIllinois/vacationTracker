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
			//echo "<pre>".$message."</pre>";
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

}

?>
