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
		$queryUserInfo = "SELECT netid,"
                        . "first_name,"
                        . "last_name,"
                        . "user_type_id,"
                        . "user_perm_id,"
                        . "email,"
                        . "supervisor_id, "
                        . "percent, "
                        . "calendar_format, "
                        . "auto_approve, "
                        . "start_date,"
                        . "enabled, "
                        . "banner_include, "
                        . "auth_key, "
                        . "uin "
                        . "FROM users "
                        . "WHERE user_id=:user_id";
                
                $params = array("user_id"=>$userId);
		$userInfo = $this->sqlDataBase->get_query_result($queryUserInfo, $params);

		if(!isset($userInfo) || count($userInfo) == 0)
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
         * @param bool $enabled true if this user is enabled, else false
         * @param string $uin University ID number
         * @param bool $banner_include True if this user is to be included 
         *              in Banner data. Defaults to true.
	 */
	public function CreateUser(
                $firstName, 
                $lastName, 
                $userPermId, 
                $userEmail, 
                $userTypeId, 
                $netid, 
                $supervisorId,
                $startDate, 
                $autoApprove,
                $percent,
                $enabled, 
                $uin, 
                $banner_include=1)
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
		$queryInsertUser = "INSERT INTO users ("
                        . "netid,"
                        . "first_name,"
                        . "last_name,"
                        . "user_type_id,"
                        . "user_perm_id,"
                        . "email,"
                        . "supervisor_id, "
                        . "percent, "
                        . "calendar_format, "
                        . "auto_approve, "
                        . "start_date,"
                        . "enabled, "
                        . "banner_include, "
                        . "group_id,"
                        . "block_days, "
                        . "auth_key, "
                        . "uin)"
                            . "VALUES("
                            . ":netid,"
                            . ":firstName,"
                            . ":lastName,"
                            . ":userTypeId,"
                            . ":userPermId,"
                            . ":userEmail,"
                            . ":supervisorId,"
                            . ":percent,"
                            . ":calendarFormat,"
                            . ":autoApprove,"
                            . ":startDate,"
                            . ":enabled,"
                            . ":banner_include,"
                            . "0,"
                            . "0,"
                            . "0,"
                            . ":uin)";
                
                $params = array("netid"=>$this->netid,
                                "firstName"=>$this->firstName,
                                "lastName"=>$this->lastName,
                                "userTypeId"=>$this->userTypeId,
                                "userPermId"=>$this->userPermId,
                                "userEmail"=>$this->userEmail,
                                "supervisorId"=>$this->supervisorId,
                                "percent"=>$this->percent,
                                "calendarFormat"=>$this->calendarFormat,
                                "autoApprove"=>$this->autoApprove,
                                "startDate"=>$this->startDate,
                                "enabled"=>$this->enabled,
                                "banner_include"=>$this->banner_include,
                                "uin"=>$this->uin
                    );
		
                $this->userId = $this->sqlDataBase->get_insert_result($queryInsertUser, $params);

		$queryLeaveTypeIds = "SELECT leave_type_id, hidden,year_type_id FROM leave_type";
		$leaveTypeIds = $this->sqlDataBase->get_query_result($queryLeaveTypeIds);

		$years = new Years($this->sqlDataBase);

		foreach($leaveTypeIds as $id=>$leaveTypeId)
		{
			foreach($years->getYearsIds($leaveTypeId['year_type_id']) as $id=>$yearInfo)
			{
				$queryInsertUserLeaveType = "INSERT INTO leave_user_info "
                                        . "(user_id,"
                                        . "leave_type_id,"
                                        . "used_hours,"
                                        . "hidden, "
                                        . "year_info_id, "
                                        . "initial_hours, "
                                        . "added_hours)"
                                        . "VALUES(".
                                        ":user_id,".
                                        ":leave_type_id, ".
                                        "0, ".
                                        ":hidden, ".
                                        ":year_info_id, ".
                                        " 0.0, " .
                                        " 0.0)";
				//echo("queryInsertUserLeaveType = $queryInsertUserLeaveType<BR>");
                                $params = array("user_id"=>$this->userId,
                                                "leave_type_id"=>$leaveTypeId['leave_type_id'],
                                                "hidden"=>$leaveTypeId['hidden'],
                                                "year_info_id"=>$yearInfo['year_info_id']);
                                $this->sqlDataBase->get_insert_result($queryInsertUserLeaveType, $params);
			}
		}

	}

	/**
	 * Apply changes made to this user object to the database
	 * 
	 */
	public function UpdateDb()
	{
		$queryUpdateUserDb = "UPDATE users SET "
                        . "first_name = :first_name, "
                        . "last_name = :last_name, "
                        . "user_perm_id = :user_perm_id, "
                        . "email = :email, "
                        . "user_type_id = :user_type_id, "
                        . "netid = :netid, "
                        . "supervisor_id = :supervisor_id, "
                        . "percent= :percent, "
                        . "calendar_format= :calendar_format, "
                        . "auto_approve = :auto_approve, "
                        . "start_date =:start_date,"
                        . "enabled= :enabled,"
                        . "banner_include= :banner_include, "
                        . "uin=:uin "
                        . "WHERE user_id= :user_id";
                
                $params = array("first_name"=>$this->firstName,
                                "last_name"=>$this->lastName,
                                "user_perm_id"=>$this->userPermId,
                                "email"=>$this->userEmail,
                                "user_type_id"=>$this->userTypeId,
                                "netid"=>$this->netid,
                                "supervisor_id"=>$this->supervisorId,
                                "percent"=>$this->percent,
                                "calendar_format"=>$this->calendarFormat,
                                "auto_approve"=>$this->autoApprove,
                                "start_date"=>$this->startDate,
                                "enabled"=>$this->enabled,
                                "banner_include"=>$this->banner_include,
                                "uin"=>$this->uin,
                                "user_id"=>$this->userId);
                
		$this->sqlDataBase->get_update_result($queryUpdateUserDb, $params);

	}

        /** Updated database authentication key for when a user logs in
         * 
         */
    public function UpdateAuthKey()
    {
        $queryUpdateAuthKey = "UPDATE users SET auth_key=MD5(RAND()) WHERE user_id=:user_id";
        $params = array("user_id"=>$this->userId);
        $this->sqlDataBase->get_update_result($queryUpdateAuthKey, $params);

        $this->authKey = $this->GetAuthKeyByUserId($this->userId);
    }

    /** Gets the authentication key from the database for a user
     * 
     * @param int $userId The ID of the user
     * 
     * 
    * 
    */
    public function GetAuthKeyByUserId($userId)
    {

        $queryAuthKey = "SELECT auth_key FROM users WHERE user_id=:userId";
        $params = array("userId"=>$userId);
        $authKey = $this->sqlDataBase->singleQuery($queryAuthKey, $params);
        return $authKey;
    }
	/**
	 * Get this user's supervisor User object
	 * @return User $supervisor
	 */
	public function GetSupervisor()
	{
		$supervisor = new User($this->sqlDataBase);
                if($this->supervisorId != 0) {

                    $supervisor->LoadUser($this->supervisorId);
                    return $supervisor;
                } else {

                    return null;
                }
	}

	/**
	 * Check if a userid is an employee of this user.
	 * 
	 * @param unknown_type $userId
	 */
	public function isEmployee($userId)
	{
		$employeesList = $this->GetEmployees();
		if(count($employeesList)>0)
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
		$queryEmployees = "SELECT user_id, first_name, last_name, email, netid FROM users WHERE supervisor_id = :supervisor_id ORDER BY first_name";
                $params = array("supervisor_id"=>$this->userId);
                $employees = $this->sqlDataBase->get_query_result($queryEmployees, $params);
		return $employees;
	}

	/**
	 * Return a list of all users in the system
	 * 
	 */
	public function GetAllUsers()
	{
		$queryAllUsers = "SELECT user_id, first_name, last_name, email, netid, enabled FROM users ORDER BY first_name";
		$allUsers = $this->sqlDataBase->get_query_result($queryAllUsers);
		return $allUsers;
	}

        /**
	 * Return a list of all enabled users in the system
	 * 
	 */
	public function GetAllEnabledUsers()
	{
		$queryAllEnabledUsers = "SELECT user_id, first_name, last_name, email, netid, enabled FROM users WHERE enabled=".ENABLED." ORDER BY last_name";
                $allEnabledUsers = $this->sqlDataBase->get_query_result($queryAllEnabledUsers);
                return $allEnabledUsers;
	}

        /**
	 * Return a list of all users who have been deactivated in the system
	 * 
	 */
	public function GetAllDisabledUsers()
	{
            $queryAllDisabledUsers = "SELECT user_id, first_name, last_name, email, netid, enabled FROM users WHERE enabled!=".ENABLED." ORDER BY last_name";
            $allDisabledUsers = $this->sqlDataBase->get_query_result($queryAllDisabledUsers);
            return $allDisabledUsers;
	}
        
        /**
	 * Return a list of all enabled users in the system whose data is to be modified in Banner
	 * 
	 */
        public function GetAllBannerUsers() {
            $queryAllBannerUsers = "SELECT user_id, first_name, last_name, email, netid enabled FROM users WHERE enabled=".ENABLED." and banner_include=1 ORDER BY last_name";
            $allBannerUsers = $this->sqlDataBase->get_query_result($queryAllBannerUsers);
            return $allBannerUsers;
        }
        
        /** 
         * 
         * Return a list of all enabled users in the system who are viewable by this user
         */
        public function GetAllViewableUsers() {

            $querySharedCalendars = "SELECT u.user_id, u.first_name, u.last_name
                FROM users u LEFT JOIN shared_calendars sc 
                ON u.user_id = sc.owner_id WHERE (sc.viewer_id = :viewer_id OR (u.supervisor_id=:viewer_id)) 
                and u.enabled=:enabled GROUP BY user_id ORDER BY u.last_name";

            $params = array("viewer_id"=>$this->getUserId(),
                            "enabled"=>ENABLED);

            $viewableUsers = $this->sqlDataBase->get_query_result($querySharedCalendars, $params);
            
            return $viewableUsers;
        }
        
        public function GetSharedUsers() {
            $querySharedUsers = "SELECT u.user_id, u.first_name, u.last_name "
                        . "FROM users u, shared_calendars sc "
                        . "WHERE enabled=:enabled and u.user_id = sc.viewer_id "
                        . "AND sc.owner_id=:owner_id ".
                        " ORDER BY u.last_name ASC";
                $params = array("enabled"=>ENABLED,
                    "owner_id"=>$this->getUserId());
                $sharedUsers = $this->sqlDataBase->get_query_result($querySharedUsers, $params);
                
                return $sharedUsers;
        }
        
        public function GetUnsharedUsers() {
            $queryAllUnsharedUsers = "SELECT user_id, first_name, last_name FROM users "
                        . "WHERE enabled=:enabled AND user_id NOT IN "
                        . "(SELECT viewer_id FROM shared_calendars "
                        . "WHERE owner_id=:owner_id) "
                        . "ORDER BY last_name ASC";

                $params = array("enabled"=>ENABLED,
                                "owner_id"=>$this->getUserId());

                $allUnsharedUsers = $this->sqlDataBase->get_query_result($queryAllUnsharedUsers, $params);
                
                return $allUnsharedUsers;
        }
        
         
        /** Gets the vacation leaves for a user for a specified appointment year and pay period
         * 
         * @param string $type Type of Leave to get ("Vacation", "Sick", "Floating Holiday")
         * @param int $year_id ID for the Year to get data from
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
                   WHERE li.user_id =:user_id AND li.status_id=:status_id AND li.year_info_id=:year_id 
                   AND lt.name = 'Vacation'
                   and date between :start_date and :end_date
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
                        WHERE li.user_id =:user_id AND li.status_id=:status_id AND li.year_info_id=:year_id
                        AND lt.name = 'Sick'
                        and date between :start_date and :end_date
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
                        WHERE li.user_id =:user_id AND li.status_id=:status_id AND li.year_info_id=:year_id ".
                        " AND li.date between :start_date and :end_date ". 
                        " ORDER BY li.date DESC";
            } else {
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
                        WHERE li.user_id =:user_id AND li.status_id=:status_id AND li.year_info_id=:year_id
                        AND lt.name = :type
                        and date between :start_date and :end_date
                        ORDER BY li.date DESC";
                $newtype=true;
            }

            $params = array("user_id"=>$user_id,
                            "status_id"=>$status_id,
                            "year_id"=>$year_id,
                            "start_date"=>$start_date,
                            "end_date"=>$end_date);
            if($newtype) {
                $params['type'] = $type;
            }
            
            $results_array = $this->sqlDataBase->get_query_result($query, $params);

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
	
        public function setBannerInclude($x) { $this->banner_include = $x; }
        public function setUIN($x) { $this->uin = $x; }
        
        public function setEnabled($x) { 
            $this->enabled = $x; 
            
            if($x == false) {
                // also delete shared calendars
                $query = "delete from shared_calendars where owner_id=:owner_id or viewer_id=:viewer_id";
                $params = array("owner_id"=>$this->userId, "viewer_id"=>$this->userId);
                $this->sqlDataBase->get_query_result($query, $params);
                
                // disable supervisor status
                $this->supervisorId = 0;
                
                $employees = $this->GetEmployees();
                foreach($employees as $employee) {
                    $employee_user = new User($this->sqlDataBase, $employee['user_id']);
                    $employee_user->setSupervisorId(0);
                    $employee_user->UpdateDb();
                }
                
            }
            
            
        }
        
        public function UpdateLocalBannerData() {
            $uin = $this->uin;
            echo("uin = $uin");
            $helperClass = new Helper($this->sqlDataBase);
                $userXML= $helperClass->apiGetUserInfo($uin);
//print_r($userXML);
                 $xml = "";
                 try {
                 $xml = new SimpleXMLElement($userXML);
//print_r($xml);
                 } catch(Exception $e) {
                     //echo("Error:");
                     //echo($e->getTraceAsString());
                     $errorMessages .= "<tr class=\"failed_row\"><td>Error: User ".$this->getNetid(). " has no UIN assigned.</td></tr>";
                     echo("Error, user not found in Banner<BR>");
                     return;
                 }
                 $current_vacation_hours = 0;
                 $current_sick_hours = 0;
                 $current_floating_hours = 0;
                 $current_nonc_sick_hours = 0;
                 $total_vacation_hours = 0;
                 $total_sick_hours = 0;
                 $total_floating_hours = 0;
                 $total_nonc_sick_hours = 0;
                 $taken_vacation_hours = 0;
                 $taken_sick_hours = 0;
                 $taken_floating_hours = 0;
                 $taken_nonc_sick_hours = 0;
                 //print_r($xml);
                 if(!empty($xml)) {
                 foreach($xml->children() as $leave) {

                     $code = $leave->Leave[0]->ValidLeaveTitle[0]->Code;
                     if($code == "VACA") {
                         $current_vacation_hours = $leave->Leave[0]->BeginBalance;
                         $accrued_vacation_hours = $leave->Leave[0]->Accrued;

                         $taken_vacation_hours = $leave->Leave[0]->Taken;
                         //$total_vacation_hours = $current_vacation_hours + $accrued_vacation_hours;
                         $total_vacation_hours = $leave->Leave[0]->AvailableBalance;

                     } elseif($code == "SICK") {
                         $current_sick_hours = $leave->Leave[0]->BeginBalance;
                         $accrued_sick_hours = $leave->Leave[0]->Accrued;
                         $taken_sick_hours = $leave->Leave[0]->Taken;
                         //$total_sick_hours = $current_sick_hours + $accrued_sick_hours;
                         $total_sick_hours = $leave->Leave[0]->AvailableBalance;

                     }
                     elseif($code == "SICN") {
                         $current_nonc_sick_hours = $leave->Leave[0]->BeginBalance;
                         $accrued_nonc_sick_hours = $leave->Leave[0]->Accrued;
                         $taken_nonc_sick_hours = $leave->Leave[0]->Taken;
                         //$total_sick_hours = $current_sick_hours + $accrued_sick_hours;
                         $total_nonc_sick_hours = $leave->Leave[0]->AvailableBalance;
                     }
                     elseif($code == "FLHL") {
                         $current_floating_hours = $leave->Leave[0]->BeginBalance;
                         $accrued_floating_hours = $leave->Leave[0]->Accrued;
                         $taken_floating_hours = $leave->Leave[0]->Taken;
                         $total_floating_hours = $current_floating_hours + $accrued_floating_hours;
                     }
                 }
                 
                 $check_query = "SELECT id from banner_data where user_id=:user_id";
                 $check_params = array("user_id"=>$this->userId);
                 
                 $result = $this->sqlDataBase->get_query_result($check_query, $check_params);
                 $insert = true;
                 if(count($result) > 0) {
                     $insert = false;
                 }
                 
                 /* Only needed:
                 * $total_vacation_hours
                   $total_sick_hours
                   $taken_vacation_hours
                   $taken_sick_hours
                   $taken_nonc_sick_hours
                  * 
                  */
                $params = array(
                        //"current_vac_hours"=>$current_vacation_hours,
                        //"accrued_vac_hours"=>$accrued_vacation_hours,
                        "taken_vac_hours"=>$taken_vacation_hours,
                        "total_vac_hours"=>$total_vacation_hours,

                        //"current_sick_hours"=>$current_sick_hours,
                        //"accrued_sick_hours"=>$accrued_sick_hours,
                        "taken_sick_hours"=>$taken_sick_hours,
                        "total_sick_hours"=>$total_sick_hours,

                        //"current_sicn_hours"=>$current_nonc_sick_hours,
                        //"accrued_sicn_hours"=>$accrued_nonc_sick_hours,
                        "taken_sicn_hours"=>$taken_nonc_sick_hours,
                        //"total_sicn_hours"=>$total_nonc_sick_hours,

                        //"current_float_hours"=>$current_floating_hours,
                        //"accrued_float_hours"=>$accrued_floating_hours,
                        //"taken_float_hours"=>$taken_floating_hours,
                        //"total_float_hours"=>$total_floating_hours,

                        "user_id"=>$this->userId

                );
                                     
                 if($insert == true) {
                     
                     $query = "INSERT INTO banner_data (".
                             "user_id,"
                             //"current_vacation_hours,"
                             //. "accrued_vacation_hours,"
                             . "taken_vacation_hours,"
                             . "total_vacation_hours,"

                             //. "current_sick_hours,"
                             //. "accrued_sick_hours,"
                             . "taken_sick_hours,"
                             . "total_sick_hours,"

                             //. "current_sicn_hours,"
                             //. "accrued_sicn_hours,"
                             . "taken_sicn_hours,"
                             //. "total_sicn_hours,"

                             //. "current_float_hours,"
                             //. "accrued_float_hours,"
                             //. "taken_float_hours,"
                             //. "total_float_hours,"
                             . "last_update)".
                             " VALUES (".
                             " :user_id, "
                             //":current_vac_hours,"
                             //. ":accrued_vac_hours,"
                             . ":taken_vac_hours,"
                             . ":total_vac_hours,"
                     
                             //.  ":current_sick_hours,"
                             //. ":accrued_sick_hours,"
                             . ":taken_sick_hours,"
                             . ":total_sick_hours,"
                             
                             //.  ":current_sicn_hours,"
                             //. ":accrued_sicn_hours,"
                             . ":taken_sicn_hours,"
                             //. ":total_sicn_hours,".
                             
                             //":current_float_hours,"
                             //. ":accrued_float_hours,"
                             //. ":taken_float_hours,"
                             //. ":total_float_hours,"
                             . "NOW())";
                     //echo("query = $query<BR>params = <BR>");
                     //print_r($params);
                     $result = $this->sqlDataBase->get_insert_result($query, $params);
                     //print_r($result);
                 } else {
                    $query = "UPDATE banner_data set ".
                            //"current_vacation_hours = :current_vac_hours,".
                            //"accrued_vacation_hours = :accrued_vac_hours,".
                            "taken_vacation_hours = :taken_vac_hours, ".
                            "total_vacation_hours = :total_vac_hours, ".

                            //"current_sick_hours = :current_sick_hours,".
                            //"accrued_sick_hours = :accrued_sick_hours,".
                            "taken_sick_hours = :taken_sick_hours, ".
                            "total_sick_hours = :total_sick_hours, ".

                            //"current_sicn_hours = :current_sicn_hours,".
                            //"accrued_sicn_hours = :accrued_sicn_hours,".
                            "taken_sicn_hours = :taken_sicn_hours, ".
                            //"total_sicn_hours = :total_sicn_hours, ".

                            //"current_float_hours = :current_float_hours,".
                            //"accrued_float_hours = :accrued_float_hours,".
                            //"taken_float_hours = :taken_float_hours, ".
                            //"total_float_hours = :total_float_hours, ".

                            "last_update = NOW() where user_id = :user_id";
echo("query = $query<BR>params =<BR>");
                     print_r($params);
                    $result = $this->sqlDataBase->get_update_result($query, $params);

                 }

                 
                        
                 }
            
        }
        
        public function GetLocalBannerData() {
            $query = "SELECT * from banner_data where user_id = :user_id";
            $params = array("user_id"=>$this->userId);
            
            $result = $this->sqlDataBase->get_query_result($query, $params);
            if(count($result) > 0) {
                return $result[0];
            } else {
                return null;
            }
                    
        }
        
        // Static functions
        
        public static function GetUserTypes($sqlDataBase) {
            $queryEmployeeTypes = "SELECT user_type_id, name FROM user_type";
            $employeeTypes = $sqlDataBase->get_query_result($queryEmployeeTypes);
            return $employeeTypes;
        }
        
        public static function GetUsers($sqlDataBase, $user_id, $employeeType=null, $enabled=null) {
            $usersToAdd = array();
            switch ($user_id)
            {
		case 0:
                    if($employeeType == null) {
                        $queryUsersToAdd = "SELECT user_id,percent,start_date FROM users";
                        $params = null;
                    } else {
			//Add leave to all users including a template
			$queryUsersToAdd = "SELECT user_id,percent,start_date FROM users WHERE user_type_id=:user_type_id ";
                        $params = array("user_type_id"=>$employeeType);
                    }
                        if($enabled != null) {
                            $queryUsersToAdd .= " AND enabled=:enabled ";
                            $params["enabled"]=$enabled;
                        }
                        $usersToAdd = $sqlDataBase->get_query_result($queryUsersToAdd, $params);
			array_push($usersToAdd,array("user_id"=>0,"percent"=>100,"start_date"=>"0000-00-00"));
			break;
		case -1:
			//Add template
			$usersToAdd = array(0=> array("user_id"=>0,"percent"=>100,"start_date"=>"0000-00-00"));
			break;
		case -2:
			//Add to all current users without adding to template
			//$queryUsersToAdd = "SELECT user_id,percent,start_date FROM users WHERE user_type_id=".$_POST['employeeType']." AND enabled=".ENABLED;
                        $queryUsersToAdd = "SELECT user_id,percent,start_date FROM users WHERE user_type_id=".$employeeType;
                        $params = array("user_type_id"=>$employeeType);
                        if($enabled != null) {
                            $queryUsersToAdd .= " AND enabled=:enabled ";
                            $params["enabled"]=$enabled;
                        }
                        $usersToAdd = $sqlDataBase->get_query_result($queryUsersToAdd, $params);
			break;
		default:
                        $queryUsersToAdd = "SELECT user_id,percent,start_date FROM users WHERE user_id=:user_id";
                        $params = array("user_id"=>$user_id);
			$usersToAdd = $sqlDataBase->get_query_result($queryUsersToAdd, $params);			
            }
        
            return $usersToAdd;
        }
        

}
?>
