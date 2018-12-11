<?php
/**
 * Class User.php
 * This class loads user information from the database into an a User object
 * which allows for easy manipulation of user information
 * @author nevoband
 *
 */
class User
{
	/*
	const ADMIN = 1;
	const USER = 2;
	const VIEWER =3;
*/
	private $sqlDataBase;
	private $supervisorId;
	private $firstName;
	private $lastName;
	private $userPermId;
	private $userId;
	private $userEmail;
	private $userTypeId;
	private $netid;
	private $percent;
	private $calendarFormat;
	private $autoApprove;
	private $startDate;
	private $enabled;
        private $banner_include; 
        private $uin;
        
    private $authKey;

	public function __construct(SQLDataBase $sqlDataBase)
	{
		$this->sqlDataBase = $sqlDataBase;
		$this->percent = 100;
		$this->calendarFormat = 0;
		$this->autoApprove = 0;
		$this->startDate = Date('Y-m-d');
		$this->userId = 0;
                
	}

	public function __destruct()
	{

	}

	/**
	 * Load user information from database and store them in this object
	 * 
	 * @param unknown_type $userId
	 */
	public function LoadUser($userId)
	{
		$this->userId = $userId;
		$queryUserInfo = "SELECT netid,first_name,last_name,user_type_id,user_perm_id,email,supervisor_id, percent, calendar_format, auto_approve, start_date,enabled, banner_include, auth_key, uin FROM users WHERE user_id=".$userId;
		$userInfo = $this->sqlDataBase->query($queryUserInfo);
		if(!isset($userInfo))
		{
			return false;
		}
		$this->supervisorId = $userInfo[0]['supervisor_id'];
		$this->firstName = $userInfo[0]['first_name'];
		$this->lastName = $userInfo[0]['last_name'];
		$this->userPermId = $userInfo[0]['user_perm_id'];
		$this->userEmail = $userInfo[0]['email'];
		$this->userTypeId = $userInfo[0]['user_type_id'];
		$this->netid = $userInfo[0]['netid'];
		$this->percent = $userInfo[0]['percent'];
		$this->calendarFormat = $userInfo[0]['calendar_format'];
		$this->autoApprove = $userInfo[0]['auto_approve'];
		$this->startDate = $userInfo[0]['start_date'];
		$this->enabled = $userInfo[0]['enabled'];
                $this->banner_include = $userInfo[0]['banner_include'];
                $this->authKey = $userInfo[0]['auth_key'];
                $this->uin = $userInfo[0]['uin'];
	}

	/**
	 * Create a user in database and store its information in this object
	 * 
	 * @param unknown_type $firstName
	 * @param unknown_type $lastName
	 * @param unknown_type $userPermId
	 * @param unknown_type $userEmail
	 * @param unknown_type $userTypeId
	 * @param unknown_type $netid
	 * @param unknown_type $supervisorId
	 * @param unknown_type $startDate
	 * @param unknown_type $autoApprove
	 * @param unknown_type $percent
	 */
	public function CreateUser($firstName, $lastName, $userPermId, $userEmail, $userTypeId, $netid, $supervisorId,$startDate, $autoApprove,$percent,$enabled, $uin, $banner_include=1)
	{
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->userPermId = $userPermId;
		$this->userEmail = $userEmail;
		$this->userTypeId = $userTypeId;
		$this->netid = $netid;
		$this->supervisorId = $supervisorId;
		$this->startDate = $startDate;
		$this->autoApprove = $autoApprove;
		$this->percent = $percent;
		$this->enabled = $enabled;
                $this->banner_include = $banner_include;
		$queryInsertUser = "INSERT INTO users (netid,first_name,last_name,user_type_id,user_perm_id,email,supervisor_id, percent, calendar_format, auto_approve, start_date,enabled, banner_include, group_id,block_days, auth_key, uin)VALUES(\"".$this->netid."\",\"".$this->firstName."\",\"".$this->lastName."\",\"".$this->userTypeId."\",\"".$this->userPermId."\",\"".$this->userEmail."\",\"".$this->supervisorId."\",".$this->percent.",".$this->calendarFormat.", ".$this->autoApprove.",\"".$this->startDate."\",".$this->enabled.",".$this->banner_include.",0,0,0, '".$this->uin."')";
		echo("queryInsertUser = $queryInsertUser<BR>");
                $this->userId = $this->sqlDataBase->insertQuery($queryInsertUser);

		$queryLeaveTypeIds = "SELECT leave_type_id, hidden,year_type_id FROM leave_type";
		$leaveTypeIds = $this->sqlDataBase->query($queryLeaveTypeIds);

		$years = new Years($this->sqlDataBase);

		foreach($leaveTypeIds as $id=>$leaveTypeId)
		{
			foreach($years->getYearsIds($leaveTypeId['year_type_id']) as $id=>$yearInfo)
			{
				$queryInsertUserLeaveType = "INSERT INTO leave_user_info (user_id,leave_type_id,used_hours,hidden, year_info_id, initial_hours, added_hours)VALUES(".$this->userId.",".$leaveTypeId['leave_type_id'].",0, ".$leaveTypeId['hidden'].",".$yearInfo['year_info_id'].", 0.0, 0.0)";
				//echo("queryInsertUserLeaveType = $queryInsertUserLeaveType<BR>");
                                $this->sqlDataBase->insertQuery($queryInsertUserLeaveType);
			}
		}

	}

