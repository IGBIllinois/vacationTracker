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
{	
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
