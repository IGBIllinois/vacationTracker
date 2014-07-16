<?php
/**
 * class FiscalYearRules
 * this class extends Rules.php
 * Used check rules for all fiscal year leaves on a particular year
 * Enter description here ...
 * @author nevoband
 *
 */
class FiscalYearRules extends Rules
{
	/**
	 * Run rules for fiscal year
	 * @see Rules::RunRules()
	 */
	public function RunRules($userId,$yearId, $userYearUsage=null)
	{
		$hasRollOver = 0;
		//Check if already loaded a year before this one
		if($userYearUsage==null)
		{
			$userYearUsage = $this->LoadUserYearUsage($userId,$yearId);
		}

		//Apply rules to leave (no real rules to apply)
	
		//Check for roll overs
		foreach($userYearUsage as $id=>$userYearUsageLeaveType)
		{
			//Check whether anything needs to be updated, mostly used for performance reasons
			if($this->LoadCalcUsedHours($yearId, $userYearUsageLeaveType['leave_type_id'], $userId)!=$userYearUsageLeaveType['used_hours'] || $this->force)
			{
				//Update the database with the new used hours
				$this->UpdateLeaveUserInfo($userId,$yearId,$userYearUsageLeaveType['used_hours'],$userYearUsageLeaveType['initial_hours'],$userYearUsageLeaveType['leave_type_id'],$userYearUsageLeaveType['leave_user_info_id']);
				if($userYearUsageLeaveType['roll_over'])
				{
					$hasRollOver = 1;
				}
			}
		}

		//Check if there exists a year after this one
		$nextYearId = $this->year->NextYearId($yearId);
		
		//If next year exists then apply roll over rules to it.
		if($nextYearId && ($hasRollOver || $this->force))
		{
			$nextYearUsage = $this->LoadUserYearUsage($userId,$nextYearId);
			
			if($hasRollOver)
			{	
				foreach($nextYearUsage as $id=>$nextYearUsageLeaveType)
				{
					if($nextYearUsageLeaveType['roll_over'])
					{
						$nextYearUsage[$id]['initial_hours'] = $userYearUsage['initial_hours']+$userYearUsage['added_hours']-$userYearUsage['used_hours'];
					}
				}
			}
			//Run rules on next year since roll over rules were applied to it
			$this->RunRules($userId,$nextYearId,$nextYearUsage);
		}

	}
}

?>
