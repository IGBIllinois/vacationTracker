<?php
include_once "includes/config.php";
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
# Development URL
$bannerUrl = "https://webservices-dev.admin.uillinois.edu/employeeWS/employeeLeaveBalance";
$senderAppId = "edu.illinois.igb.vsl";

# Testing URL
# $url = "https://webservices-test.admin.uillinois.edu/employeeWS/employeeLeaveBalance";

# Production URL
# $url = "https://webservices.admin.uillinois.edu/employeeWS/employeeLeaveBalance"; 


function apiGetUserInfo($userUin) {
    global $bannerUrl;
    global $senderAppId;
    $apiURL = $bannerUrl . "?senderAppId=$senderAppId&institutionalId=$userUin";
    
       $curl = curl_init();
       curl_setopt($curl, CURLOPT_URL, $apiURL);
       #curl_setopt($curl, CURLOPT_USERPWD, $username.":".$password);
       curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
       
       //echo ("url = $apiURL<BR>");
        //curl_setopt($curl, CURLOPT_HEADER, 0);
       $result = curl_exec($curl);
       curl_close($curl);
       return $result;
       

}

/*
 * $userUin: A user's UIN
 * $vacHours: Vacation Hours used
 * $sickHours: Sick hours used. 
 * $date: Date for this transaction (MM/DD/YYYY)
 * 
 */
function apiUpdateUserHours($userUin, $vacHours, $sickHours, $date, $validateOnly="N") {
    global $bannerUrl;
    global $senderAppId;
    $apiURL = $bannerUrl;
       $curl = curl_init();
       curl_setopt($curl, CURLOPT_URL, $apiURL);
       #curl_setopt($curl, CURLOPT_USERPWD, $username.":".$password);
       curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($curl, CURLOPT_POST, 1);
       $fields = "senderAppId=$senderAppId&institutionalId=$userUin&vacTaken=".$vacHours."&sicTaken=".$sickHours."&dateAvailable=".$date."&sbTaken=-1&separationFlag=N&conversionFlag=N&validateOnlyFlag=".$validateOnly."";
       echo("fields = $fields<BR>");
       curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
       //curl_setopt($curl, CURLOPT_HEADER, 0);
       $result = curl_exec($curl);
       return $result;    
}

function getUserUIN($netId) {
    global $peopledb_host, $peopledb_database, $peopledb_user, $peopledb_password;

    $peopleDB = new SQLDataBase($peopledb_host, $peopledb_database, $peopledb_user, $peopledb_password);
    $query="SELECT uin from users where netid='".$netId."'";
    //echo("uin query = $query");
    $uin = $peopleDB->query($query);
    //print_r($uin);
    return $uin[0]['uin'];
}

$day = Date('d');
$month = Date('m');
$year = Date('Y');
$year_type = "Appointment";

if(isset($_GET['year'])) {
    $year = $_GET['year'];
}

$pay_period = 1;
if(isset($_GET['pay_period']) ) {
    $pay_period = $_GET['pay_period'];
    if($pay_period == 2) {
        // Pay period 2 = 8/16-5/15
        $day = 16;
        $month = 8;
    }
}

$years = new Years($sqlDataBase);
$queryYearTypes = "SELECT year_type_id,name,description FROM year_type";
$yearTypes = $sqlDataBase->query($queryYearTypes);
$appointment_year_id = $years->GetYearId($day,$month,$year,$yearTypes[0]['year_type_id']);
$fiscal_year_id = $years->GetYearId($day,$month,$year,$yearTypes[1]['year_type_id']);

$yearId = $appointment_year_id;

if(isset($_GET['year_type']) && $_GET['year_type'] == "fiscal") {
    $year_type = "Fiscal";
    $yearId = $fiscal_year_id;
}

//$thisPayPeriodId = $years->GetPayPeriodId(Date("d"),Date("m"),$year,$yearId);
//$date = Date("m")."/".Date("d")."/".$year;
$thisPayPeriodId = $years->GetPayPeriodId($day, $month, $year, $yearId);
$userLeavesHoursAvailable = new Rules($sqlDataBase);


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

$user_id = $_GET['user_id'];

