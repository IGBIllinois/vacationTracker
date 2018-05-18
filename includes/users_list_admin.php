<?php
/**
 * UI users_list_admin.php
 * Creates a UI containing a list of users
 * 1) allows ldap searches for new users
 * 2) allows admin to edit user information
 * 3) allows searches of current calendar users
 *
 * @author Nevo Band
 *
 */
$ldap = new Ldap($host." 389");

if(isset($_POST['search']))
{
	if($_POST['searchBox']=="")
	{
		$searchString="%";
	}
	else
	{
		$searchString = mysqli_real_escape_string($sqlDataBase->getLink(),$_POST['searchBox']);
	}
}
else
{
	$searchString="%";
}

if(isset($_GET['edit_user']))
{
	$editUserId = $_GET['edit_user'];
}
else
{
	$editUserId = 0;
}

if(isset($_GET['create_user']))
{
	$createUserId = $_GET['create_user'];
}
else
{
	$createUserId = 0;
}

if(isset($_POST['applyEditUser']))
{
	$editUser = new User($sqlDataBase);
	$editUser->LoadUser($_POST['userId']);
	$editUser->setFirstName($_POST['firstName']);
	$editUser->setLastName($_POST['lastName']);
	$editUser->setNetid($_POST['netid']);
	$editUser->setUserEmail($_POST['email']);
	$editUser->setSupervisorId($_POST['supervisor']);
	$editUser->setUserTypeId($_POST['employeeType']);
	$editUser->setUserPermId($_POST['userPerm']);
	$editUser->setPercent($_POST['percent']);
	list($month,$day,$year) = explode("/",$_POST['startDate']);
	$editUser->setStartDate($year."-".$month."-".$day);
	$editUser->setAutoApprove(((isset($_POST['autoApprove']))?1:0));
	$editUser->setEnabled(((isset($_POST['enabled']))?1:0));
        $editUser->setBannerInclude(((isset($_POST['banner_include']))?1:0));
	$editUser->UpdateDb();
}

if(isset($_POST['createUser']))
{
	$editUser = new User($sqlDataBase);
	list($month,$day,$year) = explode("/",$_POST['startDate']);
	$editUser->CreateUser($_POST['firstName'], $_POST['lastName'], $_POST['userPerm'], $_POST['email'],$_POST['employeeType'],$_POST['netid'],$_POST['supervisor'],$year."-".$month."-".$day,((isset($_POST['autoApprove']))?1:0),$_POST['percent'],((isset($_POST['enabled']))?1:0) );

}

?>

