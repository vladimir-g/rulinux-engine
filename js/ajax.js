function createRequestObject() {
	var ro;
	var browser = navigator.appName;
	
	if (window.XMLHttpRequest) {
        try {
            ro = new XMLHttpRequest();
        } catch (e){}
    } else if (window.ActiveXObject) {
        try {
            ro = new ActiveXObject('Msxml2.XMLHTTP');
        } catch (e){
            try {
                ro = new ActiveXObject('Microsoft.XMLHTTP');
            } catch (e){}
        }
    }
	return ro;
}
var http=new Array();
//var http_req=new Array();
function sendRequest(page,callBack,errorCallBack,post) {
	cur_num=http.length;
	http[cur_num]={callBack: callBack, errorCallBack: errorCallBack, reqObject: createRequestObject() };
	if(!http[cur_num].reqObject)
	{
		alert('Ваш браузер не поддерживает технологию AJAX');
		return false;
	}
	if(!post)
	{
		http[cur_num].reqObject.open('GET', page,true);
		post=null;
	}else{
		http[cur_num].reqObject.open('POST', page,true);
		http[cur_num].reqObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	}
	eval('http[cur_num].reqObject.onreadystatechange = function(){ onreadystatechange_manual('+cur_num+'); };');
	
	http[cur_num].reqObject.send(post);
	return cur_num;
}
function onreadystatechange_manual(cur_num)
{
	if(http[cur_num].reqObject.readyState == 4){
		if (http[cur_num].reqObject.status == 200) {
			if(http[cur_num].reqObject.responseText!='Error:NoAccess')
			{
				http[cur_num].callBack(http[cur_num].reqObject.responseText);
			}else if(http[cur_num].errorCallBack){
				http[cur_num].errorCallBack(403);
			}
		}else if(http[cur_num].errorCallBack){
			http[cur_num].errorCallBack(http[cur_num].reqObject.status);
		}
	}
}

// Функция аналогична urlencode в php те кодирует русский текст для корректности url
function encoderus(EntryTXT) {
	var text = "";
	var Ucode;
	var ExitValue;
	var s;

	for (var i=0; i<EntryTXT.length; i++) {

		s= EntryTXT.charAt(i);
		Ucode = s.charCodeAt(0);
		var Acode = Ucode;
		if (Ucode > 1039 && Ucode < 1104){
			Acode -= 848;
			ExitValue = "%" + Acode.toString(16);
		}
		else
		if (Ucode == 1025) {
			Acode = 168;
			ExitValue = "%" + Acode.toString(16);
		}
		else
		if (Ucode == 1105){
			Acode = 184;
			ExitValue = "%" + Acode.toString(16);
		}
		else
		if (Ucode == 38){
			Acode = 38;
			ExitValue = "%" + Acode.toString(16);
		}else
		if (Ucode == 32){
			Acode = 32;
			ExitValue = "%" + Acode.toString(16);
		}else
		if(Ucode == 10){
			Acode=10;
			ExitValue = "%0A";
		}else
		if(Ucode == 43){
			Acode=10;
			ExitValue = "%2B";
		}else
		ExitValue=s;

		text = text + ExitValue;

	}
	return text;
}
//coded by DnAp. Great Thanks to him!