<?php

/* 
 * Creates a PHP page in .ics format, which can be read by Outlook
 * This one contains vacation data from the CNRG members
 */
require_once "includes/main.inc.php";
require_once "../conf/config.php";

header('Content-type: text/calendar; charset=utf-8');
header('Content-Disposition: inline; filename=calendar.ics');


$usernames = array("mbach", "danield", "dslater", "rsturg", "choi198", "jleigh", "jkim145", "angolz");

$users = array();

$app_years = Years::GetYears($sqlDataBase, APPOINTMENT_YEAR);
$fisc_years = Years::GetYears($sqlDataBase, FISCAL_YEAR);

$years = array_merge($app_years, $fisc_years);

// Calendar info
echo("BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
");

// Go through each listed user
foreach($usernames as $username)  {
    $user = User::GetUserByNetID($sqlDataBase, $username);

    // Go through all the years
    foreach($years as $year) {
        $yearid = $year->getId();
        $yearTypeId = $year->getYearType();

        // "1" is the pay period from August to May
        // "2" is the pay period from May to August
        // Put them together for the full year
        $leaves = $user->GetVacationLeaves($year->getId(), 1, APPROVED);
        $leaves2 = $user->GetVacationLeaves($year->getId(), 2, APPROVED);
        
        $all_leaves = array_merge($leaves, $leaves2);
        // Go through all the leaves and get the data from them 
        foreach($all_leaves as $leave) {
            
            // Unique identifier neede for each entry 
              $uid = md5(uniqid(mt_rand(), true));

              $date = $leave->getDate();

              $startd = new DateTime($date);

              $startDateFormat = $startd ->format('Ymd');
              $leaveType = new LeaveType($sqlDataBase);
              $leaveType->LoadLeaveType($leave->getLeaveTypeId());
              
              // Write the event.
             
                $str = "
                BEGIN:VEVENT
                UID:" .  $uid ."
                DTSTAMP:" . gmdate('Ymd')."T000000Z
                DTSTART;VALUE=DATE:".$startDateFormat. 
                "
                DTEND;VALUE=DATE:".$startDateFormat.
                "
                SUMMARY:". $user->getNetid() . " " . $leaveType->getName(). " " . $leave->getDescription() . 
                "
                END:VEVENT

                ";

                echo $str;
                
            }
            
        }
    
    }

echo("END:VCALENDAR");