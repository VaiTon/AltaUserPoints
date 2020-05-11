function countlength(field, countFieldId, maxlimit) {
	if (document.getElementById){
		target = document.getElementById(countFieldId);
		if (field.value.length > maxlimit){ // if too long...trim it!
			field.value = field.value.substring(0, maxlimit);
			// otherwise, update 'characters left' counter
		} else {
			target.innerHTML = (maxlimit - field.value.length);
		}
	}
}


function showInputField(field, relative, maxlimit) {
	if (document.getElementById){
		target = document.getElementById(field);	
		target.style.display = "block";
		target.innerHTML = (maxlimit - relative.value.length);		
	}		

}

