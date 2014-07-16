<?php
/**
 * UI list_years.php
 * Creates a UI listing the years created and their types.
 * Also gives the option to lock a year from reservations
 * 
 * @author Nevo Band
 */
if(isset($_GET['lock']) && isset($_GET['id']))
{
	
	$queryUpdateLockStatus = "UPDATE year_info SET locked=".$_GET['lock']." WHERE year_info_id=".$_GET['id'];
	$sqlDataBase->nonSelectQuery($queryUpdateLockStatus);
}

$queryYearTypes = "SELECT name,description,year_type_id FROM year_type";
$yearTypes = $sqlDataBase->query($queryYearTypes);

foreach($yearTypes as $yearTypeInfoId=>$yearTypeInfo)
{
	echo "<b>".$yearTypeInfo['name']."</b>  -  ".$yearTypeInfo['description'];
	
	echo "<table class=\"hover_table\" id=\"".$yearTypeInfo['year_type_id']."_year_table\">
		<thead>
        <tr>
                <th>
                Start Date
                </th>
                <th>
                End Date
                </th>
                <th>
                Locked
                </th>
                <td>
                Options
                </td>
        </tr>
        </thead>
        <tbody>";
	$queryYearInfo = "SELECT year_info_id,start_date,end_date,locked FROM year_info WHERE year_type_id=".$yearTypeInfo['year_type_id']." ORDER BY start_date";
	$yearInfo = $sqlDataBase->query($queryYearInfo);
	foreach($yearInfo as $yearInfoId=>$yearInfo)
	{
		echo "<tr>
                        <td>".Date("n/d/Y",strtotime($yearInfo['start_date']))."</td>
                        <td>".Date("n/d/Y",strtotime($yearInfo['end_date']))."</td>
                        <td>".(($yearInfo['locked'])?"locked":"open")."</td>
                        <td><a href=\"index.php?view=adminYears&id=".$yearInfo['year_info_id']."&lock=".(($yearInfo['locked'])?"0":"1")."\">".(($yearInfo['locked'])?"Unlock":"Lock")."</a></td>
		      </tr>";
	}
	echo "</tbody></table>";
}

?>
