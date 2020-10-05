<?php
/**
 * UI years.php
 * Creates a UI for the main content page for the "years" tab under administration
 * Includes create_year.php, list_years.php
 * 
 * @author Nevo Band
 */
$years = new Years($sqlDataBase);
?>
<table class="content">
<tr>
        <td class="page_title" width="200">
        </td>
        <td class="page_title">
        </td>
</tr>
<tr>
    <?php
    if(isset($_GET['max_rollover'])) {
        echo('<td valign="top">');
        require_once "includes/rollover_info.php";
        echo("</td>");
    } else {
        ?>
    
        <td valign="top">
        <?php
	require_once "includes/create_year.php";
        ?>
        </td>
        <td class="content_bg" valign="top">
        <?php
	require_once "includes/list_years.php";
        ?>
        </td>
    <?php
    }
    ?>
</tr>
</table>
