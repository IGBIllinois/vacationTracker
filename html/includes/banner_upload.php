<?php
include_once "../conf/config.php";
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$day = Date('d');
$month = Date('m');
$year = Date('Y');
$year_type = "Appointment";
$helperClass = new Helper($sqlDataBase);

if($debug) {
    echo "Test banner URL = $bannerUrl<BR>";
}
if(isset($_POST['update'])) {

    try {
    $mmdd = "";
    if(isset($_POST['pay_period'])) {
        $pay_period = $_POST['pay_period'];
        // Pay period 1 = 8/16-5/15
        if($_POST['pay_period'] == "1") {
            $mmdd = "5/15/";
        }
        else {
            $mmdd="8/15/";
        }

    }
    if(isset($_POST['year'])) {
        $year = $_POST['year'];
    }

    $date = $mmdd.$year;
    $today = date("m/d/Y");
    
    if(($bannerUrl == "https://webservices-dev.admin.uillinois.edu/employeeWS/employeeLeaveBalance") && ($date > $today)) {
        // set date to today, just for testing purposes.
        $date = $today;
    }
    $numRecords = $_POST['numRecords'];
    if(isset($_POST['update_all'])) {

    for($i=0; $i<$numRecords; $i++) {

        $uin=$_POST['uin'.$i];
        $sickHours = $_POST['sickHours'.$i];
        $vacHours = $_POST['vacHours'.$i];
         
        if($pay_period == 2) { // second pay period, update hours

           $takenSickHours = $_POST['takenSickHours'.$i];

            $takenVacHours = $_POST['takenVacHours'.$i];
            $takenNoncumulativeSickHours = $_POST['takenNoncumulativeSickHours'.$i];

            $sickHours += $takenSickHours;

            $vacHours += $takenVacHours;
        }
        $helperClass->apiUpdateUserHours($uin, $vacHours, $sickHours, $date);
        
    } 
    
    } else if (isset($_POST['update_selected'])) {

        for($i=0; $i<$numRecords; $i++) {
            
            if(isset($_POST['chk'.$i])) {

                $uin=$_POST['uin'.$i];

                $sickHours = $_POST['sickHours'.$i];
                $vacHours = $_POST['vacHours'.$i];

                if($pay_period == 2) { // second pay period, update hours
                    $takenSickHours = $_POST['takenSickHours'.$i];

                    $takenVacHours = $_POST['takenVacHours'.$i];

                    $sickHours += $takenSickHours;

                    $vacHours += $takenVacHours;

                    
                 }
                $helperClass->apiUpdateUserHours($uin, $vacHours, $sickHours, $date);
            }
    }
    }
    } catch(Exception $e) {
        echo($e);
    }  
    
}


if(isset($_GET['year'])) {
    $year = $_GET['year'];
}

$pay_period = 1;
if(isset($_GET['pay_period']) ) {
    $pay_period = $_GET['pay_period'];
    if($pay_period == 2) {
        // Pay period 2 = 5/16-8/15
        $day = 15;
        $month = 8;
    }
}

$years = new Years($sqlDataBase);
$queryYearTypes = "SELECT year_type_id,name,description FROM year_type";
$yearTypes = $sqlDataBase->get_query_result($queryYearTypes);
$appointment_year_id = $years->GetYearId($day,$month,$year,$yearTypes[0]['year_type_id']);

$yearId = $appointment_year_id;

$thisPayPeriodId = $years->GetPayPeriodId($day, $month, $year, $yearId);
$userLeavesHoursAvailable = new Rules($sqlDataBase);


?>

<?php
$pay_period_text = "8/16 - 5/15";
if($pay_period == 2) {
    $pay_period_text = "5/16 - 8/15";
}
?>

	<table class="content">
		<tr>
			<td class="page_title" width=300></td>
			<td class="page_title"><br>
			</td>
		</tr>
		<tr>
			<td valign="top">
			</td>
		</tr>
	</table>
