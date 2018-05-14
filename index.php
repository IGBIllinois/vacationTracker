<?php
/**
 * Program Name: Vacation Tracker
 * File: index.php
 * Used to keep track of vacation, sick and other leaves for IGB, using University of Illinois leave policy.
 *
 * @author Nevo Band
 */
error_reporting(E_ALL);
#ini_set('display_errors', '1');
#error_reporting(0);
date_default_timezone_set("America/Chicago");
session_start();
//Load the classes automatically without having to include them
function __autoload($class_name) {
	require_once 'classes/' . $class_name . '.php';
}

//Load configuration file
include "includes/config.php";

//Initialize database 
$sqlDataBase= new SQLDataBase('localhost',$sqlDataBase,$sqlUserName,$sqlPassword);

//initialize ldap authentication object
$authen=new Auth($sqlDataBase);
$authen->SetLdapVars($host,$peopleDN,$groupDN,$ssl,$port);
$loggedUser = new User($sqlDataBase);

//Authenticate user with LDAP and existing account
include "includes/authenticate.php";

//Check if user is connected using a token to allow automatic approving of leaves
if((isset($_GET['confirmtoken']) && (!isset($_SESSION['vacation_user_id']) && !isset($_SESSION['vacation_auth_key']))) ||
(isset($_GET['confirmtoken']) && (isset($_GET['autoapprove']) || isset($_GET['autonotapprove'])))  )
{

	$confirmKey = mysqli_real_escape_string($_GET['confirmtoken']);
	$authenticated = 0;
	$loggedUser = new User($sqlDataBase);

	//Check if token and login are valid
	if($authen->AuthenticateToken($confirmKey,$tokenTimeOut,$loggedUser) && !isset($_GET['logout']))
	{
		include "includes/header.php";
		//Check if user selecte to autoapprove all leaves for this token
		if(isset($_GET['autoapprove'])||isset($_GET['autonotapprove']))
		{
			include "includes/auto_approve.php";
		}
		else
		{
			include "includes/confirm_action.php";
		}
	}
	else
	{
		if(!isset($_GET['logout']))
		{
			
			include "includes/header.php";
			//Something went wrong with permissions when user connected, post alert to screen
			switch($authen->getError())
			{
				case Auth::TOKEN_EXPIRED:
					echo Helper::MessageBox("Token Expried","Automatic login confirmation token has expired please authenticate first.","error");
					include "includes/login.php";
					break;
				case Auth::TOKEN_INVALID:
					echo Helper::MessageBox("Confirmation key invalid","The confirmation key used in the URL is invalid. To access the main site please <a href=\"index.php\"><b>Click Here</b></a><br><br>Note: This could happen if the leaves requested for approval were deleted, or the confirmation token in the URL does not exist.","error");
					break;
				case Auth::NO_COOKIE:
						
					echo Helper::MessageBox("Cookie Expired","Please log in to view leaves.<br><br>Note: This could happen if you already used this link before from a different machine or your cookies were deleted recently.","error");
					include "includes/login.php";
					break;
			}
		}
		else
		{
			include "includes/header.php";
			include "includes/login.php";
		}
	}
}
elseif($loggedUser->getUserId() > 0)
{

		include "includes/header.php";


		if(!isset($_GET['view']))
		{
			$_GET['view']="calendar";
		}
		echo "<table class=\"main\" cellspacing=\"0\">";
		include "includes/navigation.php";
		echo "<tr><td>";
		//Select which page to show the user based on what tab he clicked
		if(isset($_GET['view']))
		{
			if($_GET['view']=='calendar')
			{
				include "includes/calendar_view.php";
			}
			elseif($_GET['view']=='create')
			{
				include "includes/manage_leaves.php";
			}
			elseif($_GET['view']=='employees')
			{
				include "includes/employees_list.php";
			}
			elseif($_GET['view']=='tree')
			{
				include "includes/employee_tree.php";
			}
			elseif($_GET['view']=='report')
			{
				include "includes/report.php";
			}
			elseif($_GET['view']=='userInfo')
			{
				include "includes/user_information.php";
			}
			elseif($_GET['view']=='adminCalendar')
			{
				include "includes/calendar_admin.php";
			}
                        elseif($_GET['view']=='bannerUpload') 
                        {
                                include "includes/banner_upload.php";
                        }
                        
			elseif($loggedUser->getUserPermId()==ADMIN)
			{
				//User is an admin so show him the admin tabs
				if($_GET['view']=='adminUsers')
				{
					include "includes/users_list_admin.php";
				}
				elseif($_GET['view']=='adminLeaves')
				{
					include "includes/leaves_admin.php";
				}
				elseif($_GET['view']=='adminEditLeaves')
				{
					include "includes/edit_leave.php";
				}
				elseif($_GET['view']=='adminEditCalendarDay')
				{
					include "includes/calendar_edit_day.php";
				}
				elseif($_GET['view']=='adminEditUser')
				{
					include "includes/edit_user.php";
				}
				elseif($_GET['view']=='adminYears')
				{
					include "includes/years.php";
				}
				elseif($_GET['view']=='adminAddLeaves')
				{
					include "includes/add_leaves_admin.php";
				}
				else
				{
					include "includes/calendar_view.php";
				}



			}
			else
			{
				include "includes/calendar_view.php";
			}
		}
		else {
			include "includes/calendar_view.php";
		}
		echo "</td></tr></table>";
}
else {
	include "includes/header.php";
	include "includes/login.php";
}

include "includes/footer.php";
?>
