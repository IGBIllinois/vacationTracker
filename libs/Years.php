<?php
/**
 * Class Years.php
 * This class loads information about a year from the database
 * can also create new years.
 * 
 * @author nevoband
 *
 */
class Years
{
	private $sqlDataBase;
	private $appointmentYearTypeId = 1;
	private $fiscalYearTypeId = 2;
        
        private $year_info_id;
        private $start_date;
        private $end_date;
        private $locked;
        private $year_type_id;
        private $prev_year_id;
        private $next_year_id;

	public function __construct(SQLDataBase $sqlDataBase, $id=null)
	{
		$this->sqlDataBase = $sqlDataBase;
                if($id != null) {
                    $this->LoadData($id);
                }
	}

	public function __destruct()
	{

	}
        
        public function getId() {
            return $this->year_info_id;
        }
        
        public function getStartDate() {
            return $this->start_date;
        }
        
        public function getEndDate() {
            return $this->end_date;
        }
        
        public function getLocked() {
            return $this->locked;
        }
        
        public function getYearType() {
            return $this->year_type_id;
        }
        
        public function getNextYearId() {
            return $this->next_year_id;
        }
        
        public function getPrevYearId() {
            return $this->prev_year_id;
        }

        /**
	 * Returns a year's ID in the database based on the date given
	 * 
	 * @param unknown_type $day
	 * @param unknown_type $month
	 * @param unknown_type $year
	 * @param unknown_type $yearTypeId
	 */
	public function GetYearId($day,$month,$year, $yearTypeId)
	{

		$queryDateYearId = "SELECT year_info_id FROM year_info "
                        . "WHERE start_date <= (SELECT CONCAT(:year, '-', :month, '-', :day)) "
                        . "AND end_date >= (SELECT CONCAT(:year, '-', :month, '-', :day)) "
                        . "AND year_type_id=:yearTypeId";

                $params = array("year"=>$year,
                                "month"=>$month,
                                "day"=>$day,
                                "yearTypeId"=>$yearTypeId);

                $yearId = $this->sqlDataBase->singleQuery($queryDateYearId, $params);

		if($yearId)
		{
			return $yearId;
		}
		else
		{
			return 0;
		}
	}

        /** Gets data for all pay periods for this Year
         * 
         */
        public function getPayPeriods() {
            $queryPayPeriod = "SELECT pay_period_id,start_date,end_date FROM pay_period WHERE year_info_id=:year_info_id";
            $params = array("year_info_id"=>$this->year_info_id);
            $payPeriod = $this->sqlDataBase->get_query_result($queryPayPeriod, $params);
            return $payPeriod;
        }
	/**
	 * Gets a pay period ID based on a yearID and a Date given
	 * 
	 * @param unknown_type $day
	 * @param unknown_type $month
	 * @param unknown_type $year
	 * @param unknown_type $yearId
	 */
	public function GetPayPeriodId($day,$month,$year,$yearId)
	{
		$queryPayPeriodId = "SELECT pay_period_id FROM pay_period "
                        . "WHERE start_date <= (SELECT CONCAT(:year, '-', :month, '-', :day)) "
                        . "AND end_date >= (SELECT CONCAT(:year, '-', :month, '-', :day)) "
                        . "AND year_info_id=:yearId";
                $params = array("year"=>$year,
                                "month"=>$month,
                                "day"=>$day,
                                "yearId"=>$yearId
                                );

                $payPeriodId = $this->sqlDataBase->singleQuery($queryPayPeriodId, $params);

		if($payPeriodId)
		{
			return $payPeriodId;
		}
		else
		{
			return 0;
		}
	}
        
