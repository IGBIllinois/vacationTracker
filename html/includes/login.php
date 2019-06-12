<?php 
/**
 * UI login.php
 * Creates a login box 
 * 
 * @author Nevo Band
 */
?>

<?php 
    $url = 'index.php';
    $url .= (isset($_GET['view']) ? ("?view=".($_GET['view'])) : "");
    $url .= (isset($_GET['confirmtoken']) ? ("&confirmtoken=".($_GET['confirmtoken'])) : "")
    ?>; 
        
<FORM ACTION='<?php echo($url)?>' METHOD="POST">
<br><br>
<center>
<img src="css/images/vacation_logo.png">
<div id="login_box">
<table class="login">
<tr><td><b>Username:</b></td><td><INPUT TYPE="TEXT" NAME="loginname"></td></tr>
<tr><td><b>Password:</b></td><td><INPUT TYPE="PASSWORD" NAME="loginpass"></td></tr>
<Tr><td colspan="2"><INPUT class="ui-state-default ui-corner-all" TYPE="submit" VALUE="Log In" name="submitLogon" style="width: 100px"></td></tr>
</table>
</div>
</center>
</FORM>
