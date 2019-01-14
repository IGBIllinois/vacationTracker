<?php
/**
 * UI add_leaves.php
 * Creates a UI containing input boxes to add leaves
 * for a particular user on a particular pay period.
 * Also contains UI input boxes to select which added leaves to show
 * in the added_leaves_list.php
 * 
 * @author Nevo Band
 */
$selectedYearTypeId = 0;
$selectedYearId = 0;
$selectedPayPeriodId = 0;
$selectedUser = 0;
$selectedLeaveType = 0;
$selectedHoursToAdd = 0;
$selectedDescription = "";
$selectedHoursAddBegin = 0;

if(isset($_POST['yearType']))
{
	if($_POST['yearType'] > 0)
	{
		$selectedYearTypeId = $_POST['yearType'];

		if($_POST['year'] > 0)
		{
			$selectedYearId = $_POST['year'];
			if($_POST['payPeriod']>0)
			{
				$selectedPayPeriodId = $_POST['payPeriod'];
			}
		}
	}
	if($_POST['addToUser'])
	{
		$selectedUser = $_POST['addToUser'];
	}
	if($_POST['leaveType']>0)
	{
		$selectedLeaveType = $_POST['leaveType'];
	}
	if($_POST['hoursToAdd'] >0 && is_numeric($_POST['hoursToAdd']))
	{
		$selectedHoursToAdd = $_POST['hoursToAdd'];
	}
	$selectedDescription = $_POST['addLeavesDescription'];
	$selectedHoursAddBegin = $_POST['hoursAddBegin'];
}

?>