	/**
	 * Apply changes made to this user object to the database
	 * 
	 */
	public function UpdateDb()
	{
		$queryUpdateUserDb = "UPDATE users SET first_name = \"".$this->firstName."\", last_name = \"".$this->lastName."\", user_perm_id = \"".$this->userPermId."\", email = \"".$this->userEmail."\", user_type_id = \"".$this->userTypeId."\", netid = \"".$this->netid."\", supervisor_id = \"".$this->supervisorId."\", percent=".$this->percent.", calendar_format=".$this->calendarFormat.", auto_approve=".$this->autoApprove.", start_date=\"".$this->startDate."\",enabled=".$this->enabled.",banner_include=".$this->banner_include.", uin='".$this->uin."' WHERE user_id=".$this->userId;
		$this->sqlDataBase->nonSelectQuery($queryUpdateUserDb);

	}

    public function UpdateAuthKey()
    {
        $queryUpdateAuthKey = "UPDATE users SET auth_key=MD5(RAND()) WHERE user_id=".$this->userId;
        $this->sqlDataBase->nonSelectQuery($queryUpdateAuthKey);

        $this->authKey = $this->GetAuthKeyByUserId($this->userId);
    }

    public function GetAuthKeyByUserId($userId)
    {
        $userId = mysqli_real_escape_string($this->sqlDataBase->getLink(), $userId);
        $queryAuthKey = "SELECT auth_key FROM users WHERE user_id=".$userId;
        $authKey = $this->sqlDataBase->singleQuery($queryAuthKey);
        return $authKey;
    }
	/**
	 * Get this user's supervisor User object
	 * @return User $supervisor
	 */
	public function GetSupervisor()
	{
		$supervisor = new User($this->sqlDataBase);
		$supervisor->LoadUser($this->supervisorId);
		return $supervisor;
	}

