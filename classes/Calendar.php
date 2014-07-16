<?php
/**
 * Class Calendar.php
 * This class draws the calendar base on the month year and user
 * 
 * @author nevoband
 *
 */
class Calendar
{
	private $sqlDataBase;
	private $Url;
	private $specialDays = array();
	private $defaultDayBg = "ffffff";

	public function __construct(SQLDataBase $sqlDataBase)
	{
		$this->sqlDataBase = $sqlDataBase;
	}

	public function __destruct()
	{

	}

	/**
	 * Return the calendar table
	 * 
	 * @param unknown_type $month
	 * @param unknown_type $year
	 * @param unknown_type $users
	 * @param User $loggedUser
	 */
	public function Show($month,$year,$users,User $loggedUser)
	{
		$this->LoadSpecialDays($loggedUser->getUserId());
		$years = new Years($this->sqlDataBase);

		$month_name = Date('F',mktime(0, 0, 0, $month, 1, $year));

		$this_month = Date('n',mktime(0, 0, 0, $month, 1, $year));
		$next_month = Date('n',mktime(0, 0, 0, $month + 1, 1, $year));

		//Find out when this month starts and ends.
		$first_week_day =  Date('w',mktime(0, 0, 0, $month, 1, $year));;
		$days_in_this_month = Date('t',mktime(0, 0, 0, $month, 1, $year));

		$monthLeaves = $this->DateMonthEvents($month,$year,$users);

		$calendar_html = "";
		$calendar_html .= "<table class=\"month\" cellspacing=\"1\">";
		$calendar_html .= "<tr><td class=\"calendar_day_title\" width=\"15%\"> SUNDAY </td><td class=\"calendar_day_title\" width=\"14%\"> MONDAY </td><td class=\"calendar_day_title\" width=\"14%\"> TUESDAY </td><td class=\"calendar_day_title\" width=\"14%\"> WEDNESDAY </td><td class=\"calendar_day_title\" width=\"14%\">THURSDAY </td><td class=\"calendar_day_title\" width=\"14%\"> FRIDAY </td><td class=\"calendar_day_title\" width=\"15%\"> SATURDAY </td></tr>";
		$calendar_html .= "<tr>";

		//Fill the first week of the month with the appropriate number of blanks.
		for($week_day = 0; $week_day < $first_week_day; $week_day++)
		{
			$calendar_html .= "<td class=\"calendar_notaday\"> </td>";
		}

		$week_day = $first_week_day;

		//Create the real days of the month
		for($day_counter = 1; $day_counter <= $days_in_this_month; $day_counter++)
		{
			$dayStyle = "";
			$dayName = "";

			$week_day %= 7;

			if($week_day == 0)
			$calendar_html .= "</tr><tr>";

			$specialDays = $this->CheckForSpecialDay($day_counter,$month,$year,$users);
				
			$specialDayBlocked = false;
			foreach($specialDays as $specialDay)
			{
				$dayName .= "<div style=\"background-color:#".$specialDay->getColor().";height:15px;color:#000000;border-radius: 5px;-moz-border-radius: 10px;padding:2px;\">".$specialDay->getName()."</div>";
				if($specialDay->getBlocked())
				{
					$specialDayBlocked = true;
				}
			}
			$dayBackground = $this->defaultDayBg;

			if(array_key_exists($day_counter,$monthLeaves))
			{
				$dayEvents = $monthLeaves[$day_counter];
			}
			else
			{
				$dayEvents = null;
			}

			$calendar_html .= "<td ";
			if( ( !$specialDayBlocked && !$years->isAllLocked($day_counter,$month,$year) && $years->Exists($day_counter,$month,$year)) || ($loggedUser->getUserPermId()==ADMIN && $years->Exists($day_counter,$month,$year) ))
			{
				if($day_counter."-".$month."-".$year==Date('j-n-Y'))
				{
					$calendar_html .= "class=\"calendar_day\" style=\"background-color:#FEFEE2\" ";
				}
				else
				{
					$calendar_html .= "class=\"calendar_day\" ";
				}
				$calendar_html .= "id=\"".date("d",mktime(0,0,0,$month,$day_counter,$year))."\"";
				$calendar_html .= " onmouseover=\"if(mouseIsDown){updateSelection(this);}\"";
			}
			else
			{
				$calendar_html .= "class=\"calendar_day_locked\" ";
			}
			$calendar_html .="><div id=\"day_shadow\">".$day_counter."</div>".$dayName;
			$calendar_html .= "<div id=\"hiddenArea\"><input type=\"checkbox\" name=\"leaveDays[]\" value=\"".date("d",mktime(0,0,0,$month,$day_counter,$year))."\"></div>";
			$deviceBox = 0;
			if(isset($dayEvents))
			{
				$rowNum=0;
				foreach($dayEvents as $id=>$dayEvent)
				{
					$calendar_html .= "<div id=\"".$dayEvent['leave_id']."\" class=\"calendar_event\"";
					$calendar_html .= "style=\"background-color:#".$dayEvent['calendar_color']."\">";
					$calendar_html .= "<div id=\"status\"><table><tr>";
					if($dayEvent['status_id']==NEW_LEAVE)
					{
						$calendar_html .= "<td><input class=\"leave_select_box\" id=\"".$dayEvent['leave_id']."_LeaveCheckBox\" type=\"checkbox\" name=\"leaveIds[]\" value=".$dayEvent['leave_id']."></td><td><label for=\"".$dayEvent['leave_id']."_LeaveCheckBox\"> New</label></td>";
					}
					elseif($dayEvent['status_id']==APPROVED)
					{
						$calendar_html .= "<td><img src=\"css/images/approved.png\"></td><td> Approved<br></td>";
					}
					elseif($dayEvent['status_id']==WAITING_APPROVAL)
					{
						if($loggedUser->getUserId()==$dayEvent['supervisor_id'])
						{
							$calendar_html .= "<td><input class=\"leave_select_box\" id=\"".$dayEvent['leave_id']."_LeaveCheckBox\" type=\"checkbox\" name=\"leaveIds[]\" value=".$dayEvent['leave_id']."></td><td><label for=\"".$dayEvent['leave_id']."_LeaveCheckBox\">Waiting Approval</label> </td>";
						}
						else
						{
							$calendar_html .= "<td><img src=\"css/images/waiting.png\"></td><td> Waiting Approval</td>";
						}
					}
					elseif($dayEvent['status_id']==NOT_APPROVED)
					{
						$calendar_html .= "<td><img src=\"css/images/notapproved.png\"></td><td> Not Approved</td>";
					}
					$calendar_html .= "</tr></table></div>";
					$calendar_html .= $dayEvent['first_name']." ".$dayEvent['last_name']."<br>";
					$calendar_html .= $dayEvent['leave_hours']." Hours";
					$calendar_html .= "</div>";
					echo "<script type=\"text/javascript\">AddLeaveInfo(\"".$dayEvent['leave_id']."\",".$dayEvent['user_id'].",".$dayEvent['time'].",".$dayEvent['leave_type_id'].",".$dayEvent['leave_type_id_special'].",\"".(str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ),$dayEvent['description']))."\",".$day_counter.");</script>";
				}
			}
			$calendar_html .= "<br><br></td>";
			$week_day++;
		}
		for($i=$week_day; $i<7; $i++)
		{
			$calendar_html .="</td><td class=\"calendar_notaday\" onmouseup=\"showPopup(300,240);\">";
		}
		$calendar_html .= "</td></tr>";
		$calendar_html .= "</table>";
		return($calendar_html);
	}

	/**
	 * Get all events for this user for the month
	 * 
	 * @param unknown_type $month
	 * @param unknown_type $year
	 * @param unknown_type $users
	 */
	private function DateMonthEvents($month,$year, $users)
	{
		$dateEventsArr = array();

		$queryLeaves = "SELECT li.leave_id, li.date, li.description, li.user_id, u.first_name, u.last_name, lt.calendar_color, li.leave_hours, lt.name, li.status_id, li.leave_type_id, li.leave_type_id_special, li.time, DAY(li.date) as day, u.supervisor_id
                                FROM leave_info li, users u , leave_type lt
                                WHERE MONTH(li.date)=".$month." AND YEAR(li.date)=".$year." AND u.user_id = li.user_id AND lt.leave_type_id = li.leave_type_id AND(";

		foreach($users as $user)
		{
			$queryLeaves .= " li.user_id=$user OR";
		}
		$queryLeaves .= " li.user_id = 0)";

		$events = $this->sqlDataBase->query($queryLeaves);

		if(isset($events))
		{
			foreach($events as $id=>$event)
			{
				if(!array_key_exists($event['day'], $dateEventsArr))
				{
					$dateEventsArr[$event['day']] = array($event['leave_id']=>$event);
				}
				else
				{
					$dateEventsArr[$event['day']][$event['leave_id']] = $event;
				}
			}
		}
		return $dateEventsArr;
	}

	/**
	 * Delete an event from the calendar
	 * 
	 * @param unknown_type $leaveId
	 */
	public function DeleteEvent($leaveId)
	{
		if($this->AuthorizeEventAction($eventID))
		{
			$queryDelLeave = "DELETE FROM leave WHERE leave_id=".$leaveId;
			$this->sqlDataBase->nonSelectQuery($queryDelLeave);
			$queryDelLeaveDay = "DELETE FROM leave WHERE leave_id=".$leaveId;
			$this->sqlDataBase->nonSelectQuery($queryDelLeaveDay);
		}
		else
		{
			echo "<font color=\"red\">Notice: Failed to authorize or event does not exist</font>";
		}
	}


	/**
	 * Load all special days as in holidays etc..
	 * 
	 * @param unknown_type $loggedUserId
	 */
	private function LoadSpecialDays($loggedUserId)
	{
		$querySpecialDaysIds = "SELECT id FROM calendar_special_days WHERE user_id=".$loggedUserId." OR user_id=0 ORDER BY priority DESC";
		$specialDaysIds = $this->sqlDataBase->query($querySpecialDaysIds);
		foreach($specialDaysIds as $id=>$specialDayId)
		{
			$specialDay = new SpecialDay($this->sqlDataBase);
			$specialDay->LoadSpecialDay($specialDayId['id']);
			$this->specialDays[]=$specialDay;
		}
	}

	/**
	 * Check whether or not a date has a special day
	 * used to save on queries from the database by loading all events for the month once
	 * using the LoadSpecialDays function and then searching the array for each date.
	 * 
	 * @param unknown_type $day
	 * @param unknown_type $month
	 * @param unknown_type $year
	 * @param unknown_type $userId
	 */
	private function CheckForSpecialDay($day,$month,$year,$userId)
	{
		$priority = 0;
		$specialDaysToReturn = array();

		foreach($this->specialDays as $specialDay)
		{
			if($specialDay->CheckDay($day,$month,$year))
			{
				$specialDaysToReturn[] = $specialDay;
			}
		}

		return $specialDaysToReturn;
	}

}

?>
