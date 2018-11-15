<?php
/**
 * UI create_year.php
 * Creates a UI with input boxes to create a Year or a Year Type.
 * This is used in the "Years" tab on the administration section.
 * 
 * @author Nevo Band
 */
if(isset($_POST['createYearType']))
{
	list($monthStart,$dayStart,$yearStart) = explode("/",$_POST['dateStart']);
	$dateStart = $yearStart."-".$monthStart."-".$dayStart;

	list($monthEnd,$dayEnd,$yearEnd) = explode("/",$_POST['dateEnd']);
        $dateEnd = $yearEnd."-".$monthEnd."-".$dayEnd;

	if($years->CreateYearType($dateStart,$dateEnd,$_POST['yearTypeName'],$_POST['yearTypeDescription'],$_POST['numPeriods']))
	{
		echo "success";
	}
	else
	{
		echo "failed";
	}
}

if(isset($_POST['addYear']))
{
	if($_POST['addYearTo']=="new")
	{
		echo $years->CreateYear(0,$years->GetLastYearId($_POST['yearTypeToAddTo']),$_POST['yearTypeToAddTo'],0);
	}	
	elseif($_POST['addYearTo']=="old")
        {
               echo $years->CreateYear($years->GetFirstYearId($_POST['yearTypeToAddTo']),0,$_POST['yearTypeToAddTo'],0);
        }
	else
	{
		echo "error no year to add to";
	}
}

?>
<form name="create_year_info" action="index.php?view=adminYears" method="post">
<table width="100%">
	<tr>
		<td colspan=2 class="col_title">
		Create Year Type
		</td>
	</tr>
	<tr>
                <td >
                Name:
                </td>
                <td>
                <input type="text" name="yearTypeName">
                </td>
        </tr>
	<tr>
                <td >
                Description:
                </td>
                <td>
                <input type="text" name="yearTypeDescription">
                </td>
        </tr>
	<tr>
		<td >
		Date Start:
		</td>
		<td>
		<input type="text" id="datepickerStart" name="dateStart">	
		</td>
	</tr>
	<tr>

		<td valign="top">
		Date End:
		</td>
		<td >
		<input type="text" id="datepickerEnd" name="dateEnd">
		</td>
	</tr>
	<tr>

                <td valign="top">
               	Period every:
                </td>
                <td>
                <SELECT name="numPeriods">
		<option value=12>1 Month</option>
		<option value=6>2 Months</option>
		<option value=4>3 Months</option>
		<option value=3>4 Months</option>
		<option value=2>6 Months</option>
		<option value=1>12 Months</option>
		</SELECT>
                </td>
        </tr>
	<tr>
		<td colspan=2 >
		<center><input class="ui-state-default ui-corner-all" type="submit" value="Create Year Type" name="createYearType"></center>
		</td>

	</tr>
	<tr>
                <td colspan=2 class="col_title">
                Add Year
                </td>
        </tr>
        <tr>
                <td >
                Year Type:
                </td>
                <td>
                <SELECT name="yearTypeToAddTo">
		<?php
		$queryYearTypes = "SELECT name,year_type_id FROM year_type";
		$yearTypes = $sqlDataBase->query($queryYearTypes);
		
		foreach($yearTypes as $id=>$yearTypeInfo)
		{
			echo "<option value=".$yearTypeInfo['year_type_id'].">".$yearTypeInfo['name']."</option>";
		}
		
		?>
		</SELECT>
                </td>
        </tr>
        <tr>

                <td valign="top">
                Add:
                </td>
                <td >
               	<SELECT name="addYearTo">
		<option value="new">Next Year</option>
		<option value="old">Previous Year</option>
                </td>
        </tr>
        <tr>
                <td colspan=2 >
                <center><input class="ui-state-default ui-corner-all" type="submit" value="Add Year" name="addYear"></center>
                </td>

        </tr>
</table>
</FORM>
