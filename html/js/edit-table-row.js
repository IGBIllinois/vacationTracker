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
