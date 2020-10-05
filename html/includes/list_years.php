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
    
	$lock = $_GET['lock'];
        $year = new Years($sqlDataBase, $_GET['id']);
        
        $year->UpdateLock($lock);

}


$yearTypes = Years::GetYearTypes($sqlDataBase);

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
                <td style=\"width:20%\">
                Rollover Hours
                </td>
        </tr>
        </thead>
        <tbody>";

        $yearInfo = Years::GetYears($sqlDataBase, $yearTypeInfo['year_type_id']);

	foreach($yearInfo as $year)
	{
            $startDate = Date("n/d/Y",strtotime($year->getStartDate()));
            $endDate = Date("n/d/Y",strtotime($year->getEndDate()));
            $locked = $year->getLocked();
            
		echo "<tr>
                        <td>".$startDate."</td>
                        <td>".$endDate."</td>
                        <td>".(($locked)?"locked":"open")."</td>
                        <td><a href=\"index.php?view=adminYears&id=".$year->getId().
                        "&lock=".(($locked)?"0":"1")."\">".
                                (($locked)?"Unlock":"Lock")."</a></td>
                        <td>"."<a href=\"index.php?view=adminYears&max_rollover=1&id=".$year->getId()."\">View rollover hours</a></td>           
		      </tr>";
	}
	echo "</tbody></table>";
}

?>
