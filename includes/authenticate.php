<?php
/**
 * Utility authenticate.php
 * Authenticates user information with he authen class.
 * Also returns proper errors and warnings.
 * 
 * @author Nevo Band
 */
if(isset($_POST['submitLogon']))
{
	$helper = new Helper($sqlDataBase);
	if($authen->AuthenticateLdap($_POST['loginname'],$_POST['loginpass'],""))
	{
		$queryCheckFirstTimeLogin="SELECT user_id FROM users WHERE enabled=1 AND netid=\"".$_POST['loginname']."\"";

		if($sqlDataBase->countQuery($queryCheckFirstTimeLogin)==0)
		{
			$userInfo = $authen->GetUserInfo($_POST['loginname'],$_POST['loginpass']);
			if($userInfo)
			{
				echo $helper->MessageBox("Not Registered","You are not a registered user.\nTo register please talk to the business office.","error");
			}
		}
        $userId = $sqlDataBase->singleQuery($queryCheckFirstTimeLogin);
        $loggedUser->LoadUser($userId);
        $loggedUser->UpdateAuthKey();
		$_SESSION['vacation_user_id']=$loggedUser->getUserId();
		$_SESSION['vacation_auth_key']= $loggedUser->getAuthKey();
	}
	else
	{
		echo $helper->MessageBox("Authentication Failed","The user name or password used are incorrect please try again.","error");
	}
}

if(isset($_SESSION['vacation_user_id']) && isset($_SESSION['vacation_auth_key']))
{

    if($loggedUser->GetAuthKeyByUserId($_SESSION['vacation_user_id'])==$_SESSION['vacation_auth_key'])
    {
        $loggedUser->LoadUser($_SESSION['vacation_user_id']);
    }
}

if(isset($_GET['logout']))
{
	unset($_SESSION['vacation_user_id']);
	unset($_SESSION['vacation_auth_key']);
}

?>
