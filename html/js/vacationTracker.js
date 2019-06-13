/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// edit-table-row.js
 function editRow(row) {
    var i = 0;
     
    $('td',row).each(function() {
	 if(i>0)
	 {
         	$(this).html('<input type="text" value="' + $(this).html() + '" id="'+row.id+'_'+i+'" />');
	 }
	 i++;
    });
    var leaveTypeCheckBox = document.getElementById(row.id+'_checkbox');
    leaveTypeCheckBox.checked = true; 
    var applyHoursChangesButton = document.getElementById('applyHoursChanges');
    applyHoursChangesButton.type="submit";
    row.onclick = "";
}

function checkByParent(aId, aChecked) {
    var collection = document.getElementById(aId).getElementsByTagName('INPUT');
    for (var x=0; x<collection.length; x++) {
        if (collection[x].type.toUpperCase()=='CHECKBOX')
            collection[x].checked = aChecked;
    }
}


// spacetree.js

var labelType, useGradients, nativeTextSupport, animate;

(function() {
  var ua = navigator.userAgent,
      iStuff = ua.match(/iPhone/i) || ua.match(/iPad/i),
      typeOfCanvas = typeof HTMLCanvasElement,
      nativeCanvasSupport = (typeOfCanvas == 'object' || typeOfCanvas == 'function'),
      textSupport = nativeCanvasSupport 
        && (typeof document.createElement('canvas').getContext('2d').fillText == 'function');
  //I'm setting this based on the fact that ExCanvas provides text support for IE
  //and that as of today iPhone/iPad current text support is lame
  labelType = (!nativeCanvasSupport || (textSupport && !iStuff))? 'Native' : 'HTML';
  nativeTextSupport = labelType == 'Native';
  useGradients = nativeCanvasSupport;
  animate = !(iStuff || !nativeCanvasSupport);
})();

var Log = {
  elem: false,
  write: function(text){
    if (!this.elem) 
      this.elem = document.getElementById('log');
    this.elem.innerHTML = text;
    this.elem.style.left = (500 - this.elem.offsetWidth / 2) + 'px';
  }
};


