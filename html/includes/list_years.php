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
	
	$queryUpdateLockStatus = "UPDATE year_info SET locked= :locked WHERE year_info_id= :year_info_id";
	$params = array("locked"=>$_GET['lock'],
                        "year_info_id"=>$_GET['id']);
        $sqlDataBase->get_update_result($queryUpdateLockStatus, $params);
}

$queryYearTypes = "SELECT name,description,year_type_id FROM year_type";
$yearTypes = $sqlDataBase->get_query_result($queryYearTypes);

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
	$queryYearInfo = "SELECT year_info_id,"
                . "start_date,"
                . "end_date,"
                . "locked "
                . "FROM year_info "
                . "WHERE year_type_id=:year_type_id"
                . " ORDER BY start_date";
        $params = array("year_type_id"=>$yearTypeInfo['year_type_id']);
        
	$yearInfo = $sqlDataBase->get_query_result($queryYearInfo, $params);
	foreach($yearInfo as $yearInfoId=>$yearInfo)
	{
		echo "<tr>
                        <td>".Date("n/d/Y",strtotime($yearInfo['start_date']))."</td>
                        <td>".Date("n/d/Y",strtotime($yearInfo['end_date']))."</td>
                        <td>".(($yearInfo['locked'])?"locked":"open")."</td>
                        <td><a href=\"index.php?view=adminYears&id=".$yearInfo['year_info_id'].
                        "&lock=".(($yearInfo['locked'])?"0":"1")."\">".
                                (($yearInfo['locked'])?"Unlock":"Lock")."</a></td>
		      </tr>";
	}
	echo "</tbody></table>";
}

?>
