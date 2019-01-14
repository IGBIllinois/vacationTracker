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

	public function __construct(SQLDataBase $sqlDataBase)
	{
		$this->sqlDataBase = $sqlDataBase;
	}

	public function __destruct()
	{

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
		//$queryDateYearId = "SELECT year_info_id FROM year_info WHERE start_date <= \"".$year."-".$month."-".$day."\" AND end_date >= \"".$year."-".$month."-".$day."\" AND year_type_id=".$yearTypeId;
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
                        . "WHERE start_date <= :year-:month-:day "
                        . "AND end_date >= (SELECT CONCAT(:year, '-', :month, '-', :day)) "
                        . "AND year_info_id=:yearId";
                $params = array("year"=>$year,
                                "month"=>$month,
                                "day"=>$day,
                                "yearId"=>$yearId);
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

                        $createYearEndTime = time();
                        $totalTime = ($createYearEndTime - $createYearStartTime)/60;
                        
			return $yearId;
				
		}
		else
		{
                    $createYearEndTime = time();
                        $totalTime = ($createYearEndTime - $createYearStartTime)/60;
			return 0;
		}

	}
        
        // Static functions
        
        public static function GetYearTypes($sqlDataBase) {
            $queryYearTypesInfo = "SELECT * FROM year_type";
            $yearTypesInfo = $sqlDataBase->get_query_result($queryYearTypesInfo);
            return $yearTypesInfo;
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
                //$yearId = $this->sqlDataBase->insertQuery($queryCreateYear);
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
                                    $queryInsertUserLeaveInfo .= "(:user_id, $leaveType_id, :usedHours, $leaveType_hidden_id, $initial_hours_id, :added_hours, :year_info_id),";
    
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

				$leaveUserInfoId = $this->sqlDataBase->insertQuery($queryInsertUserLeaveInfo);
                                
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
		$queryCreatePayPeriod = "INSERT INTO pay_period (start_date,end_date,year_info_id)VALUES";

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
				$queryCreatePayPeriod .="(\"".Date("Y-m-d",mktime(0,0,0,$startMonth+$monthItr,$startDay,$startYear))."\",\"".Date("Y-m-d",mktime(0,0,0,$startMonth+$monthItr+$monthsPerPeriod,0,$startYear))."\",".$yearId."),";
				$monthItr = $monthsPerPeriod;
			}
		}
		else
		{
			while($monthItr < $monthsPerYear)
			{
				$queryCreatePayPeriod .="(\"".Date("Y-m-d",mktime(0,0,0,$startMonth+$monthItr,$startDay,$startYear))."\",\"".Date("Y-m-d",mktime(0,0,0,$startMonth+$monthItr+$monthsPerPeriod,$endDay,$startYear))."\",".$yearId."),";
				$monthItr+= $monthsPerPeriod;
			}
		}
		$queryCreatePayPeriod = substr($queryCreatePayPeriod,0,-1);
		$this->sqlDataBase->insertQuery($queryCreatePayPeriod);
	}
        
}


?>
