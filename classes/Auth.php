<?php
/**
 * Class Auth.php
 * This class contains simple authentication functions to ldap or a confirmation token
 * 
 * @author nevoband
 *
 */
class Auth
{
	const AUTH_FAILED = 0;
	const AUTH_SUCCESS = 1;
	const TOKEN_EXPIRED = 2;
	const TOKEN_INVALID = 3;
	const COOKIE_EXPIRED = 4;
	const NO_COOKIE = 5;

	var $host="";
	var $peopleDN="";
	var $groupDN="";
	var $ssl="";
	var $port="";
	var $group="";
	var $error="";
	var $sqlDataBase;

	public function __construct(SQLDataBase $sqlDataBase=null)
	{
		$this->sqlDataBase = $sqlDataBase;
	}

	public function __destruct()
	{

	}

	/**
	 * Setup the ldap connection variables
	 * 
	 * @param unknown_type $host
	 * @param unknown_type $peopleDN
	 * @param unknown_type $groupDN
	 * @param unknown_type $ssl
	 * @param unknown_type $port
	 */
	public function SetLdapVars($host,$peopleDN,$groupDN,$ssl,$port)
	{
		$this->host=$host;
		$this->peopleDN=$peopleDN;
		$this->groupDN=$groupDN;
		$this->ssl=$ssl;
		$this->port=$port;
	}

	/**
	 * Authenticate user using a confirmation token instead of a login username and password
	 * @param unknown_type $confirmKey
	 * @param unknown_type $tokenTimeOut
	 * @param User $loggedUser
	 */
	public function AuthenticateToken($confirmKey,$tokenTimeOut,User $loggedUser=null)
	{
		$queryAuthenCodeInfo = "SELECT date_created,supervisor_id,cookie_created
                                FROM authen_key
                                WHERE confirm_key=\"".$confirmKey."\"";

		$authenCodeInfo = $this->sqlDataBase->query($queryAuthenCodeInfo);
                
		if(isset($authenCodeInfo))
		{
			if( abs(time()-strtotime($authenCodeInfo[0]['date_created'])) < ($tokenTimeOut*24*60*60) || (@$loggedUser->getUserId()==$authenCodeInfo[0]['supervisor_id']))
			{
				$computerIp = $_SERVER['REMOTE_ADDR'];

				$cookieConfirmCode = md5(uniqid(rand()));
				if(!$authenCodeInfo[0]['cookie_created'])
				{
					$queryUpdateUsedConfirmKey = "UPDATE authen_key SET cookie_created=1, cookie=\"".$cookieConfirmCode."\" WHERE confirm_key=\"".$confirmKey."\"";
					$this->sqlDataBase->nonSelectQuery($queryUpdateUsedConfirmKey);
					setcookie("Igb_Vacation_Calendar_".$confirmKey,$cookieConfirmCode,time()+$tokenTimeOut*24*60*60);
					return 1;
				}
				elseif($authenCodeInfo[0]['cookie_created'])
				{
						
					if($this->CheckCookie($confirmKey,"Igb_Vacation_Calendar_".$confirmKey))
					{
						return 1;
					}
					else
					{
						if(@$loggedUser->getUserId())
						{
							return 1;
						}
						else
						{
							$this->error=Auth::NO_COOKIE;
						}
					}
				}
			}
			else
			{
				$this->error = Auth::TOKEN_EXPIRED;
			}
		}
		else
		{
			$this->error = Auth::TOKEN_INVALID;
		}

		return 0;
	}

	/**
	 * Authenticate user to LDAP
	 * 
	 * @param unknown_type $username
	 * @param unknown_type $password
	 * @param unknown_type $group
	 */
	public function AuthenticateLdap($username,$password,$group)
	{
		if ($this->ssl == 1) {
			$connect = ldap_connect("ldaps://" . $this->host,$this->port);
			 
		}
		elseif ($this->ssl == 0) {
			$connect = ldap_connect("ldap://" . $this->host,$this->port);
			 
		}
		 
		$bindDN = "uid=" . $username . "," . $this->peopleDN;
		 
		$success = @ldap_bind($connect, $bindDN, $password);

		if ($success == 1 && $group!="") {
			$search = ldap_search($connect,$this->groupDN,"(cn=" . $group . ")");
			$data = ldap_get_entries($connect,$search);
			ldap_unbind($connect);

			foreach($data[0]['memberuid'] as $groupMember) {
				 
				if ($username == $groupMember) {
					$success = 1;
					return $success;
				}
				else {
					$success = 0;
				}
			}
			 
		}
		if($success == 0)
		{
			$error=ldap_error($connect);
		}
		return $success;
	}

	/**
	 * Get the user information from the ldap server 
	 *
	 * @param unknown_type $username
	 * @param unknown_type $password
	 */
	public function GetUserInfo($username,$password)
	{
		if ($this->ssl == 1) {
			$connect = ldap_connect("ldaps://" . $this->host,$this->port);
		}
		elseif ($this->ssl == 0) {
			$connect = ldap_connect("ldap://" . $this->host,$this->port);
		}
		$bindDN = "uid=" . $username . "," . $this->peopleDN;
		$success = @ldap_bind($connect, $bindDN, $password);

		if($success == 1)
		{
			$justthese = array("cn","mail");
			$filter="uid=".$username;
			$sr = ldap_search($connect, $bindDN, $filter, $justthese);
			$info = ldap_get_entries($connect,$sr);
			ldap_close($connect);
			return $info;
		}
		else
		{
			$info = array();
			return $info;
		}

	}

	/**
	 * Check whether or not the user is authorized to connect from his IP address
	 * Enter description here ...
	 * @param unknown_type $ip
	 * @param unknown_type $userId
	 */
	private function CheckUserIpAuthorized($ip,$userId)
	{
		$queryUserIdIp = "SELECT computer_id FROM user_computer WHERE user_id=".$userId." AND computer_ip=\"".$ip."\"";
		$computerId = $this->sqlDataBase->singleQuery($queryUserIdIp);
		if($computerId)
		{
			$queryUpdateComputerLastUsed = "UPDATE user_computer SET last_login=NOW() WHERE computer_id=".$computerId;
			$this->sqlDataBase->nonSelectQuery($queryUpdateComputerLastUsed);
			return 1;
		}
		return 0;
	}

	/**
	 * Check if the user has a cookie which allows him to connect without a user name and password
	 * 
	 * @param unknown_type $confirmKey
	 * @param unknown_type $cookieName
	 */
	private function CheckCookie($confirmKey,$cookieName)
	{
		if(isset($_COOKIE[$cookieName]))
		{
			$cookieConfirmCode = $_COOKIE[$cookieName];
			$queryConfirmKeyCookieMatch = "SELECT COUNT(*) FROM authen_key WHERE confirm_key=\"".$confirmKey."\" AND cookie=\"".$cookieConfirmCode."\"";
			$confirmKeyCookieMatch = $this->sqlDataBase->singleQuery($queryConfirmKeyCookieMatch);
			if($confirmKeyCookieMatch)
			{
				return 1;
			}
		}

		return 0;
	}

	/*
	 * Return the error message from the authentication functions
	 */
	public function getError()
	{
		return $this->error;
	}
}


?>
