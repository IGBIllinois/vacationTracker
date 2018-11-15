<?php
/**
 * Class Leave.php
 * This class provides easy functions to manipluate a leave's values
 * 
 * @author nevoband
 *
 */
class Leave
{
	private $sqlDataBase;
	private $leaveId;
	private $date;
	private $time;
	private $leaveTypeId;
	private	$userId;
	private $description;
	private $statusId;
	private $submitDate;
	private $leaveTypeSpecialId;
	private $hours;
	private $yearId;

	public function __construct(SQLDataBase $sqlDataBase)
	{
		$this->sqlDataBase = $sqlDataBase;
		$this->hours=0;
		$this->leaveId=0;
	}

	public function __destruct()
	{

	}

	/**
	 * Load a leave from a database
	 * and populate this object
	 * 
	 * @param unknown_type $leaveId
	 */
	public function LoadLeave($leaveId)
	{
		$this->leaveId = $leaveId;
		$queryLeaveInfo = "SELECT date,"
                        . "time,"
                        . "leave_hours, "
                        . "leave_type_id, "
                        . "leave_type_id_special,"
                        . "user_id,description, "
                        . "submit_date, "
                        . "status_id, "
                        . "year_info_id "
                        . "FROM leave_info "
                        . "WHERE leave_id = :leaveId";
                $params = array("leaveId"=>$leaveId);
		$leaveInfo = $this->sqlDataBase->get_query_result($queryLeaveInfo, $params);
		$this->date = $leaveInfo[0]['date'];
		$this->time = $leaveInfo[0]['time'];
		$this->leaveTypeId = $leaveInfo[0]['leave_type_id'];
		$this->userId = $leaveInfo[0]['user_id'];
		$this->description = $leaveInfo[0]['description'];
		$this->statusId = $leaveInfo[0]['status_id'];
		$this->submitDate = $leaveInfo[0]['status_id'];
		$this->leaveTypeSpecialId = $leaveInfo[0]['leave_type_id_special'];
		$this->hours = $leaveInfo[0]['leave_hours'];
		$this->yearId = $leaveInfo[0]['year_info_id'];

	}

	/**
	 * Create a leave and populate this object with its values
	 * 
	 * @param unknown_type $date
	 * @param unknown_type $time
	 * @param unknown_type $leaveTypeId
	 * @param unknown_type $description
	 * @param unknown_type $userId
	 * @param unknown_type $leaveTypeSpecialId
	 * @param unknown_type $yearId
	 * @param unknown_type $hours
	 */
	public function CreateLeave($date,$time,$leaveTypeId,$description,$userId, $leaveTypeSpecialId, $yearId,$hours)
	{
		$this->date = $date;
		$this->time = $time;
		$this->leaveTypeId = $leaveTypeId;
		$this->userId = $userId;
		$this->description = $description;
		$this->leaveTypeSpecialId = $leaveTypeSpecialId;
		$this->statusId = NEW_LEAVE;
		$this->yearId = $yearId;
		$this->hours = $hours;
		$this->WriteLeave();

		return $this->leaveId;
	}

	/**
	 * Write leave values to the database
	 * 
	 */
	private function WriteLeave()
	{
		$queryInsertLeave = "INSERT INTO leave_info ("
                        . "date,"
                        . "time,"
                        . "leave_type_id,"
                        . "leave_type_id_special, "
                        . "user_id,"
                        . "description, "
                        . "submit_date, "
                        . "status_id, "
                        . "leave_hours,"
                        . "year_info_id)"
                        .  "VALUES ("
                        .":date, "
                        .":time, "
                        .":leave_type_id, "
                        .":leave_type_id_special, "
                        .":user_id, "
                        .":description, "
                        ." NOW(), "
                        .":status_id, "
                        .":leave_hours, "
                        .":year_info_id )";
                
                $params = array("date"=>$this->date,
                        "time"=>$this->time,
                        "leave_type_id"=>$this->leaveTypeId,
                        "leave_type_id_special"=>$this->leaveTypeSpecialId,
                        "user_id"=>$this->userId,
                        "description"=>$this->description,
                        "status_id"=>$this->statusId,
                        "leave_hours"=>$this->hours,
                        "year_info_id"=>$this->yearId);

                //$leaveId = $this->sqlDataBase->insertQuery($queryInsertLeave);
                $leaveId = $this->sqlDataBase->get_insert_result($queryInsertLeave, $params);
                $this->leaveId = $leaveId;

	}