<table class="content">
	<tr>
		<td class="page_title" width="200"></td>
		<td class="page_title"></td>
	</tr>
	<tr>
		<td valign="top">
			<form action="index.php?view=adminUsers" method="post">
				<table width="100%">
					<tr>
						<td class="col_title" colspan="2">Calendar Users</td>
					</tr>
					<tr>
						<td class="form_field">Netid:</td>
						<td class="form_field"><input type="text" name="searchBox"
							value="">
						</td>
					</tr>
					<tr>
						<td></td>
						<td class="form_field"><input class="ui-state-default ui-corner-all" type="submit" name="search"
							value="Search Users">
						</td>
					</tr>
					</form>
					<form action="index.php?view=adminUsers" method="post">
					<tr>
						<td class="col_title" colspan="2">Add New User</td>
					</tr>
					<tr>
						<td class="form_field">Netid:</td>
						<td class="form_field"><input type="text" name="ldapSearchQuery"
							value="">
						</td>
					</tr>
					<tr>
						<td class="form_field"></td>
						<td class="form_field"><input class="ui-state-default ui-corner-all" type="submit" name="searchLdap"
							value="Search IGB Users">
						</td>
					</tr>
                                        <tr>
						<td class="col_title" colspan="2">View options</td>
					</tr>
                                        <tr><td></td>
                                            <td class="form_field"><input class="ui-state-default ui-corner-all" type="submit" name="allUsers"
							value="Show All Users">
                                            <input class="ui-state-default ui-corner-all" type="submit" name="enabled_users"
							value="Show Enabled Users">
                                            <input class="ui-state-default ui-corner-all" type="submit" name="disabled_users"
							value="Show Disabled Users">
                                            </td>
				</table>
			</form>
		</td>
		<td class="content_bg"><?php
		if($editUserId)
		{
			echo "<form action=\"index.php?view=adminUsers\" method=\"POST\">
		<input class=\"ui-state-default ui-corner-all\" type=\"submit\" value=\"Back\" name=\"\">
		<input class=\"ui-state-default ui-corner-all\" type=\"submit\" value=\"Apply\" name=\"applyEditUser\">";

			$editUser = new User($sqlDataBase);
			$editUser->LoadUser($editUserId);
			echo "<input type=\"hidden\" name=\"userId\" value=".$editUserId.">";
			include "includes/edit_user.php";
			echo "</form>";
		}
		elseif($createUserId)
		{
			echo "<form action=\"index.php?view=adminUsers\" method=\"POST\">
     	<input class=\"ui-state-default ui-corner-all\" type=\"submit\" value=\"Back\" name=\"\">
	<input class=\"ui-state-default ui-corner-all\" type=\"submit\" value=\"Create\" name=\"createUser\">";

			$editUser = new User($sqlDataBase);
			if(!$ldap->connect())
			{
				die("Error connecting: ".$ldap->ldapError."\n");
			}

			if($ldap->bind())
			{
				if($sr = $ldap->searchSubtree($peopleDN, "uid=".$createUserId,array('uid','cn','mail')))
				{
					if($entry = $sr->firstEntry())
					{
						if($attrs = $entry->getAttributes())
						{
							$editUser->setNetId($attrs['uid'][0]);
							@list($firstName,$lastName) = explode(" ",$attrs['cn'][0]);
							$editUser->setFirstName($firstName);
							$editUser->setLastName($lastName);
							$editUser->setUserEmail($attrs['mail'][0]);
							$editUser->setUserPermId(USER);
							$editUser->setEnabled(ENABLED);
                                                        
						}
					}
				}
			}

			include "includes/edit_user.php";
			echo "</form>";
		}
		else
		{
			?>
			<table class="hover_table" id="hover_table">
				<thead>
					<tr>
						<th>Netid</th>
						<th>First</th>
						<th>Last</th>
						<th>Permissions</th>
						<th>Employee Type</th>
						<th>Enabled</th>
                                                <th>Banner Include</th>
						<td>Options</td>
					</tr>
				</thead>
				<tbody>
				<?php
				if(isset($_POST['searchLdap']))
				{
					if(!$ldap->connect())
					{
						die("Error connecting: ".$ldap->ldapError."\n");
					}

					if($ldap->bind())
					{
						if($sr = $ldap->searchSubtree($peopleDN, "(|(uid=*".$_POST['ldapSearchQuery']."*)(cn=*".$_POST['ldapSearchQuery']."*))",array('uid','cn')))
						{
							if($entry = $sr->firstEntry())
							{
								do
								{
									if($attrs = $entry->getAttributes())
									{
										@list($firstName,$lastName) = explode(" ",$attrs['cn'][0]);
										echo "<tr><td>".$attrs['uid'][0]."</td>
								<td>".$firstName."</td>
								<td>".$lastName."</td>
								<td>n/a</td>
								<td>n/a</td>
								<td><a href=\"index.php?view=adminUsers&create_user=".$attrs['uid'][0]."\">Add</a></td></tr>";
									}

								}while($entry->nextEntry());
							}
						}
					}
				}
				else
				{

                                        $additionalQuery = "";
                                        if(isset($_POST['enabled_users'])) {
                                            $additionalQuery = " and enabled=1 ";
                                        }
                                        if(isset($_POST['disabled_users'])) {
                                            $additionalQuery = " and enabled=0 ";
                                        }
					$queryUsers = "SELECT u.user_id, u.first_name, u.last_name, u.netid, ut.name as type, up.name as perm, u.enabled, u.banner_include FROM users u, user_type ut, user_perm up WHERE up.user_perm_id = u.user_perm_id AND ut.user_type_id = u.user_type_id AND (u.first_name LIKE \"".$searchString."\" OR u.last_name LIKE \"".$searchString."\" OR u.netid LIKE \"".$searchString."\") $additionalQuery ORDER BY u.last_name ASC";
					$users = $sqlDataBase->query($queryUsers);
					if($users)
					{
						foreach($users as $id=>$userInfo)
						{
							echo "<tr>
								<td>".$userInfo['netid']."</td>
								<td>".$userInfo['first_name']."</td>
								<td>".$userInfo['last_name']."</td>
								<td>".$userInfo['perm']."</td>
								<td>".$userInfo['type']."</td>
								<td><img src=\"css/images/".(($userInfo['enabled'])?"approved.png":"notapproved.png")."\"></td>
                                                                <td><img src=\"css/images/".(($userInfo['banner_include'])?"approved.png":"notapproved.png")."\"></td>
								<td><a href=\"index.php?view=adminUsers&edit_user=".$userInfo['user_id']."\">Edit</a></td>
							</tr>";
						}
					}
				}
				?>
				</tbody>
			</table> <?php
}
?>
		</td>
	</tr>
</table>
