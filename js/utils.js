// utils.js
// Author: Jean Dannemann Carone
// Creation date: 02/25/2010

function checkMail(mail) {
	var er = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);

	if(typeof(mail) == "string") {
		if(er.test(mail))
			return true;
	} else if(typeof(mail) == "object") {
		if(er.test(mail.value))
			return true;
	} else
		return false;
}

function textCounter(field, cntfield, maxlimit) {
	var fieldValue = field.value;

	if (fieldValue.length > maxlimit)
		fieldValue = fieldValue.substring(0, maxlimit);
	else
		cntfield.value = maxlimit - fieldValue.length;
}
