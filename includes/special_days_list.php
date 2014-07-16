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

$querySpecialDays = "SELECT description,color,blocked,month,day,year,week_day,name,id,priority FROM calendar_special_days WHERE user_id=".$editSpecialDay->getUserId();

$specialDays = $sqlDataBase->query($querySpecialDays);
if($specialDays)
{
	foreach($specialDays as $id=>$specialDay)
	{
		if($specialDay['id']==$dayId)
		{
			$colStyle="selected_col";
		}
		else
		{
			$colStyle="";
		}
		
		echo "<tr><td class=\"".$colStyle."\">".$specialDay['name']."</td>";
		echo "<td class=\"".$colStyle."\">".(($specialDay['month']==0)?"All":Date("M",mktime(0,0,0,$specialDay['month'],1,2010)))."</td>";
		echo "<td class=\"".$colStyle."\">".(($specialDay['day']==0)?"All":$specialDay['day'].Date("S",mktime(0,0,0,8,$specialDay['day'],2010)))."</td>";
		echo "<td class=\"".$colStyle."\">".(($specialDay['year']==0)?"All":$specialDay['year'])."</td>";
		echo "<td class=\"".$colStyle."\">".(($specialDay['week_day']=="0")?"All":Date("l",mktime(0,0,0,8,$specialDay['week_day'],2010)))."</td>";
		echo "<td class=\"".$colStyle."\">".$specialDay['priority']."</td>";
		echo "<td class=\"".$colStyle."\">".(($specialDay['blocked'])?"blocked":"usable")."</td>";
		echo "<td class=\"".$colStyle."\"><center><div id=\"day_color_box\" style=\"background-color:#".$specialDay['color']."\"></div></center></td>";
		echo "<td class=\"".$colStyle."\"><a href=\"index.php?view=adminCalendar&id=".$specialDay['id']."\">Edit</a> | <a href=\"index.php?view=adminCalendar&action=del&id=".$specialDay['id']."\">Delete</a></td></tr>";
	}
}

?>
</tbody>
</table>

</form>
