// efecade.js
// Author: Jean Dannemann Carone
// Creation date: 02/25/2010

var tipoDaPesquisa;
var commentFormSubmitting = false;
var contactFormSubmitting = false;

function trim(value) {
	value = value.replace(/((\s*\S+)*)\s*/, '$1');
	value = value.replace(/\s*((\S+\s*)*)/, '$1');
	return value;
}

function setTipoPesquisa(tipoPesquisa) {
	tipoDaPesquisa = tipoPesquisa;
}

function verificaBusca() {
	if (tipoDaPesquisa == "" || tipoDaPesquisa == undefined || tipoDaPesquisa == "undefined") {
		alert("Escolha se a pesquisa ser\u00e1 feita por t\u00edtulo ou conte\u00fado do texto.");
		return false;
	}

	var valorInputPesuqisa = trim(document.getElementById("s").value);

	if (valorInputPesuqisa == "" || valorInputPesuqisa == undefined || valorInputPesuqisa == "undefined") {
		alert("Digite o conte\u00fado da pesquisa.");
		return false;
	} else if (valorInputPesuqisa.length < 5) {
		alert("Informe o conte\u00fado da pesquisa com no m\u00ednimo 5 caracteres.");
		return false;
	}

	document.getElementById("ccc").value = tipoDaPesquisa;
	return true;
}

function ficarEmNegrito(id) {
	document.getElementById("tit").style.fontWeight = "normal";
	document.getElementById("tex").style.fontWeight = "normal";
	document.getElementById(id).style.fontWeight = "bold";
}

function recaptchaResponse() {
	var responseField = document.querySelector('textarea[name="g-recaptcha-response"]');

	if (responseField && responseField.value != "") {
		return responseField.value;
	}

	if (typeof grecaptcha == "undefined") {
		return "";
	}

	if (grecaptcha.enterprise && typeof grecaptcha.enterprise.getResponse == "function") {
		return grecaptcha.enterprise.getResponse();
	}

	if (typeof grecaptcha.getResponse == "function") {
		return grecaptcha.getResponse();
	}

	return "";
}

function preserveCommentFieldValue(form, field) {
	if (!field || !field.name) {
		return;
	}

	if ((field.type == "checkbox" || field.type == "radio") && !field.checked) {
		return;
	}

	var hiddenField = document.createElement("input");
	hiddenField.type = "hidden";
	hiddenField.name = field.name;
	hiddenField.value = field.type == "checkbox" ? (field.value || "on") : field.value;
	hiddenField.className = "comment-submit-preserved";
	form.appendChild(hiddenField);
}

function setCommentFormSubmitting(form) {
	var fieldIdsToPreserve = ["nome", "email", "theurl", "comment", "isPublicarEmail"];
	var fieldIdsToDisable = fieldIdsToPreserve.concat(["remLen1", "commentSubmit"]);
	var status = document.getElementById("commentSubmitStatus");
	var submitButton = document.getElementById("commentSubmit");
	var i;
	var field;

	for (i = 0; i < fieldIdsToPreserve.length; i++) {
		preserveCommentFieldValue(form, document.getElementById(fieldIdsToPreserve[i]));
	}

	for (i = 0; i < fieldIdsToDisable.length; i++) {
		field = document.getElementById(fieldIdsToDisable[i]);
		if (field) {
			field.disabled = true;
		}
	}

	if (submitButton) {
		submitButton.value = "Enviando...";
	}

	if (status) {
		status.style.display = "inline-block";
	}

	form.className += form.className ? " is-submitting" : "is-submitting";
	form.setAttribute("aria-busy", "true");
}

