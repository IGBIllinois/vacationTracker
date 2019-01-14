<?php
/**
 * Class SpcialDay.php
 * A special day is a holiday or any day which shoudl be mentioned on a calendar
 * as reminder to users, can also be used to block certain days from reserving leaves
 * @author nevoband
 *
 */
class SpecialDay
{

	private $sqlDataBase;
	private $dayId;
	private $name;
	private $description;
	private $color;
	private $blocked;
	private $month;
	private $day;
	private $year;
	private $weekDay;
	private $priority;
	private $userId;

	public function __construct(SQLDataBase $sqlDataBase)
	{
		$this->sqlDataBase=$sqlDataBase;
	}

	public function __desctruct()
	{

	}

	/**
	 * Create a special day and store its values in the database
	 * 
	 * @param unknown_type $description
	 * @param unknown_type $name
	 * @param unknown_type $color
	 * @param unknown_type $blocked
	 * @param unknown_type $month
	 * @param unknown_type $day
	 * @param unknown_type $year
	 * @param unknown_type $priority
	 * @param unknown_type $weekDay
	 * @param unknown_type $userId
	 */
	public function CreateSpecialDay($description,$name,$color,$blocked,$month,$day,$year,$priority,$weekDay,$userId)
	{
		$this->description = $description;
		$this->name = $name;
		$this->color = $color;
		$this->blocked = $blocked;
		$this->month = $month;
		$this->day = $day;
		$this->year = $year;
		$this->priority = $priority;
		$this->weekDay = $weekDay;
		$this->userId = $userId;

		$queryInsertDay = "INSERT INTO calendar_special_days "
                        . "(description,color,blocked,month,day,year,priority,week_day,name,user_id)"
                        . "VALUES(:description, :color, :blocked, :month, :day, :year, :priority, :weekDay, :name, :userId)";
		$params = array("description"=>$description,
                                "color"=>$color,
                                "blocked"=>$blocked,
                                "month"=>$month,
                                "day"=>$day,
                                "year"=>$year,
                                "priority"=>$priority,
                                "weekDay"=>$weekDay,
                                "name"=>$name,
                                "userId"=>$userId);
                    
                
                $this->dayId = $this->sqlDataBase->get_insert_result($queryInsertDay, $params);
	}

	/**
	 * Load a spcial day from the database and store its values in this object.
	 * 
	 * @param unknown_type $dayId
	 */
	public function LoadSpecialDay($dayId)
	{
		$querySelectDay = "SELECT description,color,blocked,month,day,year,priority,week_day,name,user_id FROM calendar_special_days WHERE id=:dayId";
                $params = array("dayId"=>$dayId);
                $selectDay = $this->sqlDataBase->get_query_result($querySelectDay, $params);
		$this->description = $selectDay[0]['description'];
		$this->color = $selectDay[0]['color'];
		$this->blocked = $selectDay[0]['blocked'];
		$this->month = $selectDay[0]['month'];
		$this->day = $selectDay[0]['day'];
		$this->year = $selectDay[0]['year'];
		$this->priority = $selectDay[0]['priority'];
		$this->weekDay = $selectDay[0]['week_day'];
		$this->name = $selectDay[0]['name'];
		$this->dayId = $dayId;
		$this->userId = $selectDay[0]['user_id'];
	}

	/**
	 * Apply all changes to this object onto the database
	 * 
	 */
	public function UpdateDb()
	{
		$queryUpdateDb = "UPDATE calendar_special_days SET "
                        . "description=:description, "
                        . "color=:color, "
                        . "blocked=:blocked, "
                        . "month=:month, "
                        . "day=:day, "
                        . "year=:year, "
                        . "priority=:priority, "
                        . "name=:name, "
                        . "week_day=:weekDay, "
                        . "user_id=:userId "
                        . "WHERE id=:day_id";
                
                $params = array("description"=>$this->description,
                                "color"=>$this->color,
                                "blocked"=>$this->blocked,
                                "month"=>$this->month,
                                "day"=>$this->day,
                                "year"=>$this->year,
                                "priority"=>$this->priority,
                                "weekDay"=>$this->weekDay,
                                "name"=>$this->name,
                                "userId"=>$this->userId,
                                "day_id"=>$this->dayId
                        );
                
		$this->sqlDataBase->get_update_result($queryUpdateDb, $params);
	}

	/**
	 * Delete this special day from the database
	 *
	 */
	public function Delete()
	{
		$queryDeleteDay = "DELETE FROM calendar_special_days WHERE id= :day_id";
                $params = array("day_id"=>$this->dayId);
		$this->sqlDataBase->get_update_result($queryDeleteDay, $params);
	}
       

	/**
	 * Check if this special day exists on a particual day
	 * 
	 * @param unknown_type $day
	 * @param unknown_type $month
	 * @param unknown_type $year
	 */
	public function CheckDay($day, $month, $year)
	{
		if(($day==$this->day || $this->day==0) && 
                        ($month==$this->month || $this->month==0) && 
                        ($year==$this->year || $this->year==0) && 
                        ($this->weekDay==(Date("w",mktime(1,1,1,$month,$day,$year))+1) || $this->weekDay==0))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	//Getters and Setters -------------------------------------------------------------------------------------------------------
	
	public function getDayId() { return $this->dayId; }
	public function getDescription() { return $this->description; }
	public function getColor() { return $this->color; }
	public function getBlocked() { return $this->blocked; }
	public function getMonth() { return $this->month; }
	public function getDay() { return $this->day; }
	public function getYear() { return $this->year; }
	public function getPriority() { return $this->priority; }
	public function getWeekDay() { return $this->weekDay; }
	public function getName() { return $this->name; }
	public function getUserId() { return $this->userId; }

	public function setDescription($x) { $this->description = $x; }
	public function setColor($x) { $this->color = $x; }
	public function setBlocked($x) { $this->blocked = $x; }
	public function setMonth($x) { $this->month = $x; }
	public function setDay($x) { $this->day = $x; }
	public function setYear($x) { $this->year = $x; }
	public function setPriority($x) { $this->priority = $x; }
	public function setWeekDay($x) { $this->weekDay = $x; }
	public function setName($x) { $this->name = $x; }
	public function setUserId($x) { $this->userId = $x; }
        
        
        // Static Functions
        public static function getDaysForUser($sqlDataBase, $userId) {
            $querySpecialDays = "SELECT * FROM calendar_special_days WHERE user_id=:userId";
            $params = array("userId"=>$userId);
            $result = $sqlDataBase->get_query_result($querySpecialDays, $params);
            $days = array();
            foreach($result as $specialDay) {
                $this_day = new SpecialDay($sqlDataBase);
                $this_day->LoadSpecialDay($specialDay['id']);
                $days[] = $this_day;
            }
            
            return $days;
        }
}

?>