	/**
	 * Create a copy of this leave and return a leave object.
	 * 
	 */
	public function CopyLeave()
	{
		$leaveCopy = new Leave($this->sqlDataBase);
		$leaveCopy->CreateLeave($this->date,$this->time,$this->leaveTypeId,$this->description,$this->userId,$this->leaveTypeSpecialId);
		return $leaveCopy;
	}

	/**
	 * Update the database with leave values incase any of them changed
	 * 
	 */
	public function UpdateDb()
	{
		//$this->description = mysqli_real_escape_string($this->sqlDataBase->getLink(), $this->description);
		$queryUpdateLeave = "UPDATE leave_info SET "
                        . "date = :date, "
                        . "time = :time, "
                        . "leave_type_id = :leave_type_id, "
                        . "user_id = :user_id, "
                        . "description = :description, "
                        . "status_id= :status_id, "
                        . "leave_type_id_special= :leave_type_id_special, "
                        . "leave_hours = :leave_hours, "
                        . "year_info_id= :year_info_id "
                        . "WHERE leave_id = :leave_id";

                $params = array("date"=>$this->date,
                                "time"=>$this->time,
                                "leave_type_id"=>$this->leaveTypeId,
                                "user_id"=>$this->userId,
                                "description"=>$this->description,
                                "status_id"=>$this->statusId, 
                                "leave_type_id_special"=>$this->leaveTypeSpecialId,
                                "leave_hours"=>$this->hours,
                                "year_info_id"=>$this->yearId,
                                "leave_id"=>$this->leaveId);

                $this->sqlDataBase->get_update_result($queryUpdateLeave, $params);
	}

	/**
	 * Delete leave from database
	 * 
	 */
	public function Delete()
	{
		$queryDeleteLeave = "DELETE FROM leave_info WHERE leave_id = :leaveId";
                $deleteParams = array("leave_id"=>$this->leaveId);
		$this->sqlDataBase->nonSelectQuery($queryDeleteLeave);
		$queryDeleteLeaveConfirmation = "DELETE FROM authen_key WHERE leave_id=".$this->leaveId;
		$this->sqlDataBase->nonSelectQuery($queryDeleteLeaveConfirmation);
	}

	/**
	 * Check that a leave has a confirmation token
	 * 
	 * @param unknown_type $confirmToken
	 */
	public function CheckConfirmToken($confirmToken)
	{
		$queryConfirmedToken = "SELECT COUNT(*) as leave_exists FROM authen_key WHERE leave_id=".$this->leaveId." AND confirm_key=\"".$confirmToken."\"";
		$confirmedToken = $this->sqlDataBase->query($queryConfirmedToken);
		if($confirmedToken[0]['leave_exists'])
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	//Getters and setters -------------------------------------------------------------------------------
	
	public function getLeaveId() { return $this->leaveId; }
	public function getDate() { return $this->date; }
	public function getTime() { return $this->time; }
	public function getLeaveTypeId() { return $this->leaveTypeId; }
	public function getUserId() { return $this->userId; }
	public function getDescription() { return $this->description; }
	public function getStatusId() { return $this->statusId; }
	public function getLeaveTypeIdSpecial() { return $this->leaveTypeSpecialId; }
	public function getHours() { return $this->hours; }
	public function getYearId() { return $this->yearId; }
	public function getStatusString()
	{
		$queryStatusName = "SELECT name FROM status WHERE status_id=".$this->statusId;
		$name =  $this->sqlDataBase->singleQuery($queryStatusName);
		return $name;

	}

	public function setDate($x) { $this->date = $x; }
	public function setTime($x) { $this->time = $x; }
	public function setLeaveTypeId($x) { $this->leaveTypeId = $x; }
	public function setUserId($x) { $this->userId = $x; }
	public function setDescription($x) { $this->description = $x; }
	public function setStatusId($x) { $this->statusId = $x; }
	public function setSubmitDate($x) { $this->submitDate = $x; }
	public function setLeaveTypeIdSpecial($x) { $this->leaveTypeSpecialId = $x; }
	public function setHours($x) { $this->hours = $x; }
	public function setYearId($x) { $this->yearId = $x; }
}
?>
