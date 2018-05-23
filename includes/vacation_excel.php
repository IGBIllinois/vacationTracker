<?php

include_once '../classes/ExcelWriter.php';
include_once '../classes/SQLDataBase.php';
include_once '../classes/User.php';
include_once '../classes/Years.php';
include_once '../classes/Rules.php';
include_once "config.php";

//Initialize database
$sqlDataBase= new SQLDataBase('localhost',$sqlDataBase,$sqlUserName,$sqlPassword);

if(isset($_GET['excel'])) {
    $user_id = $_GET['user_id'];
    $status_id = $_GET['status_id'];
    $app_year_id = $_GET['app_year_id'];
    $fisc_year_id = $_GET['fisc_year_id'];
    $pay_period = $_GET['pay_period'];
    writeExcel($user_id, $status_id, $app_year_id, $fisc_year_id, $sqlDataBase, $pay_period);
    
}
//include_once "excel/ExcelWriter.php";
/* Writes and opens an Excel file
*
* @param search_results: An array from an sql query
*
*/

function writeExcel($user_id, $status_id, $appointment_year_id, $fiscal_year_id, $db, $pay_period = 1, $filename="vacation.xls") {
    
    $years = new Years($db);
           $appYearInfo = $years->GetYearDates($appointment_year_id);
           $fiscYearInfo = $years->GetYearDates($fiscal_year_id);
           
           $start_year = Date("Y",strtotime($appYearInfo[0]['start_date']));
           $end_year = Date("Y",strtotime($appYearInfo[0]['end_date']));
           
           $start_date = $start_year . "-08-15"; 
          $end_date = $end_year. "-05-15";
           //echo("curr Pay period = $curr_pay_period<BR>");
           if($pay_period == 2) {
               $start_date = $end_year . "-05-15";
               $end_date = $end_year . "-08-15";
               
           }
           
        $vacationLeaves = "SELECT li.leave_id, DATE_FORMAT(li.date,'%c-%e-%Y') as date, li.leave_hours, TIME_FORMAT(SEC_TO_TIME(li.time), '%kh %im') as time, li.description, lt.name, s.name as statusName, li.leave_type_id_special, lts.name as special_name
				FROM (leave_info li)
				JOIN leave_type lt ON li.leave_type_id=lt.leave_type_id
				JOIN status s ON li.status_id = s.status_id
				LEFT JOIN leave_type lts ON lts.leave_type_id = li.leave_type_id_special
				WHERE li.user_id =".$user_id." AND li.status_id=".$status_id." AND li.year_info_id=".$appointment_year_id."
                                AND lt.name = 'Vacation'
                                and date between '$start_date' and '$end_date' 
				ORDER BY li.date DESC";
    
    $sickLeaves = "SELECT li.leave_id, DATE_FORMAT(li.date,'%c-%e-%Y') as date, li.leave_hours, TIME_FORMAT(SEC_TO_TIME(li.time), '%kh %im') as time, li.description, lt.name, s.name as statusName, li.leave_type_id_special, lts.name as special_name
				FROM (leave_info li)
				JOIN leave_type lt ON li.leave_type_id=lt.leave_type_id
				JOIN status s ON li.status_id = s.status_id
				LEFT JOIN leave_type lts ON lts.leave_type_id = li.leave_type_id_special
				WHERE li.user_id =".$user_id." AND li.status_id=".$status_id." AND li.year_info_id=".$appointment_year_id."
                                AND lt.name = 'Sick'
                                and date between '$start_date' and '$end_date' 
				ORDER BY li.date DESC";
    
    $floatingLeaves = "SELECT li.leave_id, DATE_FORMAT(li.date,'%c-%e-%Y') as date, li.leave_hours, TIME_FORMAT(SEC_TO_TIME(li.time), '%kh %im') as time, li.description, lt.name, s.name as statusName, li.leave_type_id_special, lts.name as special_name
				FROM (leave_info li)
				JOIN leave_type lt ON li.leave_type_id=lt.leave_type_id
				JOIN status s ON li.status_id = s.status_id
				LEFT JOIN leave_type lts ON lts.leave_type_id = li.leave_type_id_special
				WHERE li.user_id =".$user_id." AND li.status_id=".$status_id." AND li.year_info_id=".$fiscal_year_id."
                                ORDER BY li.date DESC";
    
    //echo("queryLeaves = $queryLeaves");
    $vacation_results = $db->query($vacationLeaves);
    $sick_results = $db->query($sickLeaves);
    $floating_results = $db->query($floatingLeaves);
    
	//$filename = "./igb_people.xls";
    
    try {
        if(file_exists("../excel/".$filename)) {
            unlink("../excel/".$filename);
        }
	$excel= new ExcelWriter("../excel/".$filename);
        $user = new User($db);
	$user->LoadUser($user_id);
        $username = $user->getNetid();
        
        $total_vac = 0;
        $total_sick = 0;
        $total_float = 0;
        
        $excel->writeLine(array("<B>Vacation Leave for $username</B>"));
        $excel->writeLine(array($start_date . " - " . $end_date));
        $excel->writeLine(array());
        
	$myArr=array("<b>Date</b>","<b>Type</b>","<b>Special</b>","<b>Charge Time</b>","<b>Actual Time</b>","<b>Description</b>","<b>Status</b>");

	$excel->writeLine($myArr);
        
	for ($i = 0; $i < count($vacation_results); $i++) {
		//$thisuser = new user($dbase, $search_results[$i]['user_id']);
            /*
		$line = array($search_results[$i]['first_name'] . " " . $search_results[$i]['last_name'],
					  $search_results[$i]['email'],
					  $search_results[$i]['theme_name'],
					  $search_results[$i]['type_name'],
					  $search_results[$i]['igb_room'],
					  get_address($db, $search_results[$i]['user_id'], "HOME")

					  );
             * 
             */
            $line = array($vacation_results[$i]['date'],
                            $vacation_results[$i]['name'],
                            $vacation_results[$i]['special_name'],
                            $vacation_results[$i]['leave_hours'],
                            $vacation_results[$i]['time'],
                            $vacation_results[$i]['description'],
                            $vacation_results[$i]['statusName']
                    );
                  
		$excel->writeLine($line);
                $total_vac += $vacation_results[$i]['leave_hours'];
	}
 
        $excel->writeLine(array());
        $excel->writeLine(array("<B>Sick Leave</B>"));
        
	$myArr=array("<b>Date</b>","<b>Type</b>","<b>Special</b>","<b>Charge Time</b>","<b>Actual Time</b>","<b>Description</b>","<b>Status</b>");
	$excel->writeLine($myArr);
        //$excel->writeLine($queryLeaves);
	for ($i = 0; $i < count($sick_results); $i++) {
		//$thisuser = new user($dbase, $search_results[$i]['user_id']);
            /*
		$line = array($search_results[$i]['first_name'] . " " . $search_results[$i]['last_name'],
					  $search_results[$i]['email'],
					  $search_results[$i]['theme_name'],
					  $search_results[$i]['type_name'],
					  $search_results[$i]['igb_room'],
					  get_address($db, $search_results[$i]['user_id'], "HOME")

					  );
             * 
             */
            $line = array($sick_results[$i]['date'],
                            $sick_results[$i]['name'],
                            $sick_results[$i]['special_name'],
                            $sick_results[$i]['leave_hours'],
                            $sick_results[$i]['time'],
                            $sick_results[$i]['description'],
                            $sick_results[$i]['statusName']
                    );
                $total_sick += $sick_results[$i]['leave_hours'];
		$excel->writeLine($line);
	}
        
        $excel->writeLine(array());
        $excel->writeLine(array("<B>Floating Holidays</B>"));
        
	$myArr=array("<b>Date</b>","<b>Type</b>","<b>Special</b>","<b>Charge Time</b>","<b>Actual Time</b>","<b>Description</b>","<b>Status</b>");
	$excel->writeLine($myArr);
        //$excel->writeLine($queryLeaves);
	for ($i = 0; $i < count($floating_results); $i++) {
		//$thisuser = new user($dbase, $search_results[$i]['user_id']);
            /*
		$line = array($search_results[$i]['first_name'] . " " . $search_results[$i]['last_name'],
					  $search_results[$i]['email'],
					  $search_results[$i]['theme_name'],
					  $search_results[$i]['type_name'],
					  $search_results[$i]['igb_room'],
					  get_address($db, $search_results[$i]['user_id'], "HOME")

					  );
             * 
             */
            $line = array($floating_results[$i]['date'],
                            $floating_results[$i]['name'],
                            $floating_results[$i]['special_name'],
                            $floating_results[$i]['leave_hours'],
                            $floating_results[$i]['time'],
                            $floating_results[$i]['description'],
                            $floating_results[$i]['statusName']
                    );
                $total_float += $floating_results[$i]['leave_hours'];
		$excel->writeLine($line);
	}
         
        $excel->writeLine(array());
        $excel->writeLine(array("<B>Total Vacation Hours:</B>", "<B>".$total_vac."</B>"));
 
        $excel->writeLine(array("<B>Total Sick Hours:</B>", "<B>".$total_sick."</B>"));

        $excel->writeLine(array("<B>Total Floating Holiday Hours:</B>", "<B>".$total_float."</B>"));
        
        
        if($pay_period == 2) {
            //write yearly totals
            $userLeavesHoursAvailable = new Rules($db);
            $leavesAvailable = $userLeavesHoursAvailable->LoadUserYearUsageCalc($user_id,$appointment_year_id);
            
            $totalVacHours = round(($leavesAvailable[1]['initial_hours']+$leavesAvailable[1]['added_hours']-$leaveAvailable[1]['calc_used_hours']),2);
            $estimatedVacHours = round(($leavesAvailable[1]['initial_hours']+$leavesAvailable[1]['est_added_hours']-$leavesAvailable[1]['calc_used_hours']),2);
            
            $totalSickHours = round(($leavesAvailable[2]['initial_hours']+$leavesAvailable[2]['added_hours']-$leaveAvailable[2]['calc_used_hours']),2);
            $estimatedSickHours = round(($leavesAvailable[2]['initial_hours']+$leavesAvailable[2]['est_added_hours']-$leavesAvailable[2]['calc_used_hours']),2);
            
            $excel->writeLine(array());
            $total_vac_hours = $leavesAvailable[1]['calc_used_hours'];
            $total_sick_hours = $leavesAvailable[2]['calc_used_hours'];
            
            $excel->writeLine(array("<B>Yearly Total Vacation Hours Taken:</B>", "<B>".round($leavesAvailable[1]['calc_used_hours'],2)."</B>"));
            $excel->writeLine(array("<B>Yearly Total Sick Hours Taken:</B>", "<B>".round($leavesAvailable[2]['calc_used_hours'],2)."</B>"));
            
            $excel->writeLine(array("<B>Vacation Hours Available:</B>", "<B>".$estimatedVacHours."</B>"));
            $excel->writeLine(array("<B>Sick Hours Available:</B>", "<B>".$estimatedSickHours."</B>"));
        }
	$excel->close();
        
        //header("Content-type: application/xls");
	//header("Content-Disposition: attachment; filename=excel/". $filename. "')");
        //header("Location: excel/". $filename);

        //echo("Location = ". "../excel/". $filename);
        header("Location: ../excel/". $filename);
        exit();
    } catch(Exception $e) {
        echo($e);
        echo($e->getTrace());
    }
	
}

?>