function init(json){
    //init data
    //end
    
    //A client-side tree generator
    var getTree = (function() {
        var i = 0;
        return function(nodeId, level) {
          var subtree = eval('(' + json.replace(/id:\"([a-zA-Z0-9]+)\"/g, 
          function(all, match) {
            return "id:\"" + match + "_" + i + "\""  
          }) + ')');
          $jit.json.prune(subtree, level); i++;
          return {
              'id': nodeId,
              'children': subtree.children
          };
        };
    })();
    
    //Implement a node rendering function called 'nodeline' that plots a straight line
    //when contracting or expanding a subtree.
    $jit.ST.Plot.NodeTypes.implement({
        'nodeline': {
          'render': function(node, canvas, animating) {
                if(animating === 'expand' || animating === 'contract') {
                  var pos = node.pos.getc(true), nconfig = this.node, data = node.data;
                  var width  = nconfig.width, height = nconfig.height;
                  var algnPos = this.getAlignedPos(pos, width, height);
                  var ctx = canvas.getCtx(), ort = this.config.orientation;
                  ctx.beginPath();
                  if(ort == 'left' || ort == 'right') {
                      ctx.moveTo(algnPos.x, algnPos.y + height / 2);
                      ctx.lineTo(algnPos.x + width, algnPos.y + height / 2);
                  } else {
                      ctx.moveTo(algnPos.x + width / 2, algnPos.y);
                      ctx.lineTo(algnPos.x + width / 2, algnPos.y + height);
                  }
                  ctx.stroke();
              } 
          }
        }
          
    });

    //init Spacetree
    //Create a new ST instance
    var st = new $jit.ST({
        'injectInto': 'infovis',
        //set duration for the animation
        duration: 200,
        //set animation transition type
        transition: $jit.Trans.Quart.easeInOut,
        //set distance between node and its children
        levelDistance: 90,
        //set max levels to show. Useful when used with
        //the request method for requesting trees of specific depth
        levelsToShow: 1,
        //set node and edge styles
        //set overridable=true for styling individual
        //nodes or edges
        Node: {
            height: 40,
            width: 40,
            //use a custom
            //node rendering function
            type: 'nodeline',
            color:'#E3E9FF',
            lineWidth: 2,
            align:"center",
            overridable: true
        },
        
        Edge: {
            type: 'bezier',
            lineWidth: 2,
            color:'#E3E9FF',
            overridable: true
        },
        
        //Add a request method for requesting on-demand json trees. 
        //This method gets called when a node
        //is clicked and its subtree has a smaller depth
        //than the one specified by the levelsToShow parameter.
        //In that case a subtree is requested and is added to the dataset.
        //This method is asynchronous, so you can make an Ajax request for that
        //subtree and then handle it to the onComplete callback.
        //Here we just use a client-side tree generator (the getTree function).
        //request: function(nodeId, level, onComplete) {
        //  var ans = getTree(nodeId, level);
        //  onComplete.onComplete(nodeId, ans);  
        //},
        
        //onBeforeCompute: function(node){
        //    Log.write("loading " + node.name);
        //},
        
        //onAfterCompute: function(){
        //    Log.write("done");
        //},
        
        //This method is called on DOM label creation.
        //Use this method to add event handlers and styles to
        //your node.
        onCreateLabel: function(label, node){
            label.id = node.id;            
            label.innerHTML = node.name;
            label.onclick = function(){
                st.onClick(node.id);
            };
            //set label styles
            var style = label.style;
            style.width = 40 + 'px';
            style.height = 40 + 'px';            
            style.cursor = 'pointer';
            style.color = '#000';
            //style.backgroundColor = '#1a1a1a';
            style.fontSize = '0.9em';
            style.textAlign= 'left';
            style.textDecoration = 'underline';
            style.paddingTop = '10px';
            style.fontWeight = 'bold';
        },
        
        //This method is called right before plotting
        //a node. It's useful for changing an individual node
        //style properties before plotting it.
        //The data properties prefixed with a dollar
        //sign will override the global node style properties.
        onBeforePlotNode: function(node){
            //add some color to the nodes in the path between the
            //root node and the selected node.
            if (node.selected) {
                node.data.$color = "#E3E9FF";
            }
            else {
                delete node.data.$color;
            }
        },
        
        //This method is called right before plotting
        //an edge. It's useful for changing an individual edge
        //style properties before plotting it.
        //Edge data proprties prefixed with a dollar sign will
        //override the Edge global style properties.
        onBeforePlotLine: function(adj){
            if (adj.nodeFrom.selected && adj.nodeTo.selected) {
                adj.data.$color = "#6B8CB7";
                adj.data.$lineWidth = 3;
            }
            else {
                delete adj.data.$color;
                delete adj.data.$lineWidth;
            }
        }
    });
    //load json data
    st.loadJSON(eval( '(' + json + ')' ));
    //compute node positions and layout
    st.compute();
    //emulate a click on the root node.
    st.onClick(st.root);
    //end
    //Add event handlers to switch spacetree orientation.
   function get(id) {
      return document.getElementById(id);  
    };

    var top = get('r-top'), 
    left = get('r-left'), 
    bottom = get('r-bottom'), 
    right = get('r-right');
    
    function changeHandler() {
        if(this.checked) {
            top.disabled = bottom.disabled = right.disabled = left.disabled = true;
            st.switchPosition(this.value, "animate", {
                onComplete: function(){
                    top.disabled = bottom.disabled = right.disabled = left.disabled = false;
                }
            });
        }
    };
    
    top.onchange = left.onchange = bottom.onchange = right.onchange = changeHandler;
    //end

}

// leave-selection.js

// Global variables
var xMousePos = 0; // Horizontal position of the mouse on the screen
var yMousePos = 0; // Vertical position of the mouse on the screen
var xMousePosMax = 0; // Width of the page
var yMousePosMax = 0; // Height of the page
var baseText = null;
var viewportwidth;
var viewportheight;
var dayStartSelection = 0;
var dayCurrentSelection = 0;
var monthYear = "";
var numBoxes=0;

var mouseIsDown = false;
var eventDown = null;
var eventUp = null;

var leaveInfoArray = null;

window.onload = function()
{
  document.onmousedown = docOnMousedown;
  document.onmouseup = docOnMouseup;
}
function docOnMousedown(e)
{
  if(!e) var e = window.event;
  var tname = e.srcElement? e.srcElement : e.target;

  if(tname.className == "calendar_event" || tname.className == "calendar_day" || tname.className == "calendar_day_selected")
  {
  	if(tname.className =="calendar_event")
  	{
  	      	eventDown = tname.id;
		setStartSelection(GetLeaveDay(eventDown));
		resetCalendarSelection();
  	}
  	else if(tname.className =="calendar_day" || tname.className == "calendar_day_selected")
  	{
  		resetCalendarSelection();
  		setStartSelection(tname);
		updateSelection(tname);
  	}
  	else
  	{
  	      eventDown = null;
  	}

	mouseIsDown = true;
  }
}