<?php 
// List all users
if(!isset($_GET['user_id'])) {
    


echo("<h2 style='font-style:normal; text-align:left'>$year_type Year: " . $pay_period_text . " " . $year . "</h2>");
echo("<form action=#>"
        ."<input type=hidden name=view value=bannerUpload>".
        "    
            Pay Period: <select name='pay_period'><option value='1' ". (($pay_period==1) ? " SELECTED " : "").">8/16 - 5/15</option><option value='2' ". (($pay_period==2) ? " SELECTED " : "").">5/16 - 8/15</select>
            Year: <input type=string name='year' value='".$year."'><button type='submit'>Submit</button>
                </form>");

echo("<form action='#' method=POST name='update'>");
echo("<input type=hidden name=view value=bannerUpload>");
echo("<input type=hidden name=update value=update>");
echo("<input type=hidden name=pay_period value=$pay_period>");
echo("<input type=hidden name=year value=$year>");
?>
    <table class="hover_table" id="hover_table">
            <thead>
                    <tr>
                        <th></th>
                            <th>UIN</th>
                            <th>Last Name</th>
                            <th>First Name</th>
                            
                            
                            <?php

                                    echo("<th>Total Banner<BR>Vacation Hours</th>
                                            <th>Total Banner<BR>Sick Hours</th>
                                            <th>Banner Vacation<BR>Hours Taken</th>
                                            <th>Banner Sick<BR>Hours Taken</th>".
                                            "<th>Banner Non-cumulative<BR>Sick Hours Taken</th>" .
                                            "<th>Vacation Hours Taken".(($pay_period == 2) ? "<BR>(Hours will be added)" : "") ."</th>
                                            <th>Sick Hours Taken".(($pay_period == 2) ? "<BR>(Hours will be added)" : "") ."</th>".
                                            "<th>Edit</th>");

                                ?>
                            
                            
                    </tr>
            </thead>
            <tbody>
<?php


$queryUsers = "SELECT u.user_id, "
        . "u.first_name, "
        . "u.last_name, "
        . "u.netid, "
        . "ut.name as type, "
        . "up.name as perm, "
        . "u.enabled FROM users u, "
        . "user_type ut, "
        . "user_perm up "
        . "WHERE up.user_perm_id = u.user_perm_id "
        . "AND ut.user_type_id = u.user_type_id "
        . "AND enabled=1 "
        . "and banner_include=1 ORDER BY u.last_name ASC";

    $users = $sqlDataBase->get_query_result($queryUsers, null);

     if($users)
     {
         $i = 0;
         $numRecords = count($users);
         $errorMessages = "";
         echo("<input type='hidden' name='numRecords' value=".$numRecords.">");
             foreach($users as $id=>$userInfo)
             {
                 try{
                 $curr_user = new User($sqlDataBase);
                 $curr_user->LoadUser($userInfo['user_id']);

                 // See Helper->DrawLeaveHoursAvailable for hour calculation

                 if($pay_period == 1) {
                     // Pay period 1 = 8/16-5/15
                     $startDate = ($year - 1 ). "-08-16";
                     $endDate = $year. "-05-15";
                 } else {
                     // Pay period 2 = 5/16-8/15
                     $startDate = $year. "-05-16";
                     $endDate = $year. "-08-15";
                 }

                 $leavesAvailable = $userLeavesHoursAvailable->LoadUserYearUsageCalcPayPeriod($userInfo['user_id'],$yearId, $startDate, $endDate);

                 $uin = $curr_user->getUIN();

                 echo("<input type='hidden' name=uin".$i." value=".$uin.">");
                 $userXML= $helperClass->apiGetUserInfo($uin);

                 $xml = "";
                 try {
                 $xml = new SimpleXMLElement($userXML);

                 } catch(Exception $e) {

                     //echo("Error:");
                     //echo($e->getTraceAsString());
                     $errorMessages .= "<tr class=\"failed_row\"><td>Error: User ".$curr_user->getNetid(). " has no UIN assigned.</td></tr>";

                     continue;
                 }
                 $current_vacation_hours = 0;
                 $current_sick_hours = 0;
                 $current_floating_hours = 0;
                 $current_nonc_sick_hours = 0;
                 $total_vacation_hours = 0;
                 $total_sick_hours = 0;
                 $total_floating_hours = 0;
                 $total_nonc_sick_hours = 0;
                 $taken_vacation_hours = 0;
                 $taken_sick_hours = 0;
                 $taken_floating_hours = 0;
                 $taken_nonc_sick_hours = 0;
                 //print_r($xml);
                 if(!empty($xml)) {
                 foreach($xml->children() as $leave) {

                     $code = $leave->Leave[0]->ValidLeaveTitle[0]->Code;
                     if($code == "VACA") {
                         $current_vacation_hours = $leave->Leave[0]->BeginBalance;
                         $accrued_vacation_hours = $leave->Leave[0]->Accrued;

                         $taken_vacation_hours = $leave->Leave[0]->Taken;
                         //$total_vacation_hours = $current_vacation_hours + $accrued_vacation_hours;
                         $total_vacation_hours = $leave->Leave[0]->AvailableBalance;

                     } elseif($code == "SICK") {
                         $current_sick_hours = $leave->Leave[0]->BeginBalance;
                         $accrued_sick_hours = $leave->Leave[0]->Accrued;
                         $taken_sick_hours = $leave->Leave[0]->Taken;
                         //$total_sick_hours = $current_sick_hours + $accrued_sick_hours;
                         $total_sick_hours = $leave->Leave[0]->AvailableBalance;

                     }
                     elseif($code == "SICN") {
                         $current_nonc_sick_hours = $leave->Leave[0]->BeginBalance;
                         $accrued_nonc_sick_hours = $leave->Leave[0]->Accrued;
                         $taken_nonc_sick_hours = $leave->Leave[0]->Taken;
                         //$total_sick_hours = $current_sick_hours + $accrued_sick_hours;
                         $total_nonc_sick_hours = $leave->Leave[0]->AvailableBalance;
                     }
                     elseif($code == "FLHL") {
                         $current_floating_hours = $leave->Leave[0]->BeginBalance;
                         $accrued_floating_hours = $leave->Leave[0]->Accrued;
                         $taken_floating_hours = $leave->Leave[0]->Taken;
                         $total_floating_hours = $current_floating_hours + $accrued_floating_hours;
                     }
                 }

                 } else {
                     //echo("xml is null");
                 }

                     echo "<tr>
                         <td><input type=checkbox name=chk".$i." value=chk".$i."></td>
                             <td>".$uin."</td>
                             <td>".$curr_user->getFirstName()."</td>
                             <td>".$curr_user->getLastName()."</td>";

                                 echo ("<td>".$total_vacation_hours."</td>
                                 <td>".$total_sick_hours."</td>");
                                 echo("<td>".$taken_vacation_hours."</td>");
                                 echo("<td>".$taken_sick_hours."</td>");
                                 echo("<td>".$taken_nonc_sick_hours."</td>");

                                 echo("<td>".$leavesAvailable[1]['calc_used_hours']."</td>
                                 <td>".$leavesAvailable[2]['calc_used_hours']."</td>");

                                 echo("<input type='hidden' name=vacHours".$i." value=".$leavesAvailable[1]['calc_used_hours'].">");
                                 echo("<input type='hidden' name=sickHours".$i." value=".$leavesAvailable[2]['calc_used_hours'].">");
                                 echo("<input type='hidden' name=takenVacHours".$i." value=".$taken_vacation_hours.">");
                                 echo("<input type='hidden' name=takenSickHours".$i." value=".$taken_sick_hours.">");


                                 echo "<td><a href=\"index.php?view=bannerUpload&user_id=".$userInfo['user_id']."&pay_period=$pay_period&year_type=$year_type&year=$year\">Edit</a></td>";  

                     echo("</tr>");
                 } catch(Exception $e) {
                     echo($e->getTraceAsString());
                 }	
                     $i++;
             }
     }
echo("</table>");
if($errorMessages != "") {
    echo("<table>".$errorMessages."</table>");
}
echo("<input class='ui-state-default ui-corner-all' type='submit' name='update_all' value='Submit all data to Banner'>");
echo("<input class='ui-state-default ui-corner-all' type='submit'  name='update_selected' value='Submit selected data to Banner' >");
echo("</form>");
?>
</tbody>
</table>


    
<?php 

} else {
    // List single user for editing

$user_id = $_GET['user_id'];

echo("<U><h2 style='font-style:normal; text-align:left'> Edit user info </H2></U>");
echo("<h2 style='font-style:normal; text-align:left'>$year_type Year: " . $pay_period_text . " " . $year . "</h2>");

echo("<form action=#>"
        ."<input type=hidden name=view value=bannerUpload>"
        ."<input type=hidden name=user_id value='".$user_id."'>".

            "<BR>
            Pay Period: <select name='pay_period'><option value='1' ". 
                (($pay_period==1) ? " SELECTED " : "").
            ">8/16 - 5/15</option><option value='2' ". 
                (($pay_period==2) ? " SELECTED " : "").
            ">5/16 - 8/15</select>
                
            Year: <input type=string name='year' value='".$year."'><button type='submit'>Submit</button>
                </form>");

        echo("<form action='#' method=POST name='update'>");
        echo("<input type=hidden name=view value=bannerUpload>");
        echo("<input type=hidden name=update value=update>");
        echo("<input type=hidden name=pay_period value=$pay_period>");
        echo("<input type=hidden name=year value=$year>");
?>
        
    <table class="hover_table" id="hover_table">
        <thead>
            <tr>
                <th>UIN</th>
                <th>Last</th>
                <th>First</th>
                            
                            
                            
<?php

        echo("<th>Total Banner<BR>Vacation Hours</th>
                <th>Total Banner<BR>Sick Hours</th>
                <th>Banner Vacation<BR>Hours Taken</th>
                <th>Banner Sick<BR>Hours Taken</th>");
        echo("<th>Banner Non-cumulative Sick<BR>Hours Taken</th>");


        echo("<th>Vacation Hours Taken".(($pay_period == 2) ? "<BR>(Hours will be added)" : "") ."</th>
                <th>Sick Hours Taken".(($pay_period == 2) ? "<BR>(Hours will be added)" : "") ."</th>");


?>
                            
                            
                    </tr>
            </thead>
            <tbody>
<?php


$queryUser = "SELECT u.user_id, "
        . "u.first_name, "
        . "u.last_name, "
        . "u.netid, "
        . "ut.name as type, "
        . "up.name as perm, "
        . "u.enabled "
        . "FROM users u, "
        . "user_type ut,"
        . " user_perm up "
        . "WHERE "
        . "up.user_perm_id = u.user_perm_id "
        . "AND ut.user_type_id = u.user_type_id "
        . "AND enabled=1 and u.user_id= :user_id "
        . "ORDER BY u.netid ASC";

            $params = array("user_id"=>$user_id);
            echo("<input type='hidden' name='numRecords' value=1>");

            $userInfo = $sqlDatabase->get_query_result($queryUser, $params);
            $userInfo = $userInfo[0];
            $curr_user_id = $userInfo['user_id'];
            $curr_user = new User($sqlDataBase);
            $curr_user->LoadUser($curr_user_id);

            $errorMessages = "";
            if($userInfo)
            {
                    $i=0;

                        // See Helper->DrawLeaveHoursAvailable for hour calculation
                        if($pay_period == 1) {
                            // Pay period 1 = 8/16-5/15
                            $startDate = ($year - 1 ). "-08-16";
                            $endDate = $year. "-05-15";
                        } else {
                            // Pay period 2 = 5/16-8/15
                            $startDate = $year. "-05-16";
                            $endDate = $year. "-08-15";
                        }
                        $leavesAvailable = $userLeavesHoursAvailable->LoadUserYearUsageCalcPayPeriod($curr_user_id,$yearId, $startDate, $endDate);

                        $uin = $curr_user->getUIN();
                        echo("<input type='hidden' name=uin".$i." value=".$uin.">");
                        $userXML= $helperClass->apiGetUserInfo($uin);

                        try {
                        $xml = new SimpleXMLElement($userXML);
                        } catch(Exception $e) {
                            $errorMessages .= "<tr class=\"failed_row\"><td>Error: User ".$curr_user->getNetid(). " has no UIN assigned.</td></tr>";
                            //echo("Error:");
                            //echo($e->getTraceAsString());
                        }
                        $current_vacation_hours = 0;
                        $current_sick_hours = 0;
                        $current_floating_hours = 0;
                        $total_vacation_hours = 0;
                        $total_sick_hours = 0;
                        $total_floating_hours = 0;
                        $taken_vacation_hours = 0;
                        $taken_sick_hours = 0;
                        $taken_floating_hours = 0;

                        foreach($xml->children() as $leave) {

                            $code = $leave->Leave[0]->ValidLeaveTitle[0]->Code;
                            if($code == "VACA") {
                                 $current_vacation_hours = $leave->Leave[0]->BeginBalance;
                                $accrued_vacation_hours = $leave->Leave[0]->Accrued;

                                $taken_vacation_hours = $leave->Leave[0]->Taken;
                                //$total_vacation_hours = $current_vacation_hours + $accrued_vacation_hours;
                                $total_vacation_hours = $leave->Leave[0]->AvailableBalance;
                            } elseif($code == "SICK") {
                                $current_sick_hours = $leave->Leave[0]->BeginBalance;
                                $accrued_sick_hours = $leave->Leave[0]->Accrued;
                                $taken_sick_hours = $leave->Leave[0]->Taken;
                                //$total_sick_hours = $current_sick_hours + $accrued_sick_hours;
                                $total_sick_hours = $leave->Leave[0]->AvailableBalance;
                            } elseif($code == "SICN") {
                                $current_nonc_sick_hours = $leave->Leave[0]->BeginBalance;
                                $accrued_nonc_sick_hours = $leave->Leave[0]->Accrued;
                                $taken_nonc_sick_hours = $leave->Leave[0]->Taken;
                                //$total_sick_hours = $current_sick_hours + $accrued_sick_hours;
                                $total_nonc_sick_hours = $leave->Leave[0]->AvailableBalance;

                            } elseif($code == "FLHL") {
                                $current_floating_hours = $leave->Leave[0]->BeginBalance;
                                $accrued_floating_hours = $leave->Leave[0]->Accrued;
                                $taken_floating_hours = $leave->Leave[0]->Taken;
                                $total_floating_hours = $current_floating_hours + $accrued_floating_hours;
                            }

                        }

                            echo "<tr>
                                    <td>".$uin."</td>
                                    <td>".$curr_user->getFirstName()."</td>
                                    <td>".$curr_user->getLastName()."</td>";

                                        echo ("<td>".$total_vacation_hours."</td>
                                        <td>".$total_sick_hours."</td>");

                                        echo("<td>".$taken_vacation_hours."</td>");
                                        echo("<td>".$taken_sick_hours."</td>");
                                        echo("<td>".$taken_nonc_sick_hours."</td>");


                                        echo("<td><input type=text name='vacHours".$i."' value=".$leavesAvailable[1]['calc_used_hours']."></input></td>
                                        <td><input type=text name='sickHours".$i."' value=".$leavesAvailable[2]['calc_used_hours']."></input></td>");

                                        echo("<input type='hidden' name=takenVacHours".$i." value=".$taken_vacation_hours.">");
                                        echo("<input type='hidden' name=takenSickHours".$i." value=".$taken_sick_hours.">");


                                        echo("<input type='hidden' name=takenNoncumulativeSickHours".$i." value=".$taken_nonc_sick_hours.">");


                            echo("</tr>");
                    }
echo("</table>");
if($errorMessages != "") {
    echo("<table>".$errorMessages."</table>");
}
echo("<input class='ui-state-default ui-corner-all' type='submit'  name='update_all' value='Submit data to Banner' >");
                                                
echo("</form>");					
?>
</tbody>
</table>
<BR>

<?php

echo("<a href=\"index.php?view=bannerUpload&pay_period=$pay_period&year_type=$year_type&year=$year\">Back to Banner Upload</a>");

  
}
?>
