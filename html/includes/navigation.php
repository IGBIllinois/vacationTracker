<?php
/**
 * UI navigation.php
 * Creates the main navigation tabs at the top
 * Makes sure the images are ordered correctly to give the user information regarding which
 * tab he is currently viewing.
 * 
 * @author Nevo Band
 */
echo "<tr><td align=\"right\">";

echo "<table cellspacing=\"20\"><tr>";

if($loggedUser->getUserPermId() == ADMIN)
{
	echo "<td><a class=\"main_nav\" href=\"index.php?view=adminCalendar\">Admin View</a></td>";
	echo "<td><a class=\"main_nav\" href=\"index.php?view=calendar\">User View</a></td>";
}
echo "<td><a class=\"main_nav\" href=\"index.php?logout=1\">Logout</a></td>";
echo "</tr></table>";

echo "</td></tr><tr><td align=\"left\">";
$adminNavigationArray = array('adminCalendar'=>'Calendar Settings','adminUsers'=>'Manage Users','adminYears'=>'Years','adminLeaves'=>'Leave Type Settings','adminAddLeaves'=>'Add Leaves', 'bannerUpload'=>"Banner Upload");
$userNavigationArray = array('calendar'=>'Calendar','create'=>'Manage Leaves','employees'=>'User Accounts','tree'=>'Department');
$imagePath = "css/images/";

if(array_key_exists($_GET['view'],$userNavigationArray))
{
	$navigationArray = $userNavigationArray;
}
elseif(array_key_exists($_GET['view'],$adminNavigationArray))
{
	$navigationArray = $adminNavigationArray;
}
else
{
	$navigationArray = $userNavigationArray;
}

$lengthArr = sizeof($navigationArray);
echo "<table class=\"main_nav\" border=0><tr>";

$i=0;
$activeFlag = 0;
foreach($navigationArray as $view => $navigationView)
{
	echo "<td>";
	if($i==0)
	{
		if($_GET['view']==$view)
		{
			echo "<img src=\"".$imagePath."first_active.png\">";
			$activeFlag = 1;
		}
		else
		{
			echo "<img src=\"".$imagePath."first_inactive.png\">";
		}
	}
	elseif($activeFlag)
	{
		echo "<img src=\"".$imagePath."active_right.png\">";
		$activeFlag=0;
	}
	elseif($_GET['view']==$view)
	{
		echo "<img src=\"".$imagePath."active_left.png\">";
		$activeFlag = 1;
	}
	else
	{
		echo "<img src=\"".$imagePath."inactive_right.png\">";
	}
	echo "</td>";
	if($activeFlag)
	{
		echo "<td class=\"active_nav\">";
	}
	else
	{
		echo "<td class=\"inactive_nav\">";
	}
	echo "<center><a href=\"index.php?view=".$view."\">".$navigationArray[$view]."</a></center></td>";
	
	$i++;
	
}
echo "<td>";

if($activeFlag)
{
    	echo "<img src=\"".$imagePath."last_active_right.png\">";
}
else
{
	echo "<img src=\"".$imagePath."last_inactive_right.png\">";
}
echo "</td>";
echo "</tr></table>";

echo "</td></tr>";
?>

