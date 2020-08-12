<?php
/**
 * Class Rules.php
 * This class has several subclasses for each year type
 * Provides functions to Load yearly leaves information into easily read 2D arrays
 * 
 * @author nevoband
 *
 */
class Rules
{
	protected $sqlDataBase;
	protected $year;
	protected $force = false;

	public function __construct(SQLDataBase $sqlDataBase)
	{
		$this->sqlDataBase = $sqlDataBase;
		$this->year = new Years($sqlDataBase);
	}

	public function __destruct()
	{
	}

	/**
	 * A function used by subclasses
	 * to run rules for a year type
	 * 
	 * @param unknown_type $userId
	 * @param unknown_type $yearId
	 */
	public function RunRules($userId,$yearId,$force=null)
	{
		//This code should go in extended class
	}

	/**
	 * 
	 * Loads a user's yearly usage for a particular year
	 * Can load only leaves added before the payPeriod given otherwise set to false 
	 * @param unknown_type $userId
	 * @param unknown_type $yearId
	 * @param unknown_type $payPeriod
	 */
	public function LoadUserYearUsage($userId,$yearId,$payPeriod=false)
	{
		$years = new Years($this->sqlDataBase);
		$yearTypeId = $years->GetYearTypeId($yearId);
		$userLeaveTypesUsage = $this->LoadLeaveTypesUsage($yearTypeId);
		foreach($userLeaveTypesUsage as $id=>$userLeaveTypeUsage)
		{
			$leaveUserInfo = $this->LoadLeaveUserInfo($yearId,$userLeaveTypeUsage['leave_type_id'],$userId);
			$userLeaveTypesUsage[$id]['added_hours'] = $this->LoadAddedHours($yearId,$userLeaveTypeUsage['leave_type_id'],$userId,$payPeriod);
			$userLeaveTypesUsage[$id]['used_hours'] = $this->LoadRawUsedHours($yearId,$userLeaveTypeUsage['leave_type_id'],$userId);
			$userLeaveTypesUsage[$id]['initial_hours'] = $leaveUserInfo[0]['initial_hours'];
			$userLeaveTypesUsage[$id]['leave_user_info_id'] = $leaveUserInfo[0]['leave_user_info_id'];
                        $userLeaveTypesUsage[$id]['leave_type_id'] = $userLeaveTypeUsage['leave_type_id'];

		}
		return $userLeaveTypesUsage;
	}

	/**
	 * 
	 * Loads year usage plust estimates, used for user viewing not for rules
	 * placed here to reduce coupling due to use of similar functions
	 * @param unknown_type $userId
	 * @param unknown_type $yearId
	 * @param unknown_type $payPeriod
	 */
	public function LoadUserYearUsageCalc($userId,$yearId,$payPeriod=false)
	{
		$years = new Years($this->sqlDataBase);
		$yearTypeId = $years->GetYearTypeId($yearId);
		$userLeaveTypesUsage = $this->LoadLeaveTypesUsage($yearTypeId);
                if($userLeaveTypesUsage != null) {

                    
		foreach($userLeaveTypesUsage as $id=>$userLeaveTypeUsage)
		{
                    

			$leaveUserInfo =$this->LoadLeaveUserInfo($yearId,$userLeaveTypeUsage['leave_type_id'],$userId);
			$userLeaveTypesUsage[$id]['added_hours'] = $this->LoadAddedHours($yearId,$userLeaveTypeUsage['leave_type_id'],$userId,$payPeriod);
			$userLeaveTypesUsage[$id]['initial_hours'] = $leaveUserInfo[0]['initial_hours'];
			$userLeaveTypesUsage[$id]['est_added_hours'] = $this->LoadAddedHours($yearId,$userLeaveTypeUsage['leave_type_id'],$userId);
			$userLeaveTypesUsage[$id]['calc_used_hours'] = $this->LoadCalcUsedHours($yearId,$userLeaveTypeUsage['leave_type_id'],$userId);
                        $userLeaveTypesUsage[$id]['leave_type_id'] = $userLeaveTypeUsage['leave_type_id'];

		} 
		return $userLeaveTypesUsage;
                }
	}
        
        public function LoadUserYearUsageCalcPayPeriod($userId,$yearId,$startDate, $endDate)
	{     
		$years = new Years($this->sqlDataBase);
		$yearTypeId = $years->GetYearTypeId($yearId);
		$userLeaveTypesUsage = $this->LoadLeaveTypesUsage($yearTypeId);
                if($userLeaveTypesUsage != null) {

                    
		foreach($userLeaveTypesUsage as $id=>$userLeaveTypeUsage)
		{

                    
			$leaveUserInfo =$this->LoadLeaveUserInfo($yearId,$userLeaveTypeUsage['leave_type_id'],$userId);
			//$userLeaveTypesUsage[$id]['added_hours'] = $this->LoadAddedHoursPayPeriod($yearId,$userLeaveTypeUsage['leave_type_id'],$userId,$payPeriod);
                        $userLeaveTypesUsage[$id]['added_hours'] = $this->LoadAddedHoursPayPeriod($yearId,$userLeaveTypeUsage['leave_type_id'],$userId, $startDate, $endDate);
			//$userLeaveTypesUsage[$id]['initial_hours'] = $leaveUserInfo[0]['initial_hours'];
			//$userLeaveTypesUsage[$id]['est_added_hours'] = $this->LoadAddedHoursPayPeriod($yearId,$userLeaveTypeUsage['leave_type_id'],$userId);
			$userLeaveTypesUsage[$id]['calc_used_hours'] = $this->LoadCalcUsedHoursPayPeriod($yearId,$userLeaveTypeUsage['leave_type_id'],$userId, $startDate, $endDate);
                        $userLeaveTypesUsage[$id]['leave_type_id'] = $userLeaveTypeUsage['leave_type_id'];

		} 
		return $userLeaveTypesUsage;
                }
	}
        
