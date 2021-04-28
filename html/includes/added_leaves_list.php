<?php
/**
 * UI added_leaves_list.php
 * Draws a table containing added leaves.
 * It is filtered by the input boxes from the file add_leaves.php
 *
 * @author Nevo Band
 */
$selectedYearTypeIdView = 0;
$selectedYearIdView = 0;
$selectedPayPeriodIdView = 0;

$helperClass = new Helper($sqlDataBase);

if(isset($_POST['yearTypeView']))
{

	if($_POST['yearTypeView']>0)
	{
		$selectedYearTypeIdView = $_POST['yearTypeView'];

		if($_POST['yearView']>0)
		{
			$selectedYearIdView = $_POST['yearView'];
			if($_POST['payPeriodView']>0)
			{
				$selectedPayPeriodIdView = $_POST['payPeriodView'];
			}
		}
	}
}

if(isset($_POST['userToViewAddedLeaves']))
{
	$selectedUserToView = $_POST['userToViewAddedLeaves'];
}
else
{
	$selectedUserToView = $loggedUser->getUserId();
}

if(isset($_POST['leaveTypeIdView']))
{
	$selectedLeaveTypeIdView = $_POST['leaveTypeIdView'];
}
else
{
	$selectedLeaveTypeIdView = 0;
}


$leavesAddedGlobal = $helperClass->get_leaves_added_global($selectedYearIdView, $selectedPayPeriodIdView, $selectedLeaveTypeIdView, $selectedYearIdView);

?>