        /** 
         * Gets the current pay period for the current date
         * 
         * @param int $yearType ID of year type 
         * @return int
         */
        public function GetCurrentPayPeriodId($yearTypeId)
	{
            $day = date("d");
            $month = date("m");
            $year = date("Y");
            $yearId = $this->GetYearId($day,$month,$year, $yearTypeId);

		$queryPayPeriodId = "SELECT pay_period_id FROM pay_period "
                        . "WHERE start_date <= (SELECT CONCAT(:year, '-', :month, '-', :day)) "
                        . "AND end_date >= (SELECT CONCAT(:year, '-', :month, '-', :day)) "
                        . "AND year_info_id=:yearId";
                $params = array("year"=>$year,
                                "month"=>$month,
                                "day"=>$day,
                                "yearId"=>$yearId
                                );

                $payPeriodId = $this->sqlDataBase->singleQuery($queryPayPeriodId, $params);

		if($payPeriodId)
		{
			return $payPeriodId;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Gets the next year starting with a given yearid
	 * 
	 * @param unknown_type $yearId
	 */
	public function NextYearId($yearId)
	{
		$queryNextYearId = "SELECT year_info_id FROM year_info WHERE prev_year_id = :yearId";
                $params = array("yearId"=>$yearId);
		$nextYearId = $this->sqlDataBase->singleQuery($queryNextYearId, $params);
		if($nextYearId)
		{
			return $nextYearId;
		}
		else
		{
			return 0;
		}
			
	}

	/**
	 * Gets the previous year id based on a given yearid
	 * 
	 * @param unknown_type $yearId
	 */
	public function PrevYearId($yearId)
	{
		$queryPrevYearId = "SELECT year_info_id FROM year_info WHERE next_year_id = :yearId";
                $params = array("yearId"=>$yearId);
		$prevYearId = $this->sqlDataBase->singleQuery($queryPrevYearId, $params);
		if($prevYearId)
		{
			return $prevYearId;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Gets the latest year created return its ID
	 * 
	 * @param unknown_type $yearTypeId
	 */
	public function GetLastYearId($yearTypeId)
	{
		$queryLastYearId = "SELECT year_info_id FROM year_info WHERE next_year_id=0 AND year_type_id=:yearTypeId";
                $params = array("yearTypeId"=>$yearTypeId);
		$lastYearId = $this->sqlDataBase->singleQuery($queryLastYearId, $params);

		if($lastYearId)
		{
			return $lastYearId;
		}
		else
		{
			return 0;
		}

	}

	/**
	 * Gets the earliest year created. Return's its ID 
	 *
	 * @param unknown_type $yearTypeId
	 */
	public function GetFirstYearId($yearTypeId)
	{
		$queryFirstYearId = "SELECT year_info_id FROM year_info WHERE prev_year_id=0 AND year_type_id=:yearTypeId";
                $params = array("yearTypeId"=>$yearTypeId);
		$firstYearId = $this->sqlDataBase->singleQuery($queryFirstYearId, $params);

		if($firstYearId)
		{
			return $firstYearId;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Return the date for a given year ID
	 * 
	 * @param unknown_type $yearId
	 */
	public function GetYearDates($yearId)
	{
		$queryYearDates = "SELECT start_date,end_date FROM year_info WHERE year_info_id=:yearId";
                $params = array("yearId"=>$yearId);
		$yearDates = $this->sqlDataBase->get_query_result($queryYearDates, $params);
		if(isset($yearDates))
		{
			return $yearDates;
		}
		else
		{
			return 0;
		}

	}

	/**
	 * Get all years of a certain year type.
	 * @param $yearTypeId
	 */
	public function getYearsIds($yearTypeId)
	{
		$queryYearsIds = "SELECT year_info_id FROM year_info WHERE year_type_id=:yearTypeId";
                $params = array("yearTypeId"=>$yearTypeId);
		$yearsIds = $this->sqlDataBase->get_query_result($queryYearsIds, $params);
		if(isset($yearsIds))
		{
			return $yearsIds;
		}
		else
		{
			return 0;
		}
		
	}
	
	/**
	 * Check if a year is locked from reserving leaves
	 * 
	 * @param unknown_type $yearId
	 */
	public function isLocked($yearId)
	{
		$queryIfLocked = "SELECT locked FROM year_info WHERE year_info_id = :yearId";
                $params = array("yearId"=>$yearId);
		$locked = $this->sqlDataBase->singleQuery($queryIfLocked, $params);
		return $locked;
	}

	/**
	 * Check if a date is locked
	 * 
	 * @param unknown_type $day
	 * @param unknown_type $month
	 * @param unknown_type $year
	 */
	public function isAllLocked($day,$month,$year)
	{
		$date = "(SELECT CONCAT(:year, '-', :month, '-', :day))";
		$queryLockedYears = "SELECT COUNT(*) as locked_year FROM year_info WHERE start_date <= $date AND end_date >= $date AND locked = 1 LIMIT 1";
                $params = array("day"=>$day, "month"=>$month, "year"=>$year);
		$locked = $this->sqlDataBase->singleQuery($queryLockedYears, $params);

		return $locked;
	}

	/**
	 * Get the year type id for a given year id
	 * 
	 * @param unknown_type $yearId
	 */
	public function GetYearTypeId($yearId)
	{
		$queryYearTypeId = "SELECT year_type_id FROM year_info WHERE year_info_id=:yearId";
                $params = array("yearId"=>$yearId);
		$yearTypeId = $this->sqlDataBase->singleQuery($queryYearTypeId, $params);
		if($yearTypeId)
		{
			return $yearTypeId;
		}
		else
		{
			return 0;
		}

	}

	/**
	 * Load year type info from database and return an associative array containing this information.
	 * 
	 * @param unknown_type $yearTypeId
	 */
	public function GetYearTypeInfo($yearTypeId)
	{
		$queryYearTypeInfo = "SELECT name,description,start_date,end_date,num_periods FROM year_type WHERE year_type_id=:yearTypeId";
                $params = array("yearTypeId"=>$yearTypeId);

		$yearTypeInfo = $this->sqlDataBase->get_query_result($queryYearTypeInfo, $params);

		if(isset($yearTypeInfo))
		{
			return $yearTypeInfo;
		}
		else
		{
			return 0;
		}

	}

	/**
	 * Check if a particular date exists in the system
	 * 
	 * @param unknown_type $day
	 * @param unknown_type $month
	 * @param unknown_type $year
	 */
	public function Exists($day,$month,$year)
	{
		$date = "(SELECT CONCAT(:year, '-', :month, '-', :day))";
		$queryYearExists = "SELECT COUNT(*) as year_exists FROM year_info WHERE start_date <= $date AND end_date >= $date LIMIT 1";
                $params = array("day"=>$day, "month"=>$month, "year"=>$year);
		$exists = $this->sqlDataBase->singleQuery($queryYearExists, $params);

		return $exists;
	}

	/**
	 * Check if a year already exists between two dates for a particular year type.
	 * 
	 * @param unknown_type $dateStart
	 * @param unknown_type $dateEnd
	 * @param unknown_type $yearTypeId
	 */
	public function CheckYearConflict($dateStart,$dateEnd,$yearTypeId)
	{
		$queryConflicts = "SELECT *
				FROM year_info 
				WHERE ( (start_date <= :dateStart AND end_date >= :dateStart) OR
				(start_date <= :dateEnd AND end_date >= :dateEnd) OR
				(start_date >= :dateStart AND end_date <= :dateEnd) ) AND 
				year_type_id=:yearTypeId";

                $params = array("dateStart"=>$dateStart, "dateEnd"=>$dateEnd, "yearTypeId"=>$yearTypeId);
		if($this->sqlDataBase->get_query_result($queryConflicts, $params))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Create a year type
	 * example:
	 * appointment year
	 * fiscal year
	 * etc...
	 * 
	 * @param unknown_type $dateStart
	 * @param unknown_type $dateEnd
	 * @param unknown_type $name
	 * @param unknown_type $description
	 * @param unknown_type $numPeriods
	 */
	public function CreateYearType($dateStart,$dateEnd,$name,$description,$numPeriods)
	{
		$queryCreateYearType = "INSERT INTO year_type "
                        . "(name,description,start_date,end_date,num_periods)"
                        . "VALUES(:name, :description, :dateStart, :dateEnd, :numPeriods)";
                $params = array("name"=>$name, 
                    "description"=>$description, 
                    "dateStart"=>$dateStart, 
                    "dateEnd"=>$dateEnd, 
                    "numPeriods"=>$numPeriods);
		$yearTypeId = $this->sqlDataBase->get_insert_result($queryCreateYearType, $params);

		if($this->CreateYear(0,0,$yearTypeId,0))
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
        
        

	/**
	 * create a new year for a particular year type.
	 * 
	 * @param unknown_type $nextYear
	 * @param unknown_type $prevYear
	 * @param unknown_type $yearTypeId
	 * @param unknown_type $locked
	 */
	public function CreateYear($nextYear,$prevYear,$yearTypeId, $locked)
	{
            $createYearStartTime = time();
		$yearId = 0;
		$prevYearDates = $this->GetYearDates($prevYear);
		$nextYearDates = $this->GetYearDates($nextYear);
		$yearTypeInfo = $this->GetYearTypeInfo($yearTypeId);

		if($yearTypeInfo)
		{
			if($prevYearDates)
			{
				list($startYear,$startMonth,$startDay) = explode("-",$prevYearDates[0]['start_date']);
				list($endYear,$endMonth,$endDay) = explode("-",$prevYearDates[0]['end_date']);
				$dateStart = ($startYear+1)."-".$startMonth."-".$startDay;
				$dateEnd = ($endYear+1)."-".$endMonth."-".$endDay;
				$nextYear=0;

			}
			elseif(($nextYearDates))
			{

				list($startYear,$startMonth,$startDay) = explode("-",$nextYearDates[0]['start_date']);
				list($endYear,$endMonth,$endDay) = explode("-",$nextYearDates[0]['end_date']);
				$dateStart = ($startYear-1)."-".$startMonth."-".$startDay;
				$dateEnd = ($endYear-1)."-".$endMonth."-".$endDay;
				$prevYear=0;
                        }
			else
			{
				$dateStart = $yearTypeInfo[0]['start_date'];
				$dateEnd = $yearTypeInfo[0]['end_date'];
				$nextYear=0;
				$prevYear=0;
			}
				
			$yearId = $this->YearInfoToDb($nextYear,$prevYear,$dateStart,$dateEnd,$locked,$yearTypeId);

			$yearTypeInfo = $this->GetYearTypeInfo($yearTypeId);
			$this->CreatePayPeriod($dateStart,$dateEnd,$yearId,$yearTypeInfo['0']['num_periods']);

			return $yearId;
				
		}
		else
		{
			return 0;
		}

	}
        
        /** Gets the maximum rollover hours for this year for a specific leave type
         *  Checks the max_rollover table, and if no entry exists, uses the default for
         * the leave type
         * 
         * @param int $leaveTypeId ID of the year type
         * 
         * @return hours The maximum rollover hours for this year and type
         */
        public function GetMaxRollover($leaveTypeId) {
            
            try {
            $query = "SELECT max_rollover_hours from max_rollover where year_id=:year_id and leave_type_id=:leave_type_id";
            $params = array("year_id"=>$this->year_info_id,
                            "leave_type_id"=>$leaveTypeId);
            

            $result = $this->sqlDataBase->get_query_result($query, $params);

            $hours = 0;

            if(count($result) > 0) {
                $hours = $result[0]['max_rollover_hours'];
            } else {
                $leave_type = new LeaveType($this->sqlDataBase);
                $leave_type->LoadLeaveType($leaveTypeId);
                $hours = $leave_type->getMax();
            }
            
            return $hours;
            } catch(Exception $e) {

                return 0;
            }
        }
        
        
        
        // Static functions
        
        /**Returns an array of year type data in the database
         * 
         * @param SqlDataBase $sqlDataBase The database object
         * 
         * @return array An array of Year Type data
         */
        public static function GetYearTypes($sqlDataBase) {
            $queryYearTypesInfo = "SELECT * FROM year_type";
            $yearTypesInfo = $sqlDataBase->get_query_result($queryYearTypesInfo);
            return $yearTypesInfo;
        }
        
        /** Gets the years for a year type
         * 
         * @param SqlDataBase $sqlDataBase The database object
         * @param int $selectedYearTypeId The year type id to get years for
         * 
         * @return array[Year] An array of Year objects of the specified type
         * 
         */
        public static function GetYears($sqlDataBase, $selectedYearTypeId) {
            $queryYearsInfo = "SELECT year_info_id FROM year_info "
             . "WHERE year_type_id=:year_type_id ORDER BY start_date";
                                
            $params = array("year_type_id"=>$selectedYearTypeId);
                                
            $yearsInfo = $sqlDataBase->get_query_result($queryYearsInfo, $params);
            
            $years = array();
            foreach($yearsInfo as $id=>$info) {
                $year = new Years($sqlDataBase, $info['year_info_id']);
                $years[] = $year;
            }
            return $years;
        }
        
        // Private functions

	/**
	 * Insert year information into the database
	 *
	 * @param unknown_type $nextYear
	 * @param unknown_type $prevYear
	 * @param unknown_type $dateStart
	 * @param unknown_type $dateEnd
	 * @param unknown_type $locked
	 * @param unknown_type $yearTypeId
	 */
	private function YearInfoToDb($nextYear,$prevYear,$dateStart,$dateEnd,$locked,$yearTypeId)
	{
            
            // TODO: Query takes too long
            
		$usedHours = 0;
		$addedHours = 0;

		$queryCreateYear = "INSERT INTO year_info "
                        . "(start_date,"
                        . "end_date,"
                        . "locked,"
                        . "year_type_id,"
                        . "next_year_id,"
                        . "prev_year_id)"
                        . "VALUES(:dateStart,"
                                . ":dateEnd,"
                                . ":locked,"
                                . ":yearTypeId,"
                                . ":nextYear,"
                                . ":prevYear)";
                
                $params = array("dateStart"=>$dateStart,
                                "dateEnd"=>$dateEnd,
                                "locked"=>$locked,
                                "yearTypeId"=>$yearTypeId,
                                "nextYear"=>$nextYear,
                                "prevYear"=>$prevYear);
                $yearId = $this->sqlDataBase->get_insert_result($queryCreateYear, $params);
                
		if($prevYear)
		{
			$updatePrevYear = "UPDATE year_info SET next_year_id=:yearId WHERE year_info_id=:prevYear";
                        $prevParams = array("yearId"=>$yearId, "prevYear"=>$prevYear);
                        

                        $this->sqlDataBase->get_update_result($updatePrevYear, $prevParams);
                }
		if($nextYear)
		{
			$updateNextYear = "UPDATE year_info SET prev_year_id=:yearId WHERE year_info_id=:nextYear";
                        $nextParams = array("yearId"=>$yearId, "nextYear"=>$nextYear);

                        $this->sqlDataBase->get_update_result($updateNextYear, $nextParams);
		}

		$queryUserIds = "SELECT user_id FROM users";
		$userIds = $this->sqlDataBase->get_query_result($queryUserIds);

		$queryLeaveTypes = "SELECT leave_type_id,roll_over,default_value,hidden FROM leave_type WHERE year_type_id=:yearTypeId";
                $typeParams = array("yearTypeId"=>$yearTypeId);

                $leaveTypes = $this->sqlDataBase->get_query_result($queryLeaveTypes, $typeParams);

		if(isset($leaveTypes))
		{
                    $helper = new Helper($this->sqlDataBase);
			foreach($userIds as $id_u=>$userId)
			{
                            $starttime = time();
				$user = new User($this->sqlDataBase);
				$user->LoadUser($userId['user_id']);

				$queryInsertUserLeaveInfo = "INSERT INTO leave_user_info "
                                        . "(user_id,"
                                        . "leave_type_id,"
                                        . "used_hours,"
                                        . "hidden,"
                                        . "initial_hours,"
                                        . "added_hours,"
                                        . "year_info_id) VALUES ";
                                $params = array();
				foreach($leaveTypes as $id_l=>$leaveType)
				{
                                    // user data is same for each entry in this query, leave type data changes
                                    $leaveType_id = ":leaveType_".$leaveType['leave_type_id'];
                                    $leaveType_hidden_id = ":leaveType_hidden_".$leaveType['leave_type_id'];
                                    $initial_hours_id = ":initial_hours_".$leaveType['leave_type_id'];
                                    $queryInsertUserLeaveInfo .= "(:user_id, "
                                            . "$leaveType_id, "
                                            . ":usedHours, "
                                            . "$leaveType_hidden_id, "
                                            . "$initial_hours_id, "
                                            . ":added_hours, "
                                            . ":year_info_id),";
    
                                    $params["user_id"] = $userId['user_id'];
                                        $params[$leaveType_id] = $leaveType['leave_type_id'];
                                        $params["usedHours"] = $usedHours;
                                        $params[$leaveType_hidden_id] = $leaveType['hidden'];
                                        $params[$initial_hours_id] = (($user->getPercent()/100)*$leaveType['default_value']);
                                        $params["added_hours"] = $addedHours;
                                        $params["year_info_id"] = $yearId;
				}
				//remove last comma from sql string
				$queryInsertUserLeaveInfo = substr($queryInsertUserLeaveInfo,0,-1);

				$leaveUserInfoId = $this->sqlDataBase->get_insert_result($queryInsertUserLeaveInfo, $params);
                                
                                $helper->RunRulesYearType($userId['user_id'], $yearTypeId, false);

			}
		}
                      
		return $yearId;
	}

	/**
	 * Create a period for a particular year id
	 * A period exists incase there are some rules that refer to particular section of time within a year such as pay period for example.
	 *
	 * @param unknown_type $dateStart
	 * @param unknown_type $dateEnd
	 * @param unknown_type $yearId
	 * @param unknown_type $numPeriods
	 */
	private function CreatePayPeriod($dateStart,$dateEnd,$yearId,$numPeriods)
	{
		$monthsPerYear = 12;
		$monthsPerPeriod = $monthsPerYear / $numPeriods;
		$queryCreatePayPeriod = "INSERT INTO pay_period (start_date,end_date,year_info_id) VALUES ";
                $queryCreatePayPeriod .= "( :start_date, :end_date, :year_info_id)";

		$startDay = Date("d",strtotime($dateStart));
		$startMonth = Date("m",strtotime($dateStart));
		$startYear = Date("Y",strtotime($dateStart));

		$endDay = Date("d",strtotime($dateEnd));
		$endMonth = Date("m",strtotime($dateEnd));
		$endYear = Date("Y",strtotime($dateEnd));
		$monthItr = 0;
		if($startDay==1)
		{
			while($monthItr < $monthsPerYear)
			{
                            $queryCreatePayPeriod = "INSERT INTO pay_period (start_date,end_date,year_info_id) VALUES ";
                            $queryCreatePayPeriod .= "( :start_date, :end_date, :year_info_id)";
                            $params = array("start_date"=>Date("Y-m-d",mktime(0,0,0,$startMonth+$monthItr,$startDay,$startYear)),
                                            "end_date"=>Date("Y-m-d",mktime(0,0,0,$startMonth+$monthItr+$monthsPerPeriod,0,$startYear)),
                                            "year_info_id"=>$yearId);
                            $this->sqlDataBase->get_insert_result($queryCreatePayPeriod, $params);
                            $monthItr = $monthsPerPeriod;
			}
		}
		else
		{
			while($monthItr < $monthsPerYear)
			{
                            $queryCreatePayPeriod = "INSERT INTO pay_period (start_date,end_date,year_info_id) VALUES ";
                            $queryCreatePayPeriod .= "( :start_date, :end_date, :year_info_id)";
                            $params = array("start_date"=>Date("Y-m-d",mktime(0,0,0,$startMonth+$monthItr,$startDay,$startYear)),
                                            "end_date"=>Date("Y-m-d",mktime(0,0,0,$startMonth+$monthItr+$monthsPerPeriod,$endDay,$startYear)),
                                            "year_info_id"=>$yearId);
                            $this->sqlDataBase->get_insert_result($queryCreatePayPeriod, $params);
                            $monthItr+= $monthsPerPeriod;
			}
		}

	}
        

        private function LoadData($id) {
            $query = "SELECT * from year_info where year_info_id = :id";
            $params = array("id"=>$id);
            $result = $this->sqlDataBase->get_query_result($query, $params);
            if(count($result)>0) {
                $result_data = $result[0];
                $this->year_info_id = $id;
                $this->start_date = $result_data['start_date'];
                $this->end_date = $result_data['end_date'];
                $this->locked = $result_data['locked'];
                $this->year_type_id = $result_data['year_type_id'];
                $this->prev_year_id = $result_data['prev_year_id'];
                $this->next_year_id = $result_data['next_year_id']; 
           }
        }
        
}

?>