        	protected function LoadAddedHoursPayPeriod($yearId,$leaveTypeId,$userId, $startDate, $endDate)
	{
		$queryAddedLeaveSum = "SELECT SUM(hours) FROM added_hours WHERE user_id=:user_id AND year_info_id=:year_info_id AND leave_type_id=:leave_type_id ";
		//if($payPeriod)
		//{
			$queryAddedLeaveSum .=" AND date between :start_date and :end_date";
		//}
		$queryAddedLeaveSum .= " GROUP BY leave_type_id";
                $params = array("user_id"=>$userId,
                                "year_info_id"=>$yearId,
                                "leave_type_id"=>$leaveTypeId,
                                "start_date"=>$startDate,
                                "end_date"=>$endDate);
                //echo("queryAddedLeaveSum = $queryAddedLeaveSum <BR>");
		$addedLeaveSum = $this->sqlDataBase->singleQuery($queryAddedLeaveSum, $params);
		if($addedLeaveSum)
		{
			return $addedLeaveSum;
		}
		else
		{
			return 0;
		}
	}
        
        	protected function LoadCalcUsedHoursPayPeriod($yearId,$leaveTypeId,$userId, $startDate, $endDate)
	{
		//$queryCalcUsedHours = "SELECT used_hours FROM leave_user_info WHERE user_id=".$userId." AND year_info_id=".$yearId." AND leave_type_id=".$leaveTypeId;
                //$queryCalcUsedHours = "SELECT SUM(used_hours) FROM leave_user_info where user_id='".$userId."' and date between '".$startDate."' AND '".$endDate."' and leave_type_id='".$leaveTypeId."'";
		$queryCalcUsedHours = "SELECT SUM(leave_hours) FROM leave_info where user_id='".$userId."' "
                        . "and date between '".$startDate."' AND '".$endDate."' "
                        . "and leave_type_id='".$leaveTypeId."'";
                
                $params = array("user_id"=>$userId,
                                "startDate"=>$startDate,
                                "endDate"=>$endDate,
                                "leave_type_id"=>$leaveTypeId);
                
                if($leaveTypeId == 10) { // unique case for non-cumulative sick
                    $queryCalcUsedHours = "SELECT used_hours FROM leave_user_info WHERE user_id=".$userId." AND year_info_id=".$yearId." AND leave_type_id=".$leaveTypeId;
                    $params = array("user_id"=>$userId,
                                "year_info_id"=>$yearId,
                                "leave_type_id"=>$leaveTypeId);
                }
                //echo("lcuhpp query = $queryCalcUsedHours<BR>");
                $calcUsedHours = $this->sqlDataBase->singleQuery($queryCalcUsedHours, $params);
		if($calcUsedHours)
		{
			return $calcUsedHours;
		}
		else
		{
			return 0;
		}
	}
        

	/**
	 * Forces rules to run on all years very ineffecient
	 * Best in case something goes wrong
	 * Enter description here ...
	 * @param unknown_type $force
	 */
	public function SetForceApplyRules($force)
	{
		$this->force = $force;
	}
	
