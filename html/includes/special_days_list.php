<?php
/**
 * UI special_days_list.php
 * Create UI list of special days (holidays to view on the calendar) this is used under "calendar settings" tab under administration.
 * 
 * @author Nevo Band
 */
if(isset($_GET['action']))
{
	if($_GET['action']=="del")
	{
		$specialDay = new SpecialDay($sqlDataBase);
		$specialDay->LoadSpecialDay($_GET['id']);
		$specialDay->Delete();
	}
}
?>

<table class="hover_table" id="hover_table">
<thead>
<tr>
	<th>
	Name
	</th>
	<th>
        Month
        </th>
	<th>
        Day
        </th>
	<th>
        Year
        </th>
	<th>
        Week Day
        </th>
	<th>
        Priority
        </th>
	<th>
        Blocked
        </th>
	<td>
        Color
        </td>	
	<td>
        Options
    </td>
</tr>
</thead>
<tbody>
<?php

$specialDays = SpecialDay::getDaysForUser($sqlDataBase, $editSpecialDay->getUserId());
if($specialDays)
{
	foreach($specialDays as $specialDay)
	{
		if($specialDay->getDayId()==$dayId)
		{
			$colStyle="selected_col";
		}
		else
		{
			$colStyle="";
		}
		
		echo "<tr><td class=\"".$colStyle."\">".$specialDay->getName()."</td>";
		echo "<td class=\"".$colStyle."\">".(($specialDay->getMonth()==0)?"All":Date("M",mktime(0,0,0,$specialDay->getMonth(),1,2010)))."</td>";
		echo "<td class=\"".$colStyle."\">".(($specialDay->getDay()==0)?"All":$specialDay->getDay().Date("S",mktime(0,0,0,8,$specialDay->getDay(),2010)))."</td>";
		echo "<td class=\"".$colStyle."\">".(($specialDay->getYear()==0)?"All":$specialDay->getYear())."</td>";
		echo "<td class=\"".$colStyle."\">".(($specialDay->getWeekDay()=="0")?"All":Date("l",mktime(0,0,0,8,$specialDay->getWeekDay(),2010)))."</td>";
		echo "<td class=\"".$colStyle."\">".$specialDay->getPriority()."</td>";
		echo "<td class=\"".$colStyle."\">".(($specialDay->getBlocked())?"blocked":"usable")."</td>";
		echo "<td class=\"".$colStyle."\"><center><div id=\"day_color_box\" style=\"background-color:#".$specialDay->getColor()."\"></div></center></td>";
		echo "<td class=\"".$colStyle."\"><a href=\"index.php?view=adminCalendar&id=".$specialDay->getDayId()."\">Edit</a> | <a href=\"index.php?view=adminCalendar&action=del&id=".$specialDay->getDayId()."\">Delete</a></td></tr>";
	}
}

?>
</tbody>
</table>

</form>
