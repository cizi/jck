$(function(){

});

function langChangeRedir(url) {
	var newLang = $("#languageSwitcher").val();
	location.assign(url + "/" + newLang);
}

// Vytvoreni linku z jmena stranky - misto #doplnte#
link_changed = false;
// zajistuje autovyplneni pole link-name po vyplneni name
function generateURL(el) {
	if(!link_changed) {
		var forElement = $(el).attr("validation-for");
		str = truncateString(trim($(el).val()));
		$("#" + forElement).val(str);
	}
}

function linkChanged() {
	link_changed = true;
}

/**
 * Funkce ma jako vstup unicode retezec a jako vystup vyhodi ten samy, ale malymi pismeny
 * bez diakritiky, nealfanumericky znaky nahrazeny "_"
 * Pokud je mode == 1, pak ponechava i tecky a orizne delku na 31 znaku
 * Vystupem by tedy mel byt validni link-name
 *
 */
function truncateString(str) {
	// UTF8 "ěščřžýáíéťúůóď�?ľĺ"
	convFromL = String.fromCharCode(283,353,269,345,382,253,225,237,233,357,367,250,243,271,328,318,314);
	// UTF8 "escrzyaietuuodnll"
	convToL = String.fromCharCode(101,115,99,114,122,121,97,105,101,116,117,117,111,100,110,108,108);

	// zmenseni a odstraneni diakritiky
	str = str.toLowerCase();
	str = strtr(str,convFromL,convToL);

	// jakykoliv nealfanumericky znak (nepouzit \W ci \w, protoze jinak tam necha treba "ďż˝")
	preg = /[^0-9A-Za-z]{1,}?/g;

	// odstraneni nealfanumerickych znaku (pripadne je tolerovana tecka)
	str = trim(str.replace(preg, ' '));
	str = str.replace(/[\s]+/g, '-');

	return str;
}

/**
 * Funkce strtr odpovida teto funkci z PHP
 */
function strtr(s, from, to) {
	out = new String();
	// slow but simple :^)
	top:
		for(i=0; i < s.length; i++) {
			for(j=0; j < from.length; j++) {
				if(s.charAt(i) == from.charAt(j)) {
					out += to.charAt(j);
					continue top;
				}
			}
			out += s.charAt(i);
		}
	return out;
}

function trim(string) {
	//var re= /^\s|\s$/g;
	var re= /^\s*|\s*$/g;
	return string.replace(re,"");
}

function rewriteContent() {
	$("#frm-articleForm-en-header").val($("#frm-articleForm-cs-header").val());
	$("#frm-submitForm-en-header").val($("#frm-submitForm-cs-header").val());
	if (typeof tinyMCE === 'undefined' || typeof tinyMCE == undefined || tinyMCE == "" || tinyMCE == null) {
		$("#article_content_en").val("");
		$("#article_content_en").val($("#article_content_cs").val());
	} else {
		tinymce.get("article_content_en").setContent('');
		tinymce.get("article_content_en").execCommand('mceInsertContent', true, tinymce.get('article_content_cs').getContent());
		tinyMCE.triggerSave();
	}
}

function submitArticleForm(event) {
	if ($('#frm-articleForm-picUrlUpload').length) {	// pokud toto existuje tak jsme na BE
		var articlePicUpload = $("#frm-articleForm-picUrlUpload");
		var articlePicUrl = $("#articleMainImgUrl");
		if ((articlePicUrl.val() === "") && (articlePicUpload.val() === "")) {
			articlePicUpload.addClass("tinym_required_field");
			articlePicUpload.attr("validation", "Hlavní obrázek (660 x 443px) je povinná položka.");	// ARTICLE_MAIN_URL_REQ

			$("#tinym_info_modal_message").text(articlePicUpload.attr("validation"));
			$("#tinym_info_modal").modal();
			articlePicUpload.focus();
			articlePicUpload.val("");

			articlePicUpload.css("background-color",'#fba');
			$("#frm-articleForm").unbind().submit();
		} else {
			articlePicUpload.css("background-color",'#bfa');
			articlePicUpload.removeClass("tinym_required_field");
			articlePicUpload.removeAttr("validation");
		}
		// máme aspoň jeden čas konání
		if ($("#frm-articleForm-type").val() == 1) {
			var takingTime = $('.takingDate');
			var addNextTakingTime = $("#addNextTakingTime");
			if (takingTime.length == 0) {
				$("#tinym_info_modal_message").text( 'Přidejte, prosím, datum a čas konání.');
				$("#tinym_info_modal").modal();
				event.preventDefault();
			}
		}
	} else {											// jinak jsem na FE
		var articlePicUpload = $("#frm-submitForm-picUrlUpload");
		if (articlePicUpload.val() === "") {
			articlePicUpload.addClass("tinym_required_field");
			articlePicUpload.attr("validation", "Hlavní obrázek (660 x 443px) je povinná položka.");	// ARTICLE_MAIN_URL_REQ

			$("#tinym_info_modal_message").text(articlePicUpload.attr("validation"));
			$("#tinym_info_modal").modal();
			articlePicUpload.focus();
			articlePicUpload.val("");

			articlePicUpload.css("background-color",'#fba');
			$("#frm-submitForm").unbind().submit();
		} else {
			articlePicUpload.css("background-color",'#bfa');
			articlePicUpload.removeClass("tinym_required_field");
			articlePicUpload.removeAttr("validation");
		}

		// máme aspoň jeden čas konání
		var takingTime = $('.takingDate');
		var addNextTakingTime = $("#addNextTakingTime");
		if (takingTime.length == 0) {
			$("#tinym_info_modal_message").text( 'Přidejte, prosím, datum a čas konání.');
			$("#tinym_info_modal").modal();
			event.preventDefault();
		}
	}
}

function articleRemoveRequiredFields() {
	$("input").removeClass("tinym_required_field");
	$("textarea").removeClass("tinym_required_field");
}