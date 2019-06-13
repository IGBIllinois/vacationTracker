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
		$queryLeaveType = "SELECT name, description, calendar_color, special, hidden, roll_over, max, default_value, year_type_id FROM leave_type WHERE leave_type_id=:typeId";
                $params = array("typeId"=>$typeId);
		$leaveType = $this->sqlDataBase->get_query_result($queryLeaveType, $params);
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
		$this->name = $name;
		$this->description = $description;
		$this->color = $color;
		$this->special = $special;
		$this->hidden = $hidden;
		$this->rollOver = $rollOver;
		$this->max = ($max)?$max:0;
		$this->defaultValue = ($defaultValue)?$defaultValue:0;
		$this->yearTypeId = $yearType;

		$queryInsertLeaveType = "INSERT INTO leave_type ("
                        . "name,"
                        . "description,"
                        . "calendar_color, "
                        . "special, "
                        . "hidden, "
                        . "roll_over, "
                        . "max, "
                        . "default_value,y"
                        . "ear_type_id)"
                        . "VALUES(".
                            ":name,".
                            ":description,".
                            ":color, ".
                            ":special, ".
                            ":hidden, ".
                            ":rollOver,".
                            ":max,".
                            ":defaultValue,".
                            ":yearTypeId)";
                
                $params = array("name"=>$this->name,
                        "description"=>$this->description,
                        "color"=>$this->color,
                        "special"=>$this->special,
                        "hidden"=>$this->hidden,
                        "rollOver"=>$this->rollOver,
                        "max"=>$this->max,
                        "defaultValue"=>$this->defaultValue,
                        "yearTypeId"=>$this->yearTypeId);
                
		$this->typeId = $this->sqlDataBase->get_insert_result($queryInsertLeaveType, $params);
		if($this->typeId)
		{
			$queryAllUsers = "SELECT user_id FROM users";
			$allUsers = $this->sqlDataBase->get_query_result($queryAllUsers);

			$queryYears = "SELECT year_info_id FROM year_info WHERE year_type_id=:yearTypeId";
                        $params = array("yearTypeId"=>$this->yearTypeId);
                        
			$years = $this->sqlDataBase->get_query_result($queryYears, $params);

			foreach($allUsers as $id=>$user)
			{
				foreach($years as $id=>$year)
				{
					$queryInsertLeaveCount = "INSERT INTO leave_user_info ("
                                                . "user_id,"
                                                . "leave_type_id,"
                                                . "used_hours,"
                                                . "hidden, "
                                                . "year_info_id,"
                                                . "initial_hours,"
                                                . "added_hours)"
                                                . "VALUES("
                                                    . ":user_id,"
                                                    . ":leave_type_id,"
                                                    . "0, "
                                                    . ":hidden, "
                                                    . ":year_info_id,"
                                                    . "0,"
                                                    . "0)";
                                        
                                        $params = array("user_id"=>$user['user_id'],
                                                        "leave_type_id"=>$this->typeId,
                                                        "hidden"=>$this->hidden,
                                                        "year_info_id"=>$year['year_info_id']);

					$result = $this->sqlDataBase->get_insert_result($queryInsertLeaveCount, $params);

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
		$queryUpdateLeaveType = "UPDATE leave_type SET "
                        . "name=:name, "
                        . "description=:description, "
                        . "calendar_color=:calendar_color,"
                        . "special=:special, "
                        . "hidden=:hidden, "
                        . "roll_over=:roll_over, "
                        . "max=:max, "
                        . "default_value=:default_value, "
                        . "year_type_id=:year_type_id "
                        . " WHERE leave_type_id=:type_id";
                
                $params = array("name"=>$this->name,
                        "description"=>$this->description,
                        "calendar_color"=>$this->color,
                        "special"=>$this->special,
                        "hidden"=>$this->hidden,
                        "roll_over"=>$this->rollOver,
                        "max"=>$this->max,
                        "default_value"=>$this->defaultValue,
                        "year_type_id"=>$this->yearTypeId,
                        "type_id"=>$this->typeId);

		$this->sqlDataBase->get_update_result($queryUpdateLeaveType, $params);
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
        
        // Static functions
        
        /** Gets the Leave Types in the database
         * 
         * @param SQLDataBase $sqlDataBase The database object
         * @param int $yearType ID of the year type to get leave types for
         * @return \LeaveType An array of LeaveType objects
         */
        public static function GetLeaveTypes($sqlDataBase, $yearType) {
            $queryLeaveTypes = "SELECT leave_type_id FROM leave_type WHERE year_type_id=:year_type_id";
            $params = array("year_type_id"=>$yearType);
            $leaveTypes = $sqlDataBase->get_query_result($queryLeaveTypes, $params);
            
            $types = array();
            foreach($leaveTypes as $leaveType) {
                $new_type = new LeaveType($sqlDataBase);
                $new_type->LoadLeaveType($leaveType['leave_type_id']);
                $types[] = $new_type;
                        
            }
            return $types;
        }
}

?>