function docOnMouseup(e)
{
  if(!e) var e = window.event;
  var tname = e.srcElement? e.srcElement : e.target;

  if(tname.className=="calendar_event")
  {
        eventUp = tname.id;

	if((eventDown == eventUp) && (eventDown!= null) && (eventUp!=null))
	{
		showEditLeavePopup(e);
		tname.style.borderColor="#eef06f";
		tname.style.borderWidth="2px";
	}
	else
	{
		showCreateLeavePopup(e);
	}
  }
  else if(tname.className=="calendar_day_selected")
  {
   	showCreateLeavePopup(e);
  }
  /*
  else
  {
        eventUp = null;
	if(tname.parentNode.id!="createLeave")
	{
		if(mouseIsDown)
		{
			showCreateLeavePopup();
		}
	}
  }
  */

  mouseIsDown = false;
}

function showEditLeavePopup(e)
{
	var popUp;
	popUp = document.getElementById("editLeavePopup");
	document.getElementById("editLeaveId").value = eventDown;
	selectOptionByValue(document.getElementById("editUserId"),GetLeaveUserId(eventDown));
	document.getElementById("editHours").value=GetLeaveHours(eventDown);
	document.getElementById("editMinutes").value=GetLeaveMinutes(eventDown);
	selectOptionByValue(document.getElementById("editLeaveType"),GetLeaveType(eventDown));
	selectOptionByValue(document.getElementById("editLeaveTypeSpecial"),GetLeaveTypeSpecial(eventDown));
	document.getElementById("editDescription").value=GetLeaveDescription(eventDown);
	showPopup(popUp,e);	
}

function showCreateLeavePopup(e)
{
	var popUp;
	popUp = document.getElementById("createLeavePopup");
	var checkBoxes = document.calendar.elements['leaveDays[]'];
	for(i=0; i< checkBoxes.length; i++)
   	{
        	if(checkBoxes[i].checked)
        	{
                	if (baseText == null) baseText = popUp.innerHTML;
                	showPopup(popUp,e);
                	break;
        	}
   	}
}

function showPopup(popUp,e){
   // Set Netscape up to run the "captureMousePosition" function whenever
   // the mouse is moved. For Internet Explorer and Netscape 6, you can capture
   // the movement a little easier.
   hidePopup();
   captureMousePosition(e);
   var popupX = xMousePos;
   var popupY = yMousePos;

   var shiftLeft = false;
   var shiftUp = false

   if(xMousePosMax < popupX + 350)
   {
	shiftLeft = true;
   } 
   if(yMousePosMax < popupY + 300)
   {
	shiftUp = true;
   }
	 
   if(shiftUp && shiftLeft)
   {

	popupX = popupX - 300;
	popupY = popupY - 300;
	
   }
   else if(shiftUp && !shiftLeft)
   {

	popupX = popupX - 20;
        popupY = popupY - 260;
   }
   else if(!shiftUp && shiftLeft)
   {
	
	popupX = popupX - 300;
        popupY = popupY + 13;
   }
   else
   {

	popupX = popupX - 20;
	popupY = popupY + 13;
   }
   popUp.style.top = popupY + "px";
   popUp.style.left = popupX + "px";
   popUp.style.position = "absolute";
   popUp.style.visibility = "visible";
}

function hidePopup(){
   var popUp = document.getElementById("createLeavePopup");
   popUp.style.visibility = "hidden";
   popUp = document.getElementById("editLeavePopup");
   popUp.style.visibility = "hidden";
}

function resetCalendarSelection()
{
	hidePopup();
	uncheckAll(document.calendar.elements['leaveDays[]'],1);
	resetCalendarEventBorders();	
	var dayStartSelection = 0;
	var dayCurrentSelection = 0;
	
}

function setStartSelection(start)
{
	dayStartSelection  = start.id;
	dayCurrentSelection = dayStartSelection;
}

function updateSelection(current)
{
	dayCurrentSelection = current.id;
	var checkBoxes = document.calendar.elements['leaveDays[]'];
	for(i=0; i < checkBoxes.length; i++)
	{
		if((checkBoxes[i].value <= dayCurrentSelection && checkBoxes[i].value >= dayStartSelection) || (checkBoxes[i].value >= dayCurrentSelection && checkBoxes[i].value <= dayStartSelection))
		{
			if(document.getElementById(checkBoxes[i].value))
			{
				checkBoxes[i].checked = true;
				document.getElementById(checkBoxes[i].value).className="calendar_day_selected";
			}
		}
		else
		{
			if(document.getElementById(checkBoxes[i].value))
			{
				checkBoxes[i].checked = false;
				document.getElementById(checkBoxes[i].value).className="calendar_day";
			}
		}
	}
}

function checkAll(field)
{
	for (i = 0; i < field.length; i++)
	{
       	field[i].checked = true ;
	}
}

function uncheckAll(field,calendar)
{	if(field != null) {
		numBoxes = field.length;

		if(numBoxes)
		{
			for (i = 0; i < numBoxes; i++)
			{
				uncheckSingle(field[i],calendar);
			}
		}
		else
		{
			
			if(field)
			{
				uncheckSingle(field,calendar);
			}
			else
			{
				
			}
		}
            }
	
}

