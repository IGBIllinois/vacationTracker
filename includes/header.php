<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<HTML>
<head>
<link rel="stylesheet"
	href="css/custom-theme/jquery-ui-1.8.6.custom.css">
<link rel="stylesheet" type="text/css" href="css/speech_bubble.css" />
<link rel="stylesheet" type="text/css" href="css/vacationTracker.css" />
<link rel="stylesheet" type="text/css" href="css/Spacetree.css" />
<!--[if IE]>
<link rel="stylesheet" type="text/css" href="css/vacationTrackerIE.css" />
<![endif]-->
<script src="js/jquery.js" type="text/javascript"></script>
<script src="js/jquery.tools.min.js" type="text/javascript"></script>
<script src="js/ui/jquery.ui.core.js"></script>
<script src="js/ui/jquery.ui.widget.js"></script>
<script src="js/ui/jquery.ui.datepicker.js"></script>
<script src="js/jquery.tablesorter.min.js"></script>
<script src="js/jit.js"></script>
<script src="js/excanvas.js"></script>
<script src="js/space-tree.js"></script>
<script src="js/jscolor.js" type="text/javascript"></script>
<script src="js/edit-table-row.js" type="text/javascript"></script>
<script src="js/leave-selection.js" type="text/javascript"></script>
<script src="js/ui/jquery.ui.accordion.js" type="text/javascript"></script>
<script type="text/javascript" src="js/ui/jquery.ui.tabs.js"></script>
<script src="js/ui/jquery.ui.dialog.js" type="text/javascript"></script>

