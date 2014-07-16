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
		foreach($userLeaveTypesUsage as $id=>$userLeaveTypeUsage)
		{
			$leaveUserInfo =$this->LoadLeaveUserInfo($yearId,$userLeaveTypeUsage['leave_type_id'],$userId);
			$userLeaveTypesUsage[$id]['added_hours'] = $this->LoadAddedHours($yearId,$userLeaveTypeUsage['leave_type_id'],$userId,$payPeriod);
			$userLeaveTypesUsage[$id]['initial_hours'] = $leaveUserInfo[0]['initial_hours'];
			$userLeaveTypesUsage[$id]['est_added_hours'] = $this->LoadAddedHours($yearId,$userLeaveTypeUsage['leave_type_id'],$userId);
			$userLeaveTypesUsage[$id]['calc_used_hours'] = $this->LoadCalcUsedHours($yearId,$userLeaveTypeUsage['leave_type_id'],$userId);
		}
		return $userLeaveTypesUsage;
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
		$queryCalcUsedHours = "SELECT used_hours FROM leave_user_info WHERE user_id=".$userId." AND year_info_id=".$yearId." AND leave_type_id=".$leaveTypeId;
		$calcUsedHours = $this->sqlDataBase->singleQuery($queryCalcUsedHours);
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
					WHERE year_info_id=".$yearId." AND user_id=".$userId." AND leave_type_id=".$leaveTypeId." AND status_id=".APPROVED." GROUP BY leave_type_id";
		$leaveSum = $this->sqlDataBase->singleQuery($queryLeaveSum);
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
		$queryAddedLeaveSum = "SELECT SUM(hours) FROM added_hours WHERE user_id=".$userId." AND year_info_id=".$yearId." AND leave_type_id=".$leaveTypeId;
		if($payPeriod)
		{
			$queryAddedLeaveSum .=" AND (pay_period_id < ".$payPeriod." OR (pay_period_id=".$payPeriod." AND begining_of_pay_period))";
		}
		$queryAddedLeaveSum .= " GROUP BY leave_type_id";
		$addedLeaveSum = $this->sqlDataBase->singleQuery($queryAddedLeaveSum);
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
		$queryLeaveUserInfo = "SELECT initial_hours, leave_user_info_id FROM leave_user_info WHERE user_id=".$userId." AND year_info_id=".$yearId." AND leave_type_id=".$leaveTypeId;
		$leaveUserInfo = $this->sqlDataBase->query($queryLeaveUserInfo);
		if($leaveUserInfo)
		{
			return $leaveUserInfo;
		}
		else
		{
			return array("initial_hours"=>0,"leave_user_info_id"=>0);
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

		$queryLeaveTypes = "SELECT leave_type_id, roll_over, max, 0 as added_hours, 0 as used_hours, 0 as initial_hours,name,description,0 as est_added_hours, 0 as calc_used_hours, 0 as leave_user_info_id FROM leave_type WHERE year_type_id=".$yearTypeId;
		$leaveTypes = $this->sqlDataBase->query($queryLeaveTypes);
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
		$queryUpdateLeaveUserInfo = "UPDATE leave_user_info SET used_hours=".$usedHours.", initial_hours=".$initialHours." WHERE leave_user_info_id=".$leaveUserInfoId;
		$this->sqlDataBase->nonSelectQuery($queryUpdateLeaveUserInfo);
	}
}
?>