function uncheckSingle(field,calendar)
{
	field.checked = false ;
	if(calendar)
	{
		if(document.getElementById(field.value))
		{
			document.getElementById(field.value).className="calendar_day";
		}
	}
}

function captureMousePosition(e) {
    if(!e)
    {
      var e= window.event;
    }
    if (document.layers) {
        // When the page scrolls in Netscape, the event's mouse position
        // reflects the absolute position on the screen. innerHight/Width
        // is the position from the top/left of the screen that the user is
        // looking at. pageX/YOffset is the amount that the user has 
        // scrolled into the page. So the values will be in relation to
        // each other as the total offsets into the page, no matter if
        // the user has scrolled or not.
        xMousePos = e.pageX;
        yMousePos = e.pageY;
        xMousePosMax = window.innerWidth+window.pageXOffset;
        yMousePosMax = window.innerHeight+window.pageYOffset;
    } else if (document.all) {
        // When the page scrolls in IE, the event's mouse position 
        // reflects the position from the top/left of the screen the 
        // user is looking at. scrollLeft/Top is the amount the user
        // has scrolled into the page. clientWidth/Height is the height/
        // width of the current page the user is looking at. So, to be
        // consistent with Netscape (above), add the scroll offsets to
        // both so we end up with an absolute value on the page, no 
        // matter if the user has scrolled or not.
        xMousePos = e.clientX + document.body.scrollLeft
        yMousePos = e.clientY + document.body.scrollTop
        xMousePosMax = document.body.clientWidth+document.body.scrollLeft;
        yMousePosMax = document.body.clientHeight+document.body.scrollTop;
    } else if (document.getElementById) {
        // Netscape 6 behaves the same as Netscape 4 in this regard 
        xMousePos = e.pageX;
        yMousePos = e.pageY;
        xMousePosMax = window.innerWidth+window.pageXOffset;
        yMousePosMax = window.innerHeight+window.pageYOffset;
    }
}

function CalendarResize()
{	
}

function closeMessageBox()
{
	var messageBox = document.getElementById("popup_message_box");
	messageBox.style.visibility = "hidden";
	var lockOverlay = document.getElementById("lock_screen");
	lockOverlay.style.visibility = "hidden"	
}

function selectOptionByValue(selObj, val){
    var A= selObj.options, L= A.length;
    while(L){
        if (A[--L].value== val){
            selObj.selectedIndex= L;
            L= 0;
        }
    }
}

function AddLeaveInfo(id,userid,seconds,leaveType,leaveSpecialType,description,day)
{
	if(leaveInfoArray == null)
	{
		leaveInfoArray = new Array();
	}
	leaveInfoArray[id]=new Array(userid,seconds,leaveType,leaveSpecialType,description,day);
		
}

function getElementsByClass(searchClass,node,tag) {
	var classElements = new Array();
	if ( node == null )
		node = document;
	if ( tag == null )
		tag = '*';
	var els = node.getElementsByTagName(tag);
	var elsLen = els.length;
	var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
	for (i = 0, j = 0; i < elsLen; i++) {
		if ( pattern.test(els[i].className) ) {
			classElements[j] = els[i];
			j++;
		}
	}
	return classElements;
}

function resetCalendarEventBorders()
{
	var calendarEventDivs = getElementsByClass("calendar_event",document,'*');
	for(var i in calendarEventDivs)
	{
		calendarEventDivs[i].style.borderWidth="0px";
	}
}

function GetLeaveUserId(id)
{
	return leaveInfoArray[id+''][0];
}

function GetLeaveHours(id)
{
	return Math.floor((leaveInfoArray[id+''][1]/(60*60)));
}

function GetLeaveMinutes(id)
{
	return ((leaveInfoArray[id+''][1] % (60*60))/60);
}

function GetLeaveType(id)
{
	return leaveInfoArray[id+''][2];
}

function GetLeaveTypeSpecial(id)
{
	return leaveInfoArray[id+''][3];
}

function GetLeaveDescription(id)
{
	return leaveInfoArray[id+''][4];
}

function GetLeaveDay(id)
{
	return leaveInfoArray[id+''][5];
}


// js from header.php

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
            if(calendarsView != null) {
                calendarsView = document.getElementById("view_calendar");
                calendarsView.scrollTop = $.cookie("calendarViewsScroll") || 0;
            }
        });
        window.onbeforeunload = function () {

            $.cookie("mainWindowScroll", mainWindow.scrollTop, { expires: 7 });
            if(calendarsView != null) {
                $.cookie("calendarViewsScroll", calendarsView.scrollTop, { expires: 7 });
            }
	    
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