<script type="text/javascript">
	//Add a widget to talbe sorter to change color of row on hover
	$.tablesorter.addWidget({
	    id: "highlightOnHover",
	    format: function(table) {
	        $("tbody tr.highlight", table).remove();
	        $("tbody tr", table).hover(
	            function(){ $(this).children("td").addClass("highlight"); },
	            function(){ $(this).children("td").removeClass("highlight"); }
	        );
	    }
	});
	//Table sorter and highlight on row hover for all tables 
	$(document).ready(function() 
	    { 
	        $("#hover_table").tablesorter(
	    	{
		    	widgets: ['zebra','highlightOnHover']
	    		
	    	}); 
	    } 
	); 
	//Table sorter for template added leaves
	$(document).ready(function() 
		    { 
		        $("#template_added_leaves").tablesorter(
		    	{
			    	widgets: ['zebra','highlightOnHover']
		    		
		    	}); 
		    } 
		); 
	//Table sorter for user added leaves
	$(document).ready(function() 
	    { 
	        $("#user_added_leaves").tablesorter(
	    	{
		    	widgets: ['zebra','highlightOnHover']
	    	}); 
	    } 
	); 

	//Year type list tables
	$(document).ready(function() 
		    { 
		    	//appointment year
		        $("#1_year_table").tablesorter(
		    	{
		    		widgets: ['zebra','highlightOnHover']
		    	}); 
		    } 
		); 

	$(document).ready(function() 
		    { 
		    	//fiscal year
		        $("#25_year_table").tablesorter(
		    	{
		    		widgets: ['zebra','highlightOnHover']
		    	}); 
		    } 
		);
	//Manage leaves table sorters for each leave status
	$(document).ready(function() 
	    { 
	    	//New leave | appointment year
	        $("#1_1_leaves_table").tablesorter(
	    	{
	    		widgets: ['zebra','highlightOnHover']
	    	}); 
	    } 
	); 
	$(document).ready(function() 
		    { 
	    		//Approved | appointment year
		        $("#2_1_leaves_table").tablesorter(
		    	{
		    		widgets: ['zebra','highlightOnHover']
		    	}); 
		    } 
		); 
	
	$(document).ready(function() 
		    { 
	    		//Waiting approval | appointment year
		        $("#3_1_leaves_table").tablesorter(
		    	{
			    	widgets: ['zebra','highlightOnHover']
		    	}); 
		    } 
		); 
	$(document).ready(function() 
		    { 
	    		//Deleted | appointment year
		        $("#4_1_leaves_table").tablesorter(
		    	{
			    	widgets: ['zebra','highlightOnHover']
		    	}); 
		    } 
		); 
	$(document).ready(function() 
		    { 
	    		//Not approved | appointment year
		        $("#5_1_leaves_table").tablesorter(
		    	{
			    	widgets: ['zebra','highlightOnHover']
		    	}); 
		    } 
		);
	$(document).ready(function() 
		    { 
		    	//New leave | fiscal year
		        $("#1_25_leaves_table").tablesorter(
		    	{
			    	widgets: ['zebra','highlightOnHover']
		    	}); 
		    } 
		); 
	$(document).ready(function() 
		    { 
	    		//Approved | fiscal year
		        $("#2_25_leaves_table").tablesorter(
		    	{
			    	widgets: ['zebra','highlightOnHover']
		    	}); 
		    } 
		); 
	
	$(document).ready(function() 
		    { 
	    		//Waiting approval | fiscal year
		        $("#3_25_leaves_table").tablesorter(
		    	{
			    	widgets: ['zebra','highlightOnHover']
		    	}); 
		    } 
		); 
	$(document).ready(function() 
		    { 
	    		//Deleted | fiscal year
		        $("#4_25_leaves_table").tablesorter(
		    	{
			    	widgets: ['zebra','highlightOnHover']
		    	}); 
		    } 
		); 
	$(document).ready(function() 
		    { 
	    		//Not approved | fiscal year
		        $("#5_25_leaves_table").tablesorter(
		    	{
			    	widgets: ['zebra','highlightOnHover']
		    	}); 
		    } 
		);

	//Date picker
	$(function() {
		$('#datepickerStart').datepicker({
		});
	});
	$(function() {
		$('#datepickerEnd').datepicker({
		});
	});
	$(function() {
		$('#datepickerStartDate').datepicker({
		});
	});

	//Help tooltips
 	$(function() {
 			$("img[title]").tooltip({ position: "bottom left", opacity: 0.8});
 	});

 	$(function() {
			$("input[title]").tooltip({ position: "bottom left", opacity: 0.8});
	});
 	$(function() {
		// a workaround for a flaw in the demo system (http://dev.jqueryui.com/ticket/4375), ignore!
		$( "#dialog:ui-dialog" ).dialog( "destroy" );
	
		$( "#dialog-confirm" ).dialog({
			resizable: false,
			height:140,
			modal: true,
			buttons: {
				"Delete all items": function() {
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
	
	$(function() {
		$("#yeartabs").tabs();
	});
	//the dash number represents the year id. when adding a year you will need to add a year id for the tabs
	$(function() {
		$("#leavetabs-1").tabs();
	});

	$(function() {
     	$("#leavetabs-25").tabs();
    });

	$(function() {
		$("#addleaves_tabs").tabs();
	});
	$(function() {
		$("#addleaves_new").tabs();
	});
	$(function() {
		$("#addleaves_user").tabs();
	});
    
    
	//Used to keep the browser scroller at the right height after a submit or refresh
	//Uses a cookie to store the value of the Yscroll
	var mainWindow;
	var calendarsView;
        $(document).ready(function () {
            mainWindow = document.body;
            mainWindow.scrollTop = $.cookie("mainWindowScroll") || 0;
	    calendarsView = document.getElementById("view_calendar");
            calendarsView.scrollTop = $.cookie("calendarViewsScroll") || 0;
        });
        window.onbeforeunload = function () {

            $.cookie("mainWindowScroll", mainWindow.scrollTop, { expires: 7 });
	    $.cookie("calendarViewsScroll", calendarsView.scrollTop, { expires: 7 });
	    
        }
        /* The jQuery cookie plugin */
        jQuery.cookie = function (name, value, options) {

            if (typeof value != 'undefined') { // name and value given, set cookie

                options = options || {};

                if (value === null) {

                    value = '';

                    options.expires = -1;

                }

                var expires = '';

                if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {

                    var date;

                    if (typeof options.expires == 'number') {

                        date = new Date();

                        date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));

                    } else {

                        date = options.expires;

                    }

                    expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE

                }

                // CAUTION: Needed to parenthesize options.path and options.domain

                // in the following expressions, otherwise they evaluate to undefined

                // in the packed version for some reason...

                var path = options.path ? '; path=' + (options.path) : '';

                var domain = options.domain ? '; domain=' + (options.domain) : '';

                var secure = options.secure ? '; secure' : '';

                document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');

            } else { // only name given, get cookie

                var cookieValue = null;

                if (document.cookie && document.cookie != '') {

                    var cookies = document.cookie.split(';');

                    for (var i = 0; i < cookies.length; i++) {

                        var cookie = jQuery.trim(cookies[i]);

                        // Does this cookie string begin with the name we want?

                        if (cookie.substring(0, name.length + 1) == (name + '=')) {

                            cookieValue = decodeURIComponent(cookie.substring(name.length + 1));

                            break;

                        }

                    }

                }

                return cookieValue;

            }

        };
</script>

</head>
<body>