	/**
	 * 
	 * Load the already calculated used hours, meaning hours which rules were already run on.
	 * @param unknown_type $yearId
	 * @param unknown_type $leaveTypeId
	 * @param unknown_type $userId
	 */
	protected function LoadCalcUsedHours($yearId,$leaveTypeId,$userId)
	{
		$queryCalcUsedHours = "SELECT used_hours FROM leave_user_info "
                        . "WHERE user_id=:user_id "
                        . "AND year_info_id=:year_info_id "
                        . "AND leave_type_id=:leave_type_id";
                
                $params = array("user_id"=>$userId,
                                "year_info_id"=>$yearId,
                                "leave_type_id"=>$leaveTypeId);
                
		$calcUsedHours = $this->sqlDataBase->singleQuery($queryCalcUsedHours, $params);
		if($calcUsedHours)
		{
			return $calcUsedHours;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Load the raw used hours before the rules were applied
	 *
	 * @param unknown_type $yearId
	 * @param unknown_type $leaveTypeId
	 * @param unknown_type $userId
	 */
	protected function LoadRawUsedHours($yearId,$leaveTypeId,$userId)
	{
		$queryLeaveSum = "SELECT SUM(leave_hours)
					FROM leave_info 
					WHERE year_info_id= :year_info_id "
                        . " AND user_id= :user_id "
                        . " AND leave_type_id= :leaveTypeId "
                        . " AND status_id=:status_id "
                        . " GROUP BY leave_type_id";
                
                $params = array("year_info_id"=>$yearId,
                                "user_id"=>$userId,
                                "leaveTypeId"=>$leaveTypeId,
                                "status_id"=>APPROVED);
                
		$leaveSum = $this->sqlDataBase->singleQuery($queryLeaveSum, $params);

                if($leaveSum)
		{
			return $leaveSum;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * 
	 * Load all hours added for a user
	 * Can provide a pay period to load only leaves added prior to pay period
	 * @param unknown_type $yearId
	 * @param unknown_type $leaveTypeId
	 * @param unknown_type $userId
	 * @param unknown_type $payPeriod
	 */
	protected function LoadAddedHours($yearId,$leaveTypeId,$userId,$payPeriod=false)
	{
		$queryAddedLeaveSum = "SELECT SUM(hours) FROM added_hours "
                        . "WHERE user_id=:user_id "
                        . "AND year_info_id=:year_info_id "
                        . "AND leave_type_id=:leave_type_id";
                
                $params = array("user_id"=>$userId,
                                "year_info_id"=>$yearId,
                                "leave_type_id"=>$leaveTypeId);
                
		if($payPeriod)
		{
			$queryAddedLeaveSum .=" AND (pay_period_id < :pay_period_id OR (pay_period_id=:pay_period_id AND begining_of_pay_period))";
                        $params["pay_period_id"] = $payPeriod;
		}
		$queryAddedLeaveSum .= " GROUP BY leave_type_id";

		$addedLeaveSum = $this->sqlDataBase->singleQuery($queryAddedLeaveSum, $params);
		if($addedLeaveSum)
		{
			return $addedLeaveSum;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Load hours available from previous year due to roll over
	 * 
	 * @param unknown_type $yearId
	 * @param unknown_type $leaveTypeId
	 * @param unknown_type $userId
	 */
	protected function LoadLeaveUserInfo($yearId,$leaveTypeId,$userId)
	{
		$queryLeaveUserInfo = "SELECT initial_hours, "
                        . "leave_user_info_id "
                        . "FROM leave_user_info "
                        . "WHERE user_id=:userId "
                        . "AND year_info_id=:yearId "
                        . "AND leave_type_id=:leaveTypeId";
                $params = array("userId"=>$userId,
                                "yearId"=>$yearId,
                                "leaveTypeId"=>$leaveTypeId);
		$leaveUserInfo = $this->sqlDataBase->get_query_result($queryLeaveUserInfo, $params);
		if($leaveUserInfo)
		{
			return $leaveUserInfo;
		}
		else
		{
			return array(array("initial_hours"=>0,"leave_user_info_id"=>0));
		}
	}

	/**
	 * Loads leave type usage, also create the basic array structure we are going to return without some of the values
	 * Enter description here ...
	 * @param unknown_type $yearTypeId
	 */
	protected function LoadLeaveTypesUsage($yearTypeId)
	{
		$indexedLeaveTypes = array();

		$queryLeaveTypes = "SELECT leave_type_id, "
                        . "roll_over,"
                        . " max, "
                        . "0 as added_hours, "
                        . "0 as used_hours, "
                        . "0 as initial_hours,"
                        . "name,"
                        . "description,"
                        . "0 as est_added_hours, "
                        . "0 as calc_used_hours, "
                        . "0 as leave_user_info_id "
                        . "FROM leave_type "
                        . "WHERE year_type_id=:yearTypeId";
                
                $params = array("yearTypeId"=>$yearTypeId);

                $leaveTypes = $this->sqlDataBase->get_query_result($queryLeaveTypes, $params);
		if($leaveTypes)
		{
			foreach($leaveTypes as $id=>$leaveType)
			{
				$indexedLeaveTypes[$leaveType['leave_type_id']] = $leaveType;
			}
			return $indexedLeaveTypes;
		}
	}

	/**
	 * Update the database with a modified array after rules are ran on the original array we loaded
	 * 
	 * @param unknown_type $userId
	 * @param unknown_type $yearId
	 * @param unknown_type $usedHours
	 * @param unknown_type $initialHours
	 * @param unknown_type $leaveTypeId
	 */
	protected function UpdateLeaveUserInfo($userId,$yearId,$usedHours,$initialHours,$leaveTypeId,$leaveUserInfoId)
	{
		$queryUpdateLeaveUserInfo = "UPDATE leave_user_info SET "
                        . "used_hours=:used_hours, "
                        . "initial_hours=:initial_hours "
                        . "WHERE leave_user_info_id=:leave_user_info_id";
                
                $params = array("used_hours"=>$usedHours,
                                "initial_hours"=>$initialHours,
                                "leave_user_info_id"=>$leaveUserInfoId);

                $this->sqlDataBase->get_update_result($queryUpdateLeaveUserInfo, $params);
	}
}
?>