	/**
	 * Check if a userid is an employee of this user.
	 * 
	 * @param unknown_type $userId
	 */
	public function isEmployee($userId)
	{
		$employeesList = $this->GetEmployees();
		if(isset($employeesList))
		{
			foreach($employeesList as $id=>$employee)
			{
				if($employee['user_id']==$userId)
				{
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Return an array containing all employees of this user
	 * 
	 */
	public function GetEmployees()
	{
		$queryEmployees = "SELECT user_id, first_name, last_name, email, netid FROM users WHERE supervisor_id = ".$this->userId." ORDER BY first_name";
		$employees = $this->sqlDataBase->query($queryEmployees);
		return $employees;
	}

	/**
	 * Return a list of all users in the system
	 * 
	 */
	public function GetAllUsers()
	{
		$queryAllUsers = "SELECT user_id, first_name, last_name, email, netid, enabled FROM users ORDER BY first_name";
		$allUsers = $this->sqlDataBase->query($queryAllUsers);
		return $allUsers;
	}

	public function GetAllEnabledUsers()
	{
		$queryAllEnabledUsers = "SELECT user_id, first_name, last_name, email, netid, enabled FROM users WHERE enabled=".ENABLED." ORDER BY last_name";
                $allEnabledUsers = $this->sqlDataBase->query($queryAllEnabledUsers);
                return $allEnabledUsers;
	}

	public function GetAllDisabledUsers()
	{
		$queryAllDisabledUsers = "SELECT user_id, first_name, last_name, email, netid, enabled FROM users WHERE enabled!=".ENABLED." ORDER BY last_name";
        $allDisabledUsers = $this->sqlDataBase->query($queryAllDisabledUsers);
        return $allDisabledUsers;
	}
        
        public function GetAllBannerUsers() {
            $queryAllBannerUsers = "SELECT user_id, first_name, last_name, email, netid enabled FROM users WHERE enabled=".ENABLED." and banner_include=1 ORDER BY last_name";
            $allBannerUsers = $this->sqlDataBase->query($queryAllBannerUsers);
            return $allBannerUsers;
        }
        
         
        /** Gets the vacation leaves for a user for a specified appointment year and pay period
         * 
         * @param string $type Type of Leave to get ("Vacation", "Sick", "Floating Holiday")
         * @param int $appointment_year_id ID for the Appointment Year to get data from
         * @param int $pay_period The Pay Period to get data from
         *          1 = 8/15 - 5/15
         *          2 = 5/15 - 8/15
         * @param $status_id ID of the status of Leaves to get (2 = APPROVED). See config.php for full list
         * 
         * @return array An array of Vacation Leave data for the user and time period specified
         */  
        public function GetLeaves($type, $year_id, $pay_period, $status_id) {
            
            $user_id = $this->userId;
            
            $years = new Years($this->sqlDataBase);
            $yearInfo = $years->GetYearDates($year_id);

            $start_year = Date("Y",strtotime($yearInfo[0]['start_date']));
            $end_year = Date("Y",strtotime($yearInfo[0]['end_date']));

            $start_date = $start_year . "-08-15";
            $end_date = $end_year. "-05-15";

            if($pay_period == 2) {
                $start_date = $end_year . "-05-15";
                $end_date = $end_year . "-08-15";

            }
            
            $query = "";
            if($type == "Vacation") {
             $query = "SELECT li.leave_id, DATE_FORMAT(li.date,'%c-%e-%Y') as date, 
                 li.leave_hours, 
                 TIME_FORMAT(SEC_TO_TIME(li.time), '%kh %im') as time, 
                 li.description, 
                 lt.name, 
                 s.name as statusName, 
                 li.leave_type_id_special, 
                 lts.name as special_name
                   FROM (leave_info li)
                   JOIN leave_type lt ON li.leave_type_id=lt.leave_type_id
                   JOIN status s ON li.status_id = s.status_id
                   LEFT JOIN leave_type lts ON lts.leave_type_id = li.leave_type_id_special
                   WHERE li.user_id =".$user_id." AND li.status_id=".$status_id." AND li.year_info_id=".$year_id."
                   AND lt.name != 'Sick'
                   and date between '$start_date' and '$end_date' 
                   ORDER BY li.date DESC";
            } else if($type == "Sick") {
                $query ="SELECT li.leave_id, DATE_FORMAT(li.date,'%c-%e-%Y') as date, 
                    li.leave_hours, TIME_FORMAT(SEC_TO_TIME(li.time), '%kh %im') as time, 
                    li.description, 
                    lt.name, 
                    s.name as statusName, 
                    li.leave_type_id_special, 
                    lts.name as special_name
                        FROM (leave_info li)
                        JOIN leave_type lt ON li.leave_type_id=lt.leave_type_id
                        JOIN status s ON li.status_id = s.status_id
                        LEFT JOIN leave_type lts ON lts.leave_type_id = li.leave_type_id_special
                        WHERE li.user_id =".$user_id." AND li.status_id=".$status_id." AND li.year_info_id=".$year_id."
                        AND lt.name = 'Sick'
                        and date between '$start_date' and '$end_date' 
                        ORDER BY li.date DESC";
            } else if($type == "Floating Holiday") {
                $query = "SELECT li.leave_id, DATE_FORMAT(li.date,'%c-%e-%Y') as date, 
                    li.leave_hours, TIME_FORMAT(SEC_TO_TIME(li.time), '%kh %im') as time, 
                    li.description, 
                    lt.name, 
                    s.name as statusName, 
                    li.leave_type_id_special, 
                    lts.name as special_name
                        FROM (leave_info li)
                        JOIN leave_type lt ON li.leave_type_id=lt.leave_type_id
                        JOIN status s ON li.status_id = s.status_id
                        LEFT JOIN leave_type lts ON lts.leave_type_id = li.leave_type_id_special
                        WHERE li.user_id =".$user_id." AND li.status_id=".$status_id." AND li.year_info_id=".$year_id.
                        " AND li.date between '$start_date' and '$end_date'". 
                        " ORDER BY li.date DESC";
            }

            $results_array = $this->sqlDataBase->query($query);

            $results = array();
            if(count($results_array) > 0) {
            foreach($results_array as $result) {
                $leave = new Leave($this->sqlDataBase);
                $leave->LoadLeave($result['leave_id']);
                $results[] = $leave;
            }
            }
            return $results;
        }
        /** Gets the vacation leaves for a user for a specified appointment year and pay period
         * 
         * @param int $appointment_year_id ID for the Appointment Year to get data from
         * @param int $pay_period The Pay Period to get data from
         *          1 = 8/15 - 5/15
         *          2 = 5/15 - 8/15
         * @param $status_id ID of the status of Leaves to get (2 = APPROVED). See config.php for full list
         * 
         * @return array An array of Vacation Leave data for the user and time period specified
         */    
        public function GetVacationLeaves($appointment_year_id, $pay_period, $status_id) {
            return $this->GetLeaves("Vacation", $appointment_year_id, $pay_period, $status_id);
        }
        
         /** Gets the sick leaves for a user for a specified appointment year and pay period
         * 
         * @param int $appointment_year_id ID for the Appointment Year to get data from
         * @param int $pay_period The Pay Period to get data from
         *          1 = 8/15 - 5/15
         *          2 = 5/15 - 8/15
         * @param $status_id ID of the status of Leaves to get (2 = APPROVED). See config.php for full list
         * 
         * @return array An array of Vacation Leave data for the user and time period specified
         */    
        public function GetSickLeaves($appointment_year_id, $pay_period, $status_id) {
            return $this->GetLeaves("Sick", $appointment_year_id, $pay_period, $status_id);
        }
        
       /** Gets the floating holidays for a user for a specified appointment year and pay period
         * 
         * @param int $appointment_year_id ID for the Appointment Year to get data from
         * @param int $pay_period The Pay Period to get data from
         *          1 = 8/15 - 5/15
         *          2 = 5/15 - 8/15
         * @param $status_id ID of the status of Leaves to get (2 = APPROVED). See config.php for full list
         * 
         * @return array An array of Vacation Leave data for the user and time period specified
         */    
        public function GetFloatingHolidays($fiscal_year_id, $pay_period, $status_id) {
            return $this->GetLeaves("Floating Holiday", $fiscal_year_id, $pay_period, $status_id);
        }
        
	//Getters and setters -------------------------------------------------------------------------------------------
	
	public function getSupervisorId() { return $this->supervisorId; }
	public function getFirstName() { return $this->firstName; }
	public function getLastName() { return $this->lastName; }
	public function getUserPermId() { return $this->userPermId; }
	public function getUserId() { return $this->userId; }
	public function getUserEmail() { return $this->userEmail; }
	public function getUserTypeId() { return $this->userTypeId; }
	public function getNetid() { return $this->netid; }
	public function getPercent() { return $this->percent; }
	public function getCalendarFormat() { return $this->calendarFormat; }
	public function getAutoApprove() { return $this->autoApprove; }
	public function getStartDate() { return $this->startDate; }
	public function getEnabled() { return $this->enabled; }
        public function getAuthKey() { return $this->authKey; }
        public function getBannerInclude() { return $this->banner_include; }
        public function getUIN() { return $this->uin; }

	public function setSupervisorId($x) { $this->supervisorId = $x; }
	public function setFirstName($x) { $this->firstName = $x; }
	public function setLastName($x) { $this->lastName = $x; }
	public function setUserPermId($x) { $this->userPermId = $x; }
	public function setUserEmail($x) { $this->userEmail = $x; }
	public function setUserTypeId($x) { $this->userTypeId = $x; }
	public function setNetid($x) { $this->netid = $x; }
	public function setPercent($x) { $this->percent = $x; }
	public function setCalendarFormat($x) { $this->calendarFormat = $x; }
	public function setAutoApprove($x) { $this->autoApprove = $x; }
	public function setStartDate($x) { $this->startDate = $x; }
	public function setEnabled($x) { $this->enabled = $x; }
        public function setBannerInclude($x) { $this->banner_include = $x; }
        public function setUIN($x) { $this->uin = $x; }

}
?>
