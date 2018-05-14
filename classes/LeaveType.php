<?php
/**
 * Class LeaveType.php
 * This class allows loading of leave type information
 * from database into an object. 
 *
 * @author nevoband
 *
 */
class LeaveType
{
	private $sqlDataBase;
	private $typeId;
	private $name;
	private $description;
	private $color;
	private $special;
	private $hidden;
	private $rollOver;
	private $max;
	private $defaultValue;
	private $yearTypeId;

	public function __construct(SQLDataBase $sqlDataBase)
	{
		$this->sqlDataBase = $sqlDataBase;
		$this->special = 0;
		$this->hidden = 0;
		$this->typeId = 0;
	}

	public function __destruct()
	{

	}

	/**
	 * Load leave type information from database into this object
	 * 
	 * @param unknown_type $typeId
	 */
	public function LoadLeaveType($typeId)
	{
		$queryLeaveType = "SELECT name, description, calendar_color, special, hidden, roll_over, max, default_value, year_type_id FROM leave_type WHERE leave_type_id=".$typeId;
		$leaveType = $this->sqlDataBase->query($queryLeaveType);
		$this->typeId = $typeId;
		$this->name = $leaveType[0]['name'];
		$this->color = $leaveType[0]['calendar_color'];
		$this->description = $leaveType[0]['description'];
		$this->special = $leaveType[0]['special'];
		$this->hidden = $leaveType[0]['hidden'];
		$this->rollOver = $leaveType[0]['roll_over'];
		$this->max = $leaveType[0]['max'];
		$this->defaultValue = $leaveType[0]['default_value'];
		$this->yearTypeId = $leaveType[0]['year_type_id'];
	}

	/**
	 * Create leave type and enter it's values into the database.
	 * 
	 * @param unknown_type $name
	 * @param unknown_type $description
	 * @param unknown_type $color
	 * @param unknown_type $special
	 * @param unknown_type $hidden
	 * @param unknown_type $rollOver
	 * @param unknown_type $max
	 * @param unknown_type $defaultValue
	 * @param unknown_type $yearType
	 */
	public function CreateLeaveType($name, $description, $color, $special, $hidden, $rollOver, $max, $defaultValue,$yearType)
	{
		$this->name = mysqli_real_escape_string($name);
		$this->description = mysqli_real_escape_string($description);
		$this->color = $color;
		$this->special = $special;
		$this->hidden = $hidden;
		$this->rollOver = $rollOver;
		$this->max = ($max)?$max:0;
		$this->defaultValue = ($defaultValue)?$defaultValue:0;
		$this->yearTypeId = $yearType;

		$queryInsertLeaveType = "INSERT INTO leave_type (name,description,calendar_color, special, hidden, roll_over, max, default_value,year_type_id)VALUES(\"".$this->name."\",\"".$this->description."\",\"".$this->color."\", ".$this->special.", ".$this->hidden.", ".$this->rollOver.",".$this->max.",".$this->defaultValue.",".$this->yearTypeId.")";
		$this->typeId = $this->sqlDataBase->insertQuery($queryInsertLeaveType);
		if($this->typeId)
		{
			$queryAllUsers = "SELECT user_id FROM users";
			$allUsers = $this->sqlDataBase->query($queryAllUsers);

			$queryYears = "SELECT year_info_id FROM year_info WHERE year_type_id=".$this->yearTypeId;
			$years = $this->sqlDataBase->query($queryYears);

			foreach($allUsers as $id=>$user)
			{
				foreach($years as $id=>$year)
				{
					$queryInsertLeaveCount = "INSERT INTO leave_user_info (user_id,leave_type_id,used_hours,hidden, year_info_id)VALUES(".$user['user_id'].",".$this->typeId.",0, ".$this->hidden.", ".$year['year_info_id'].")";
					$this->sqlDataBase->insertQuery($queryInsertLeaveCount);
				}
			}
			return $this->typeId;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Apply changes of object to the database
	 * 
	 */
	public function UpdateDb()
	{
		$queryUpdateLeaveType = "UPDATE leave_type SET name=\"".$this->name."\", description=\"".$this->description."\", calendar_color=\"".$this->color."\",special=".$this->special.", hidden=".$this->hidden.", roll_over=".$this->rollOver.", max=".$this->max.", default_value=".$this->defaultValue.", year_type_id=".$this->yearTypeId." WHERE leave_type_id=".$this->typeId;
		$this->sqlDataBase->nonSelectQuery($queryUpdateLeaveType);
	}

	//Getters and Setters ----------------------------------------------------------------------------------------------
	
	public function getTypeId() { return $this->typeId; }
	public function getName() { return stripslashes($this->name); }
	public function getDescription() { return stripslashes($this->description); }
	public function getColor() { return $this->color; }
	public function getSpecial() { return $this->special; }
	public function getHidden() { return $this->hidden; }
	public function getRollOver() { return $this->rollOver; }
	public function getMax() { return $this->max; }
	public function getDefaultValue() { return $this->defaultValue; }
	public function getYearTypeId() { return $this->yearTypeId; }

	public function setName($x) { $this->name = $x; }
	public function setDescription($x) { $this->description = $x; }
	public function setColor($x) { $this->color = $x; }
	public function setSpecial($x) { $this->special = $x; }
	public function setHidden($x) { $this->hidden = $x; }
	public function setRollOver($x) { $this->rollOver = $x; }
	public function setMax($x) { $this->max = $x; }
	public function setDefaultValue($x) { $this->defaultValue = $x; }
	public function setYearTypeId($x) { $this->yearTypeId = $x; }
}

?>