function setContactFormSubmitting(form) {
	var fieldIdsToPreserve = ["contactName", "contactEmail", "contactMotivo", "contactWebSite", "contactMessage"];
	var fieldIdsToDisable = fieldIdsToPreserve.concat(["remLen22", "contactBtnSend"]);
	var status = document.getElementById("contactSubmitStatus");
	var submitButton = document.getElementById("contactBtnSend");
	var i;
	var field;

	for (i = 0; i < fieldIdsToPreserve.length; i++) {
		preserveCommentFieldValue(form, document.getElementById(fieldIdsToPreserve[i]));
	}

	for (i = 0; i < fieldIdsToDisable.length; i++) {
		field = document.getElementById(fieldIdsToDisable[i]);
		if (field) {
			field.disabled = true;
		}
	}

	if (submitButton) {
		submitButton.value = "Enviando...";
	}

	if (status) {
		status.style.display = "inline-block";
	}

	form.className += form.className ? " is-submitting" : "is-submitting";
	form.setAttribute("aria-busy", "true");
}

function checkCommentsData() {
	var form = document.getElementById("commentform");

	if (commentFormSubmitting) {
		return false;
	}

	if (trim(document.getElementById("nome").value) == "") {
		document.getElementById("nome").focus();
		alert("Informe seu nome.");
		return false;
	} else if (trim(document.getElementById("email").value) == "") {
		document.getElementById("email").focus();
		alert("Informe seu e-mail (s\u00f3 ser\u00e1 publicado caso voc\u00ea deseje).");
		return false;
	} else if (!checkMail(trim(document.getElementById("email").value))) {
		document.getElementById("email").focus();
		alert("Informe um e-mail v\u00e1lido.");
		return false;
	} else if (trim(document.getElementById("comment").value) == "") {
		document.getElementById("comment").focus();
		alert("Fa\u00e7a seu coment\u00e1rio sobre o texto.");
		return false;
	} else if (trim(document.getElementById("comment").value).length > 1000) {
		document.getElementById("comment").focus();
		alert("Coment\u00e1rio deve conter no m\u00e1ximo 1000 caracteres.");
		return false;
	} else if (recaptchaResponse() == "") {
		alert("Confirme que voc\u00ea n\u00e3o \u00e9 um rob\u00f4.   =)");
		return false;
	} 

	commentFormSubmitting = true;
	setCommentFormSubmitting(form);

	window.setTimeout(function() {
		form.submit();
	}, 100);

	return false;
}

function checkContactData() {
	var form = document.getElementById("contactMessageform");

	if (contactFormSubmitting) {
		return false;
	}

	if (trim(document.getElementById("contactName").value) == "") {
		document.getElementById("contactName").focus();
		alert("Informe seu nome.");
		return false;
	} else if (trim(document.getElementById("contactEmail").value) == "") {
		document.getElementById("contactEmail").focus();
		alert("Informe seu e-mail.");
		return false;
	} else if ((trim(document.getElementById("contactEmail").value) != "") && (!checkMail(trim(document.getElementById("contactEmail").value)))) {
		document.getElementById("contactEmail").focus();
		alert("Informe um e-mail v\u00e1lido.");
		return false;
	} else if (document.getElementById("contactMotivo").value == "") {
		document.getElementById("contactMotivo").focus();
		alert("Informe o motivo do contato.");
		return false;
	} else if (trim(document.getElementById("contactMessage").value) == "") {
		document.getElementById("contactMessage").focus();
		alert("Escreva sua mensagem ao fernandod.com.br.");
		return false;
	} else if (trim(document.getElementById("contactMessage").value).length > 1000) {
		document.getElementById("contactMessage").focus();
		alert("Mensagem deve conter no m\u00e1ximo 1000 caracteres.");
		return false;
	} else if (recaptchaResponse() == "") {
		document.getElementById("contactMessage").focus();
		alert("Confirme que voc\u00ea n\u00e3o \u00e9 um rob\u00f4.   =)");
		return false;
	} 

	contactFormSubmitting = true;
	setContactFormSubmitting(form);

	window.setTimeout(function() {
		form.submit();
	}, 100);

	return false;
}
