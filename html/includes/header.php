<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php
    if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == ""){
        $redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: $redirect");
    }
?>
<HTML>
<head>
    <title>IGB Vacation Calendar</title>
<link rel="stylesheet"
	href="css/custom-theme/jquery-ui-1.8.6.custom.css">
<link rel="stylesheet" type="text/css" href="css/speech_bubble.css" />
<link rel="stylesheet" type="text/css" href="css/vacationTracker.css" />
<link rel="stylesheet" type="text/css" href="css/Spacetree.css" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="css/vacationTrackerIE.css" />
<![endif]-->


<script type='text/javascript' language='javascript' src='vendor/components/jquery/jquery.js'></script>

<!-- JQuery Tools: http://jquerytools.github.io/download/ -->
<script src="js/jquery.tools.min.js" type="text/javascript"></script>

<script type='text/javascript' language='javascript' src='vendor/components/jqueryui/ui/jquery.ui.core.js'></script>
<script type='text/javascript' language='javascript' src='vendor/components/jqueryui/ui/jquery.ui.widget.js'></script>
<script type='text/javascript' language='javascript' src='vendor/components/jqueryui/ui/jquery.ui.datepicker.js'></script>

<script type='text/javascript' language='javascript' src='vendor/mottie/tablesorter/js/jquery.tablesorter.js'></script>

<!-- JIT: https://github.com/philogb/jit -->
<script src="js/jit.js"></script>

<!-- excanvas.js: https://github.com/arv/explorercanvas -->
<script src="js/excanvas.js"></script>

<!-- jscolor: https://github.com/EastDesire/jscolor -->
<script src="js/jscolor.js" type="text/javascript"></script>

<script type='text/javascript' language='javascript' src='vendor/components/jqueryui/ui/jquery.ui.accordion.js'></script>

<script type='text/javascript' language='javascript' src='vendor/components/jqueryui/ui/jquery.ui.tabs.js'></script>

<script type='text/javascript' language='javascript' src='vendor/components/jqueryui/ui/jquery.ui.dialog.js'></script>

<script type='text/javascript' language='javascript' src='js/vacationTracker.js'></script>

</head>
<body>