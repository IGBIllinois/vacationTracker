<?php
/**
 * Program Name: Vacation Tracker
 * File: index.php
 * Used to keep track of vacation, sick and other leaves for IGB, using University of Illinois leave policy.
 *
 * @author Nevo Band
 */

require_once "includes/main.inc.php";

//Check if user is connected using a token to allow automatic approving of leaves
if((isset($_GET['confirmtoken']) && (!isset($_SESSION['vacation_user_id']) && !isset($_SESSION['vacation_auth_key']))) ||
(isset($_GET['confirmtoken']) && (isset($_GET['autoapprove']) || isset($_GET['autonotapprove'])))  )
{

        $confirmKey = $_GET['confirmtoken'];
	$authenticated = 0;
	$loggedUser = new User($sqlDataBase);

	//Check if token and login are valid
	if($authen->AuthenticateToken($confirmKey,$tokenTimeOut,$loggedUser) && !isset($_GET['logout']))
	{
		require_once "includes/header.php";
		//Check if user selecte to autoapprove all leaves for this token
		if(isset($_GET['autoapprove'])||isset($_GET['autonotapprove']))
		{
			require_once "includes/auto_approve.php";
		}
		else
		{
			require_once "includes/confirm_action.php";
		}
	}
	else
	{
		if(!isset($_GET['logout']))
		{
			
			require_once "includes/header.php";
			//Something went wrong with permissions when user connected, post alert to screen
			switch($authen->getError())
			{
				case Auth::TOKEN_EXPIRED:
					echo Helper::MessageBox("Token Expired",
                                                "Automatic login confirmation token has expired please authenticate first.",
                                                "error");
					require_once "includes/login.php";
					break;
				case Auth::TOKEN_INVALID:
					echo Helper::MessageBox("Confirmation key invalid",
                                                "The confirmation key used in the URL is invalid. ".
                                                "To access the main site please <a href=\"index.php\"><b>Click Here</b></a><br><br>".
                                                "Note: This could happen if the leaves requested for approval were deleted, or the confirmation token in the URL does not exist.",
                                                "error");
					break;
				case Auth::NO_COOKIE:
						
					echo Helper::MessageBox("Cookie Expired",
                                                "Please log in to view leaves.<br><br>".
                                                "Note: This could happen if you already used this link before from a different machine ".
                                                "or your cookies were deleted recently.",
                                                "error");
					require_once "includes/login.php";
					break;
			}
		}
		else
		{
			require_once "includes/header.php";
			require_once "includes/login.php";
		}
	}
}
elseif($loggedUser->getUserId() > 0)
{
		require_once "includes/header.php";

                if(isset($_GET['logout'])) 
                {
                        require_once "includes/login.php";
                        require_once "includes/footer.php";
                        return;
                }
		if(!isset($_GET['view']))
		{
			$_GET['view']="calendar";
		}
		echo "<table class=\"main\" cellspacing=\"0\">";
		require_once "includes/navigation.php";
		echo "<tr><td>";
		//Select which page to show the user based on what tab he clicked
		if(isset($_GET['view']))
		{
			if($_GET['view']=='calendar')
			{
				require_once "includes/calendar_view.php";
			}
			elseif($_GET['view']=='create')
			{
				require_once "includes/manage_leaves.php";
			}
			elseif($_GET['view']=='employees')
			{
				require_once "includes/employees_list.php";
			}
			elseif($_GET['view']=='tree')
			{
				require_once "includes/employee_tree.php";
			}
			elseif($_GET['view']=='userInfo')
			{
				require_once "includes/user_information.php";
			}
			elseif($_GET['view']=='adminCalendar')
			{
				require_once "includes/calendar_admin.php";
			}
                        elseif($_GET['view']=='bannerUpload') 
                        {
                                require_once "includes/banner_upload.php";
                        }
                        
			elseif($loggedUser->getUserPermId()==ADMIN)
			{
				//User is an admin so show him the admin tabs
				if($_GET['view']=='adminUsers')
				{
					require_once "includes/users_list_admin.php";
				}
				elseif($_GET['view']=='adminLeaves')
				{
					require_once "includes/leaves_admin.php";
				}
				elseif($_GET['view']=='adminEditLeaves')
				{
					require_once "includes/edit_leave.php";
				}
				elseif($_GET['view']=='adminEditCalendarDay')
				{
					require_once "includes/calendar_edit_day.php";
				}
				elseif($_GET['view']=='adminEditUser')
				{
					require_once "includes/edit_user.php";
				}
				elseif($_GET['view']=='adminYears')
				{
					require_once "includes/years.php";
				}
				elseif($_GET['view']=='adminAddLeaves')
				{
					require_once "includes/add_leaves_admin.php";
				}
                                
				else
				{
					require_once "includes/calendar_view.php";
				}



			}
			else
			{
				require_once "includes/calendar_view.php";
			}
		}
		else {
			require_once "includes/calendar_view.php";
		}
		echo "</td></tr></table>";
}
else {
	require_once "includes/header.php";
	require_once "includes/login.php";
}

require_once "includes/footer.php";
?>