<div id="addleaves_tabs">
	<ul>
		<li><a
			onclick="uncheckAll(document.addleaves_new.elements['addedLeavesCheckBox[]'],0)"
			href="#addleaves_new" style="font-size: 12px">Leaves Templates</a>
		</li>
		<li><a
			onclick="uncheckAll(document.addleaves_user.elements['addedLeavesCheckBox[]'],0)"
			href="#addleaves_user" style="font-size: 12px">User Added Leaves</a>
		</li>
	</ul>
	<form action="index.php?view=adminAddLeaves#addleaves_new"
		method="POST" name="addleaves_new" id="addleaves_new">
		<div id="addleaves_new">

			<table class="form_field">
				<tr>
					<td colspan=2 class="col_title">Add To User</td>
				</tr>
				<tr>
					<td class="form_field">User:</td>
					<td class="form_field"><SELECT name="userToViewAddedLeaves"
						onchange="this.form.submit()">
							<option value=0>All</option>
							<?php
 
                                                        $user = new User($sqlDataBase);
                                                        $usersToViewAddedLeaves = $user->GetAllUsers("netid");

							foreach($usersToViewAddedLeaves as $id=>$userInfo)
							{
								echo "<option value=".$userInfo['user_id'];
								if($userInfo['user_id']==$selectedUserToView)
								{
									echo " SELECTED";
								}
								echo ">".$userInfo['netid']."</option>";
							}
							?>
					</SELECT>
					</td>
				</tr>
				<tr>
					<td colspan=2 class="col_title">Filter Leaves</td>
				</tr>
				<tr>
					<td class="form_field">Year Type:</td>
					<td class="form_field"><select name="yearTypeView"
						onchange="this.form.submit()">
							<option value=0>All</option>
							<?php

                                                        $yearTypesInfo = Years::GetYearTypes($sqlDataBase);
							if($yearTypesInfo)
							{
								foreach($yearTypesInfo as $id=>$yearTypeInfo)
								{
									echo "<option value=".$yearTypeInfo['year_type_id'];
									if($selectedYearTypeIdView==$yearTypeInfo['year_type_id'])
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
					<td class="form_field"><select name="yearView"
						onchange="this.form.submit()">
							<option value=0>All</option>
							<?php

                                                        $yearsInfo = Years::GetYears($sqlDataBase, $selectedYearTypeIdView);
                                                        print_r($yearsInfo);
							if($yearsInfo)
							{
								foreach($yearsInfo as $yearInfo)
								{
									echo "<option value=".$yearInfo->getId();
									if($selectedYearIdView==$yearInfo->getId())
									{
										echo " SELECTED";
									}
									echo ">".Date('M Y',strtotime($yearInfo->getStartDate()))." - ".Date('M Y',strtotime($yearInfo->getEndDate())) ."</option>";
								}
							}
							?>
					</select>
					</td>
				</tr>
				<tr>
					<td class="form_field">Pay Period:</td>
					<td class="form_field"><select name="payPeriodView"
						onchange="this.form.submit()">
							<option value=0>All</option>
							<?php

                                                        $payPeriod = $helperClass->GetPayPeriods($selectedYearIdView);
							if($payPeriod)
							{
								foreach($payPeriod as $id=>$payPeriodInfo)
								{
									echo "<option value=".$payPeriodInfo['pay_period_id'];
									if($selectedPayPeriodIdView == $payPeriodInfo['pay_period_id'])
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
					<td class="form_field">Leave Type:</td>
					<td class="form_field"><select name="leaveTypeIdView"
						onchange="this.form.submit()">
							<option value=0>All</option>
							<?php

                                                        $leaveTypes = LeaveType::GetLeaveTypes($sqlDataBase, $selectedYearTypeIdView);
							if($leaveTypes)
							{
								foreach($leaveTypes as $leaveType)
								{
									echo "<option value=".$leaveType->getTypeId();
									if($selectedLeaveTypeIdView == $leaveType->getTypeId())
									{
										echo " SELECTED";
									}
									echo ">".$leaveType->getName()."</option>";
								}
							}
							?>
					</select>
					</td>
				</tr>
			</table>
			<br> <br> <b>Leave Templates: <img title="<b>Leave Templates:</b>
							<br>Check the leave templates to add to the selected user.
							<br>Templates are best used when adding leaves to a new user, they prevent the need to create the leaves manually all over again.
							" src="css/images/question.png"></b> 
			<br> 
				<input type="submit" class="ui-state-default ui-corner-all"
				name="deleteSelectedAddedLeaves" value="Delete Checked"> <input class="ui-state-default ui-corner-all"
				type="button" value="Check All"
				onclick="checkByParent('addedLeavesListGlobal',true)"> <input class="ui-state-default ui-corner-all"
				type="button" value="Uncheck All"
				onclick="uncheckAll(document.addleaves_new.elements['addedLeavesCheckBox[]'],0)">
				<input class="ui-state-default ui-corner-all" type="submit" name="addNewLeavesToUser" value="Add Selected To User">
				
			<div id="addedLeavesListGlobal">
				<table class="hover_table" id="template_added_leaves">
					<thead>
					<tr>
						<td></td>
						<th>Year</th>
						<th>Pay Period</th>
						<th>Hours</td>
						<th>Leave Type</th>
						<th>Pay Period</th>
						<td>Description</td>
					</tr>
					</thead>
					<tbody>
					<?php
					if(isset($leavesAddedGlobal))
					{
						foreach($leavesAddedGlobal as $id=>$leaveAddedGlobal)
						{
							echo "<tr>
                        <td><input type=\"checkbox\" value=".$leaveAddedGlobal['added_hours_id']." name=\"addedLeavesCheckBox[]\"></td>
			<td>".Date("Y",strtotime($leaveAddedGlobal['start_date']))
							.((Date("Y",strtotime($leaveAddedGlobal['start_date']))==(Date("Y",strtotime($leaveAddedGlobal['end_date']))))?"":"-".Date("Y",strtotime($leaveAddedGlobal['end_date'])))."</td>
                        <td>".Date("n/j",strtotime($leaveAddedGlobal['start_date']))." - ".Date("n/j",strtotime($leaveAddedGlobal['end_date']))."</td>
                        <td>".$leaveAddedGlobal['hours']."</td><td>".$leaveAddedGlobal['name']."</td>
                        <td>".(($leaveAddedGlobal['begining_of_pay_period'])?"Begining":"Ending")."</td>
                        <td>".$leaveAddedGlobal['description']."</td>
                      </tr>";
						}
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</form>

	<?php

        $leavesAdded = Leave::GetUserHours($sqlDataBase,
                $selectedUserToView,
                $selectedYearIdView,
                $selectedPayPeriodIdView,
                $selectedLeaveTypeIdView,
                $selectedYearTypeIdView);

	?>
	<form action="index.php?view=adminAddLeaves#addleaves_user"
		method="POST" name="addleaves_user">
		<div id="addleaves_user">
			<table class="form_field">
				<tr>
					<td colspan=2 class="col_title">View Added Leaves</td>
				</tr>
				<tr>
					<td class="form_field">User:</td>
					<td class="form_field"><SELECT name="userToViewAddedLeaves"
						onchange="this.form.submit()">
							<option value=0>All</option>
							<?php

                                                        $u = new User($sqlDataBase);
                                                        $usersToViewAddedLeaves = $u->GetAllUsers("netid");
							foreach($usersToViewAddedLeaves as $id=>$userInfo)
							{
								echo "<option value=".$userInfo['user_id'];
								if($userInfo['user_id']==$selectedUserToView)
								{
									echo " SELECTED";
								}
								echo ">".$userInfo['netid']."</option>";
							}
							?>
					</SELECT>
					</td>
				</tr>
				<tr>
					<td class="form_field">Year Type:</td>
					<td class="form_field"><select name="yearTypeView"
						onchange="this.form.submit()">
							<option value=0>All</option>
							<?php

                                                        $yearTypesInfo = Years::GetYearTypes($sqlDataBase);

							if($yearTypesInfo)
							{
								foreach($yearTypesInfo as $id=>$yearTypeInfo)
								{
									echo "<option value=".$yearTypeInfo['year_type_id'];
									if($selectedYearTypeIdView==$yearTypeInfo['year_type_id'])
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
					<td class="form_field"><select name="yearView"
						onchange="this.form.submit()">
							<option value=0>All</option>
							<?php

                                                        $yearsInfo = Years::GetYears($sqlDataBase, $selectedYearTypeIdView);
							if($yearsInfo)
							{
								foreach($yearsInfo as $yearInfo)
								{
									echo "<option value=".$yearInfo->getId();
									if($selectedYearIdView==$yearInfo->getId())
									{
										echo " SELECTED";
									}
									echo ">".Date('M Y',strtotime($yearInfo->getStartDate()))." - ".Date('M Y',strtotime($yearInfo->getEndDate())) ."</option>";
								}
							}
							?>
					</select>
					</td>
				</tr>
				<tr>
					<td class="form_field">Pay Period:</td>
					<td class="form_field"><select name="payPeriodView"
						onchange="this.form.submit()">
							<option value=0>All</option>
							<?php

                                                        $payPeriods = $helperClass->GetPayPeriods($selectedYearIdView);
							if($payPeriods)
							{
								foreach($payPeriods as $id=>$payPeriodInfo)
								{
									echo "<option value=".$payPeriodInfo['pay_period_id'];
									if($selectedPayPeriodIdView == $payPeriodInfo['pay_period_id'])
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
					<td class="form_field">Leave Type:</td>
					<td class="form_field"><select name="leaveTypeIdView"
						onchange="this.form.submit()">
							<option value=0>All</option>
							<?php

                                                        $leaveTypes = LeaveType::GetLeaveTypes($sqlDataBase, $selectedYearIdView);
							if($leaveTypes)
							{
								foreach($leaveTypes as $leaveType)
								{
									echo "<option value=".$leaveType->getTypeId();
									if($selectedLeaveTypeIdView == $leaveType->getTypeId())
									{
										echo " SELECTED";
									}
									echo ">".$leaveType->getName()."</option>";
								}
							}
							?>
					</select>
					</td>
				</tr>
			</table>
			<br> <br> <b>User Leaves:</b> <br> <input class="ui-state-default ui-corner-all" type="submit"
				name="deleteSelectedAddedLeaves" value="Delete Checked"> <input class="ui-state-default ui-corner-all"
				type="button" value="Check All"
				onclick="checkByParent('addedLeavesList',true)"> <input class="ui-state-default ui-corner-all"
				type="button" value="Uncheck All"
				onclick="uncheckAll(document.addleaves_user.elements['addedLeavesCheckBox[]'],0);">
			<div id="addedLeavesList">
				<table class="hover_table" id="user_added_leaves">
					<thead>
					<tr>
						<td></td>
						<th>Netid</th>
						<th>Year</th>
						<th>Pay Period</th>
						<th>Hours</th>
						<th>Leave Type</th>
						<th>Pay Period</th>
						<td>Description</td>
					</tr>
					</thead>
					<tbody>
					<?php
					if(isset($leavesAdded))
					{
						foreach($leavesAdded as $id=>$leaveAdded)
						{
							echo "<tr>
			<td><input type=\"checkbox\" value=".$leaveAdded['added_hours_id']." name=\"addedLeavesCheckBox[]\"></td>
			<td>".$leaveAdded['netid']."</td>
			<td>".Date("Y",strtotime($leaveAdded['start_date']))
						.((Date("Y",strtotime($leaveAdded['start_date']))==(Date("Y",strtotime($leaveAdded['end_date']))))?"":"-".Date("Y",strtotime($leaveAdded['end_date'])))."</td>
			<td>".Date("n/j",strtotime($leaveAdded['start_date']))." - ".Date("n/j",strtotime($leaveAdded['end_date']))."</td>
			<td>".$leaveAdded['hours']."</td>
			<td>".$leaveAdded['name']."</td>
			<td>".(($leaveAdded['begining_of_pay_period'])?"Begining":"Ending")."</td>
			<td>".$leaveAdded['description']."</td>
		      </tr>";
					}
				}
				?>
				</tbody>
				</table>
			</div>
		</div>
	</form>
</div>
<br>
