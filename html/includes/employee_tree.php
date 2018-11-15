<?php
/**
 * UI employee_tree.php
 * Draws the department hierarchy tree using the draw_employee_tree.php file
 */
?>
<table class="content">
	<tr>
		<td class="page_title"></td>
	</tr>
	<tr>

		<td class="content_bg">
		<?php 
		$queryUsers = "SELECT user_id, first_name, last_name, supervisor_id, email FROM users";
		$users = $sqlDataBase->query($queryUsers);

		$usersHash=array();

		foreach($users as $id=>$user)
		{
			if(array_key_exists($user['supervisor_id'], $usersHash))
			{
				array_push($usersHash[$user['supervisor_id']],$user);
			}
			else
			{
				$usersHash[$user['supervisor_id']]=array($user);
			}
		}
		
		echo "<div id=\"infovis\"></div>";
		echo "<div id=\"log\"></div>"; 
		echo "<script type=\"text/javascript\">
				var json=\"";
		generateOrganizationJson($usersHash);
		echo "\";";
		echo "init(json);";
		echo "</script>";
		
		
		
		function generateOrganizationJson($usersHash)
		{
			echo "{id: \\\"0\\\",";
			echo "name: \\\"IGB\\\",";
			echo "data: { },";
			echo "children: [";
			generateChildrenOrganizationJson(0,$usersHash,1);
			echo "]}";
		}

		function generateChildrenOrganizationJson($supervisorId,$usersHash,$depth)
		{

			if(array_key_exists($supervisorId, $usersHash))
			{
				foreach($usersHash[$supervisorId] as $id=>$user)
				{
					echo "{id: \\\"".$user['user_id']."\\\",";
					echo "name: \\\"".$user['first_name']." ".$user['last_name']."\\\",";
					echo "data: { },";
					echo "children: [";
					generateChildrenOrganizationJson($user['user_id'],$usersHash,$depth+1);
					echo "]}";
					if(array_key_exists(($id+1),$usersHash[$supervisorId]))
					{
						echo ",";
					}
				}
			}
		}
		?>
		</td>
	</tr>
</table>