$pay_period_text = "8/16 - 5/15";
if($pay_period == 2) {
    $pay_period_text = "5/16 - 8/15";
}
echo("<h2 style='font-style:normal; text-align:left'>$year_type Year: " . $pay_period_text . " " . $year . "</h2>");
echo("<form action=#>"
        ."<input type=hidden name=view value=editUserBanner>"
        . "Go to <select name='year_type'><option value='appointment'>Appointment</option>
            <option value='fiscal'".(($year_type=='Fiscal') ? " SELECTED " : "") .">Fiscal</option></select><BR>
            Pay Period: <select name='pay_period'><option value='1' ". (($pay_period==1) ? " SELECTED " : "").">8/16 - 5/15</option><option value='2' ". (($pay_period==2) ? " SELECTED " : "").">5/16 - 8/15</select>
            Year: <input type=string name='year' value='".$year."'><button type='submit'>Submit</button>
                </form>");
?>

<?php
echo("<form action=#>"
        ."<input type=hidden name=view value=editUserBanner>"
        ."<input type=hidden name=user_id value=".$user_id.">");
?>
        
    <table class="hover_table" id="hover_table">
            <thead>
                    <tr>
                            <th>First</th>
                            <th>Last</th>
                            <th>UIN</th>
                            
                            <?php
                                if($year_type == 'Fiscal') {
                                    echo("<th>Total Banner<BR>Floating Holiday Hours</th>");
                                    echo("<th>Floating Holiday<BR>Hours Taken</th>");
                                } else {
                                    echo("<th>Total Banner<BR>Vacation Hours</th>
                                            <th>Total Banner<BR>Sick Hours</th>
                                            <th>Vacation Hours Taken</th>
                                          <th>Sick Hours Taken</th>");
                                }
                                ?>
                           
                    </tr>
            </thead>
            <tbody>
<?php


$queryUser = "SELECT u.user_id, u.first_name, u.last_name, u.netid, ut.name as type, up.name as perm, u.enabled FROM users u, user_type ut, user_perm up WHERE up.user_perm_id = u.user_perm_id AND ut.user_type_id = u.user_type_id AND enabled=1 and u.user_id='".$user_id."' ORDER BY u.netid ASC";
					$userInfo = $sqlDataBase->query($queryUser);
                                        $userInfo = $userInfo[0];
                                        //print_r($userInfo);
					if($userInfo)
					{
						
                                                    //echo("id=$id <BR>");
                                                    // See Helper->DrawLeaveHoursAvailable for hour calculation
                                                    
                                                    $leavesAvailable = $userLeavesHoursAvailable->LoadUserYearUsageCalc($userInfo['user_id'],$yearId,$thisPayPeriodId);
                                                    //echo("LEAVES:<BR:");
                                                    //print_r($leavesAvailable);
                                                    //echo("<BR><BR>");
                                                    $uin = getUserUIN($userInfo['netid']);
                                                    $userXML= apiGetUserInfo($uin);
                                                    //echo("UserXML = " . $userXML);
                                                    
                                                    try {
                                                    $xml = new SimpleXMLElement($userXML);
                                                    } catch(Exception $e) {
                                                        //echo("Error:");
                                                        //echo($e->getTraceAsString());
                                                    }
                                                    $current_vacation_hours = 0;
                                                    $current_sick_hours = 0;
                                                    $current_floating_hours = 0;
                                                    foreach($xml->children() as $leave) {
                                                        //print_r($leave);
                                                        $code = $leave->Leave[0]->ValidLeaveTitle[0]->Code;
                                                        if($code == "VACA") {
                                                            $current_vacation_hours = $leave->Leave[0]->BeginBalance;
                                                            $accrued_vacation_hours = $leave->Leave[0]->Accrued;
                                                            $total_vacation_hours = $current_vacation_hours + $accrued_vacation_hours;
                                                        } elseif($code == "SICK") {
                                                            $current_sick_hours = $leave->Leave[0]->BeginBalance;
                                                            $accrued_sick_hours = $leave->Leave[0]->Accrued;
                                                            $total_sick_hours = $current_sick_hours + $accrued_sick_hours;
                                                        } elseif($code == "FLHL") {
                                                            $current_floating_hours = $leave->Leave[0]->BeginBalance;
                                                            $accrued_floating_hours = $leave->Leave[0]->Accrued;
                                                            $total_floating_hours = $current_floating_hours + $accrued_floating_hours;
                                                        }
                                                        //echo $leave['Leave']['ValidLeaveTitle']['Code'];
                                                        //echo "<BR>";
                                                    }
                                                    //echo apiGetUserInfo("654954407");
                                                    //echo apiUpdateUserHours($uin, 0, 0, "04/05/2017");
							echo "<tr>
								<td>".$userInfo['first_name']."</td>
								<td>".$userInfo['last_name']."</td>
                                                                <td>".$uin."</td>";
                                                                
                                                                if($year_type == 'Fiscal') {
                                                                    echo("<td>". $total_floating_hours."</td>");
                                                                    echo("<td><input type=text name='float_hours_used'>". $leavesAvailable[13]['calc_used_hours']."</input></td>");
                                                                    
                                                                } else {
                                                                    echo ("<td>".$total_vacation_hours."</td>
                                                                    <td>".$total_sick_hours."</td>".
                                                                    "<td><input type=text name='vac_hours_used'>".$leavesAvailable[1]['calc_used_hours']."</input></td>
                                                                    <td><input type=text name='sick_hours_used'>".$leavesAvailable[2]['calc_used_hours']."</input></td>");
                                                                }
                                                        //echo "<td><a href=\"index.php?view=adminUsers&edit_user_banner=".$userInfo['user_id']."\">Edit</a></td>
                                                                
								
							echo("</tr>");
						}
echo("</form>");					
?>
<BR><a href="index.php?view=bannerUpload">Back to Banner Upload</a>
</tbody>
</table>
    
    

