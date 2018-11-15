<?php

//include_once "main.inc.php";
function my_autoloader($class_name) {
	require_once '../classes/' . $class_name . '.php';
}
spl_autoload_register('my_autoloader');


require_once '../vendor/autoload.php';

//Load configuration file
require_once "config.php";
$sqlDataBase= new SQLDataBase('localhost',$sqlDataBase,$sqlUserName,$sqlPassword);

$type = "xlsx";
$filename = "vacation";
if (isset($_GET['vacation_report'])) {
    
    $filename = "vacation_report";
    
    $user_id = $_GET['user_id'];
    $status_id = $_GET['status_id'];
    $app_year_id = $_GET['app_year_id'];
    $fisc_year_id = $_GET['fisc_year_id'];
    $pay_period = $_GET['pay_period'];
    
    // Totals
    $total_vac = 0;
    $total_sick = 0;
    $total_float = 0;
    
    $user = new User($sqlDataBase);
    $user->LoadUser($user_id);
    $user_fullname = $user->getFirstName() . " ". $user->getLastName();
    $username = $user->getNetid();
    
    $vacation_leaves = $user->GetVacationLeaves($app_year_id, $pay_period, $status_id);
    $sick_leaves = $user->GetSickLeaves($app_year_id, $pay_period, $status_id);
    $floating_leaves = $user->GetFloatingHolidays($fisc_year_id, $pay_period, $status_id);
    
    $years = new Years($sqlDataBase);
    $appYearInfo = $years->GetYearDates($app_year_id);
    $fiscYearInfo = $years->GetYearDates($fisc_year_id);
                   
    $appYearInfo = $years->GetYearDates($app_year_id);
    $fiscYearInfo = $years->GetYearDates($fisc_year_id);

    $start_year = Date("Y",strtotime($appYearInfo[0]['start_date']));
    $end_year = Date("Y",strtotime($appYearInfo[0]['end_date']));

    $start_date = $start_year . "-08-15"; 
    $end_date = $end_year. "-05-15";

    if($pay_period == 2) {
        $start_date = $end_year . "-05-15";
        $end_date = $end_year . "-08-15";

    }
           
    $data = array();
    
    $data[] = (array("Vacation Leave for $user_fullname\n($username)"));
    $data[] = (array($start_date . " - " . $end_date));
    $data[] = (array());
        
    $data[] = array("<B>", "Date","Type","Special","Charge Time","Actual Time","Description","Status");
    
    foreach($vacation_leaves as $leave) {
        $leaveType = new LeaveType($sqlDataBase);
        $leaveType->LoadLeaveType($leave->getLeaveTypeId());
        $data[] = array(
                $leave->GetDate(),
                $leaveType->getName(),
                $leaveType->getSpecial(),
                $leave->GetHours(),
                gmdate('g\h i\m',$leave->GetTime()),
                $leave->getDescription(),
                $leave->GetStatusString());
        
        $total_vac += $leave->GetHours();
    }
    
    $data[] = array();
    // Sick Leave
    $data[] = (array("<B>","Sick Leave"));

        
    $data[] = array("<B>","Date","Type","Special","Charge Time","Actual Time","Description","Status");
    
    foreach($sick_leaves as $leave) {
        $leaveType = new LeaveType($sqlDataBase);
        $leaveType->LoadLeaveType($leave->getLeaveTypeId());
        $data[] = array(
                $leave->GetDate(),
                $leaveType->getName(),
                $leaveType->getSpecial(),
                $leave->GetHours(),
                gmdate('g\h i\m',$leave->GetTime()),
                $leave->getDescription(),
                $leave->GetStatusString());
        
        $total_sick += $leave->GetHours();
    }
    
    $data[] = (array());
    
    // Floating Holidays
    $data[] = (array("<B>","Floating Holidays"));
        
    $data[] = array("<B>","Date","Type","Special","Charge Time","Actual Time","Description","Status");
    
    foreach($floating_leaves as $leave) {
        $leaveType = new LeaveType($sqlDataBase);
        $leaveType->LoadLeaveType($leave->getLeaveTypeId());
        $data[] = array(
                $leave->GetDate(),
                $leaveType->getName(),
                $leaveType->getSpecial(),
                $leave->GetHours(),
                gmdate('g\h i\m',$leave->GetTime()),
                $leave->getDescription(),
                $leave->GetStatusString());
        
        $total_float += $leave->GetHours();
    }
    
    // Totals:
        $data[] = (array());
        $data[] = (array("<B>","Total Vacation Hours:", $total_vac));
 
        $data[] = (array("<B>","Total Sick Hours:", $total_sick));

        $data[] = (array("<B>","Total Floating Holiday Hours:", $total_float));
    
        if($pay_period == 2) {
            //write yearly totals
            $userLeavesHoursAvailable = new Rules($sqlDataBase);
            $leavesAvailable = $userLeavesHoursAvailable->LoadUserYearUsageCalc($user_id,$app_year_id);
            
            $totalVacHours = round(($leavesAvailable[1]['initial_hours']+$leavesAvailable[1]['added_hours']-$leavesAvailable[1]['calc_used_hours']),2);
            $estimatedVacHours = round(($leavesAvailable[1]['initial_hours']+$leavesAvailable[1]['est_added_hours']-$leavesAvailable[1]['calc_used_hours']),2);
            
            $totalSickHours = round(($leavesAvailable[2]['initial_hours']+$leavesAvailable[2]['added_hours']-$leavesAvailable[2]['calc_used_hours']),2);
            $estimatedSickHours = round(($leavesAvailable[2]['initial_hours']+$leavesAvailable[2]['est_added_hours']-$leavesAvailable[2]['calc_used_hours']),2);
            
            $data[] = (array());
            $total_vac_hours = $leavesAvailable[1]['calc_used_hours'];
            $total_sick_hours = $leavesAvailable[2]['calc_used_hours'];
            
            $data[] = (array("<B>","Yearly Total Vacation Hours Taken:", round($leavesAvailable[1]['calc_used_hours'],2)));
            $data[] = (array("<B>","Yearly Total Sick Hours Taken:", round($leavesAvailable[2]['calc_used_hours'],2)));
            
            $data[] = (array("<B>","Vacation Hours Available:", $estimatedVacHours));
            $data[] = (array("<B>","Sick Hours Available:", $estimatedSickHours));
        }        
}

switch ($type) {
    
	case 'csv':
		Report::create_csv_report($data,$filename);
		break;
	case 'xls':
	      	Report::create_excel_2003_report($data,$filename);
                break;
	case 'xlsx':
		Report::create_excel_2007_report($data,$filename);
		break;
}



?>