<table class="form_field" width="100%">
	<tr>
		<td colspan=2 class="col_title">Add Leaves</td>
	</tr>

	<tr>
		<td class="form_field">Year Type:</td>
		<td class="form_field"><select name="yearType"
			onchange="this.form.submit()">
				<option value=0>None</option>
				<?php

                                $yearTypesInfo = Years::GetYearTypes($sqlDataBase);
				if($yearTypesInfo)
				{
					foreach($yearTypesInfo as $id=>$yearTypeInfo)
					{
						echo "<option value=".$yearTypeInfo['year_type_id'];
						if($selectedYearTypeId==$yearTypeInfo['year_type_id'])
						{
							echo " SELECTED";
						}
						echo ">".$yearTypeInfo['name']."</option>";
					}
				}
				?>
		</select>
		</td>
	</tr>
	<tr>
		<td class="form_field">Year:</td>
		<td class="form_field"><select name="year"
			onchange="this.form.submit()">
				<option vlue=0>None</option>
				<?php
				$queryYearsInfo = "SELECT start_date,end_date,year_info_id FROM year_info "
                                        . "WHERE year_type_id=:year_type_id ORDER BY start_date";
                                
                                $params = array("year_type_id"=>$selectedYearTypeId);
                                
                                $yearsInfo = $sqlDataBase->get_query_result($queryYearsInfo, $params);

				if($yearsInfo)
				{
					foreach($yearsInfo as $id=>$yearInfo)
					{
						echo "<option value=".$yearInfo['year_info_id'];
						if($selectedYearId==$yearInfo['year_info_id'])
						{
							echo " SELECTED";
						}
						echo ">".Date('M Y',strtotime($yearInfo['start_date']))." - ".Date('M Y',strtotime($yearInfo['end_date'])) ."</option>";
					}
				}
				?>
		</select>
		</td>
	</tr>
	<tr>
		<td class="form_field">Pay Period:</td>
		<td class="form_field"><select name="payPeriod">
				<option value=0>None</option>
				<?php
				$queryPayPeriod = "SELECT pay_period_id,start_date,end_date FROM pay_period WHERE year_info_id=:year_info_id";
                                $params = array("year_info_id"=>$selectedYearId);
                                $payPeriod = $sqlDataBase->get_query_result($queryPayPeriod, $params);

				if($payPeriod)
				{
					foreach($payPeriod as $id=>$payPeriodInfo)
					{
						echo "<option value=".$payPeriodInfo['pay_period_id'];
						if($selectedPayPeriodId == $payPeriodInfo['pay_period_id'])
						{
							echo " SELECTED";
						}
						echo ">".Date('M jS',strtotime($payPeriodInfo['start_date']))." - ".Date('M jS',strtotime($payPeriodInfo['end_date'])) ."</option>";
					}
				}
				?>
		</select>
		</td>
	</tr>
	<tr>
		<td class="form_field">Employee Type:</td>
		<td class="form_field"><select name="employeeType">
		<?php
		$queryEmployeeTypes = "SELECT user_type_id, name FROM user_type";
		$employeeTypes = $sqlDataBase->get_query_result($queryEmployeeTypes);
		if($employeeTypes)
		{
			foreach($employeeTypes as $id=>$employeeType)
			{
				echo "<option value=".$employeeType['user_type_id'].">".$employeeType['name']."</option>";
			}
		}
		?>
		</select>
		</td>
	</tr>
	<tr>
		<td class="form_field">User:</td>
		<td class="form_field"><select name="addToUser">
				<option value=0 <?php echo ($selectedUser==0)?"SELECTED":""; ?>>All+Template</option>
				<option value="-1" <?php echo ($selectedUser==-1)?"SELECTED":""; ?>>Template</option>
				<option value="-2" <?php echo ($selectedUser==-2)?"SELECTED":""; ?>>All</option>
				<?php
				$queryUsers = "SELECT user_id, netid FROM users WHERE enabled=".ENABLED." ORDER BY netid ASC";
				$users = $sqlDataBase->get_query_result($queryUsers);
				if($users)
				{
					foreach($users as $id=>$userInfo)
					{
						echo "<option value=".$userInfo['user_id'];
						if($selectedUser==$userInfo['user_id'])
						{
							echo " SELECTED";
						}
						echo ">".$userInfo['netid']."</option>";
					}
				}
				?>
		</select>
		</td>
	</tr>
	<tr>
		<td class="form_field">Leave Type:</td>
		<td class="form_field"><select name="leaveType">
				<option value=0>None</option>
				<?php
				$queryLeaveTypes = "SELECT leave_type_id, name FROM leave_type WHERE year_type_id=:year_type_id";
                                $params = array("year_type_id"=>$selectedYearTypeId);
                                $leaveTypes = $sqlDataBase->get_query_result($queryLeaveTypes, $params);

				if($leaveTypes)
				{
					foreach($leaveTypes as $id=>$leaveType)
					{
						echo "<option value=".$leaveType['leave_type_id'];
						if($selectedLeaveType == $leaveType['leave_type_id'])
						{
						echo " SELECTED";
						}
						echo ">".$leaveType['name']."</option>";
					}
				}
				?>
		</select>
		</td>
	</tr>
	<tr>
		<td class="form_field">Hours To Add:</td>
		<td class="form_field"><input type="text"
			value=<?php echo $selectedHoursToAdd; ?> name="hoursToAdd">
		</td>
	</tr>
	<tr>
		<td class="form_field">Add Hours On:</td>
		<td class="form_field"><select name="hoursAddBegin">
				<option value="0"
				<?php echo ($selectedHoursAddBegin)?"":"SELECTED"; ?>>Pay Period
					Ending</option>
				<option value="1"
				<?php echo ($selectedHoursAddBegin)?"SELECTED":""; ?>>Pay Period
					Beginning</option>
		</select>
		</td>
	</tr>
	<tr>
		<td class="form_field">Description:</td>
		<td class="form_field"><textarea name="addLeavesDescription"><?php echo $selectedDescription; ?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan=2 class="form_field">
			<center>
				<input class="ui-state-default ui-corner-all" type="submit" name="addLeaveHours" value="Add Leave Hours">
			</center>
		</td>
	</tr>
</table>
