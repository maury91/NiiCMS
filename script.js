/*
	Ultima modifica 30/5/12 (v 0.4.2.2)
*/
function radio_value(a){for(i=0;i<a.length;i++)if(a[i].checked)return a[i].value}function movedown(a,b){sup=document.getElementById(a).rows[b+3].innerHTML;document.getElementById(a).rows[b+3].innerHTML=document.getElementById(a).rows[b+2].innerHTML;document.getElementById(a).rows[b+2].innerHTML=sup;sup=document.getElementById(a).rows[b+3].childNodes[0].innerHTML;document.getElementById(a).rows[b+3].childNodes[0].innerHTML=document.getElementById(a).rows[b+2].childNodes[0].innerHTML;document.getElementById(a).rows[b+2].childNodes[0].innerHTML=sup}function moveup(a,b){sup=document.getElementById(a).rows[b+2].innerHTML;document.getElementById(a).rows[b+2].innerHTML=document.getElementById(a).rows[b+1].innerHTML;document.getElementById(a).rows[b+1].innerHTML=sup;sup=document.getElementById(a).rows[b+2].childNodes[0].innerHTML;document.getElementById(a).rows[b+2].childNodes[0].innerHTML=document.getElementById(a).rows[b+1].childNodes[0].innerHTML;document.getElementById(a).rows[b+1].childNodes[0].innerHTML=sup}function dclose(a){document.getElementById(a).style.display="none";document.getElementById(a+"a").setAttribute("onclick","dopen('"+a+"')");document.getElementById(a+"a").innerHTML="+"}function dopen(a){document.getElementById(a).style.display="block";document.getElementById(a+"a").setAttribute("onclick","dclose('"+a+"')");document.getElementById(a+"a").innerHTML="-"}function encodeHex(a){var b="";for(var c=0;c<a.length;c++){b+=pad(toHex(a.charCodeAt(c)&255),2,"0")}return b}function pad(a,b,c){var d=a;for(var e=a.length;e<b;e++){d=c+d}return d}function toHex(a){var b="";var c=true;for(var d=32;d>0;){d-=4;var e=a>>d&15;if(!c||e!=0){c=false;b+=digitArray[e]}}return b==""?"0":b}function evaluateCss(a){var b=a.getElementsByTagName("STYLE");var c=document.getElementsByTagName("HEAD")[0];for(var d=0;d<b.length;d++){c.appendChild(b[d])}}function ajax_installScript(a){if(!a)return;if(window.execScript){window.execScript(a)}else if(window.jQuery&&jQuery.browser.safari){window.setTimeout(a,0)}else{window.setTimeout(a,0)}}function ajax_parseJs(a){var b=a.getElementsByTagName("SCRIPT");var c="";var d="";for(var e=0;e<b.length;e++){if(b[e].src){var f=document.getElementsByTagName("head")[0];var g=document.createElement("script");g.setAttribute("type","text/javascript");g.setAttribute("src",b[e].src)}else{if(navigator.userAgent.toLowerCase().indexOf("opera")>=0){d=d+b[e].text+"\n"}else d=d+b[e].innerHTML}}if(d)ajax_installScript(d)}function ajax_loadContent(a,b,c){if(enableCache&&jsCache[b]){document.getElementById(a).innerHTML=jsCache[b];ajax_parseJs(document.getElementById(a));evaluateCss(document.getElementById(a));if(c){executeCallback(c)}return}var d=dynamicContent_ajaxObjects.length;document.getElementById(a).innerHTML='<img class="ajloader" src="'+imgurl+'">';dynamicContent_ajaxObjects[d]=new sack;dynamicContent_ajaxObjects[d].requestFile=b;dynamicContent_ajaxObjects[d].onCompletion=function(){ajax_showContent(a,d,b,c)};dynamicContent_ajaxObjects[d].runAJAX()}function executeCallback(callbackString){if(callbackString.indexOf("(")==-1){callbackString=callbackString+"()"}try{eval(callbackString)}catch(e){}}function ajax_showContent(a,b,c,d){var e=document.getElementById(a);e.innerHTML=dynamicContent_ajaxObjects[b].response;if(enableCache){jsCache[c]=dynamicContent_ajaxObjects[b].response}dynamicContent_ajaxObjects[b]=false;ajax_parseJs(e);if(d){executeCallback(d)}}function sack(file){this.xmlhttp=null;this.resetData=function(){this.method="POST";this.queryStringSeparator="?";this.argumentSeparator="&";this.URLString="";this.encodeURIString=true;this.execute=false;this.element=null;this.elementObj=null;this.requestFile=file;this.vars=new Object;this.responseStatus=new Array(2)};this.resetFunctions=function(){this.onLoading=function(){};this.onLoaded=function(){};this.onInteractive=function(){};this.onCompletion=function(){};this.onError=function(){};this.onFail=function(){}};this.reset=function(){this.resetFunctions();this.resetData()};this.createAJAX=function(){try{this.xmlhttp=new ActiveXObject("Msxml2.XMLHTTP")}catch(a){try{this.xmlhttp=new ActiveXObject("Microsoft.XMLHTTP")}catch(b){this.xmlhttp=null}}if(!this.xmlhttp){if(typeof XMLHttpRequest!="undefined"){this.xmlhttp=new XMLHttpRequest}else{this.failed=true}}};this.setVar=function(a,b){this.vars[a]=Array(b,false)};this.encVar=function(a,b,c){if(true==c){return Array(encodeURIComponent(a),encodeURIComponent(b))}else{this.vars[encodeURIComponent(a)]=Array(encodeURIComponent(b),true)}};this.processURLString=function(a,b){encoded=encodeURIComponent(this.argumentSeparator);regexp=new RegExp(this.argumentSeparator+"|"+encoded);varArray=a.split(regexp);for(i=0;i<varArray.length;i++){urlVars=varArray[i].split("=");if(true==b){this.encVar(urlVars[0],urlVars[1])}else{this.setVar(urlVars[0],urlVars[1])}}};this.createURLString=function(a){if(this.encodeURIString&&this.URLString.length){this.processURLString(this.URLString,true)}if(a){if(this.URLString.length){this.URLString+=this.argumentSeparator+a}else{this.URLString=a}}this.setVar("rndval",(new Date).getTime());urlstringtemp=new Array;for(key in this.vars){if(false==this.vars[key][1]&&true==this.encodeURIString){encoded=this.encVar(key,this.vars[key][0],true);delete this.vars[key];this.vars[encoded[0]]=Array(encoded[1],true);key=encoded[0]}urlstringtemp[urlstringtemp.length]=key+"="+this.vars[key][0]}if(a){this.URLString+=this.argumentSeparator+urlstringtemp.join(this.argumentSeparator)}else{this.URLString+=urlstringtemp.join(this.argumentSeparator)}};this.runResponse=function(){eval(this.response)};this.runAJAX=function(a){if(this.failed){this.onFail()}else{this.createURLString(a);if(this.element){this.elementObj=document.getElementById(this.element)}if(this.xmlhttp){var b=this;if(this.method=="GET"){totalurlstring=this.requestFile+this.queryStringSeparator+this.URLString;this.xmlhttp.open(this.method,totalurlstring,true)}else{this.xmlhttp.open(this.method,this.requestFile,true);try{this.xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded")}catch(c){}}this.xmlhttp.onreadystatechange=function(){switch(b.xmlhttp.readyState){case 1:b.onLoading();break;case 2:b.onLoaded();break;case 3:b.onInteractive();break;case 4:b.response=b.xmlhttp.responseText;b.responseXML=b.xmlhttp.responseXML;b.responseStatus[0]=b.xmlhttp.status;b.responseStatus[1]=b.xmlhttp.statusText;if(b.execute){b.runResponse()}if(b.elementObj){elemNodeName=b.elementObj.nodeName;elemNodeName.toLowerCase();if(elemNodeName=="input"||elemNodeName=="select"||elemNodeName=="option"||elemNodeName=="textarea"){b.elementObj.value=b.response}else{b.elementObj.innerHTML=b.response}}if(b.responseStatus[0]=="200"){b.onCompletion()}else{b.onError()}b.URLString="";delete b.xmlhttp["onreadystatechange"];b.xmlhttp=null;b.responseStatus=null;b.response=null;b.responseXML=null;break}};this.xmlhttp.send(this.URLString)}}};this.reset();this.createAJAX()}function loadobjs(){if(!document.getElementById)return;var a=arguments[0];var b="";if(loadedobjects.indexOf(a)==-1){if(a.indexOf(".js")!=-1||a.indexOf(".php")!=-1){b=document.createElement("script");b.setAttribute("type","text/javascript");b.setAttribute("src",a)}else if(a.indexOf(".css")!=-1){b=document.createElement("link");b.setAttribute("rel","stylesheet");b.setAttribute("type","text/css");b.setAttribute("href",a)}}if(b!=""){document.getElementsByTagName("head").item(0).appendChild(b);loadedobjects+=a+" "}if(arguments.length>1){var c='"'+arguments[1]+'"';for(i=2;i<arguments.length;i++)c+=',"'+arguments[i]+'"';setTimeout("loadobjs("+c+")",120)}}function ajwrite_p(a,b,c){ajaxGet(a+"write.html?"+b,c,DummyHandler)}function write_into(a,b){ajaxGet(b,write_data,a)}function write_data(a,b){prompt(a)}function ajwrite(a,b){ajaxGet(a+"write.html?"+b,DummyHandler)}function ajread_p(a,b,c,d,e,f){ajaxGetP(a+"read.html?act="+d,e,elaborate_data,a,b,c,d,f)}function ajread(a,b,c,d,e){ajaxGet(a+"read.html?act="+d,elaborate_data,a,b,c,d,e)}function elaborate_data(dati,url,f,f2,act,timeout){var len=0;f2();data="";eval(dati);if(data!="")for(var i=0;i<data.length;i++)f(data[i]);if(timeout>0)setTimeout('ajread("'+url+'",'+f+","+f2+',"'+act+'")',timeout)}function $apphtml(a,b){document.getElementById(a).innerHTML+=b}function $sethtml(a,b){document.getElementById(a).innerHTML=b}function $html(a){return document.getElementById(a).innerHTML}function $(a){return document.getElementById(a)}function ajaxOk(a){if(a.readyState==4&&a.status==200){return a.responseText}else{return false}}function ajaxGetRand(a,b){a+=a.indexOf("?")==-1?"?":"&";a+="rand="+escape(Math.random());arguments[0]=a;try{return ajaxGet.apply(this,arguments)}catch(c){return myDummyApply(ajaxGet,arguments)}}function myDummyApply(funcname,args){var e="funcname(";for(var i=0;i<args.length;i++){e+="args["+i+"]";if(i+1!=args.length){e+=","}}e+=");";return eval(e)}function ajaxGetP(a,b,c){var d=new Array("placeholder");for(var e=3;e<arguments.length;e++){d[d.length]=arguments[e]}var f=CreateXmlHttpReq(DummyHandler);var g=function(){var a=ajaxOk(f);if(a!==false){d[0]=a;try{return c.apply(this,d)}catch(b){return myDummyApply(c,d)}}};f.onreadystatechange=g;f.open("POST",a);f.setRequestHeader("Content-type","application/x-www-form-urlencoded");f.setRequestHeader("Content-length",b.length);f.setRequestHeader("Connection","close");f.send(b)}function ajaxGet(a,b){var c=new Array("placeholder");for(var d=2;d<arguments.length;d++){c[c.length]=arguments[d]}var e=CreateXmlHttpReq(DummyHandler);var f=function(){var a=ajaxOk(e);if(a!==false){c[0]=a;try{return b.apply(this,c)}catch(d){return myDummyApply(b,c)}}};e.onreadystatechange=f;e.open("GET",a);e.send(null)}function DummyHandler(){return true}function CreateXmlHttpReq(a){var b=null;try{b=new XMLHttpRequest}catch(c){try{b=new ActiveXObject("Msxml2.XMLHTTP")}catch(c){b=new ActiveXObject("Microsoft.XMLHTTP")}}b.onreadystatechange=a;return b}var loadedobjects="";var enableCache=false;var jsCache=new Array;var imgurl="images/ajload.gif";var dynamicContent_ajaxObjects=new Array;var digitArray=new Array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f"); function make_live_tooltip(){$(".live-hint").hover(function(e){if(this.title!=""){this.t=this.title;this.title="";$(".tooltip").html(this.t);$(".tooltip").fadeIn("fast")}},function(){this.title=this.t;$(".tooltip").hide()});$(".live-hint").mousemove(function(e){$(".tooltip").css("top",(e.pageY+20)+"px").css("left",(e.pageX)+"px")})}
function make_tooltip() {
    $(".hint").hover(function (e) {
        if (this.title != "") {
            this.t = this.title;
            this.title = "";
            $(".tooltip").html(this.t);
            $(".tooltip").fadeIn("fast");
        }
    }, function () {
        this.title = this.t;
        $(".tooltip").hide()
    });
    $(".hint").mousemove(function (e) {
		xx = (e.pageX > window.innerWidth -260)? window.innerWidth -260 : e.pageX;
		yy = (e.pageY + 20 > window.innerHeight - ($('.tooltip').height()+10))? window.innerHeight - ($('.tooltip').height()+10) : e.pageY + 20;
        $(".tooltip").css("top", (yy) + "px").css("left", (xx) + "px")
    })
}
String.prototype.format = function() {
  var args = arguments;
  return this.replace(/{(\d+)}/g, function(match, number) { 
    return typeof args[number] != 'undefined'
      ? args[number]
      : match
    ;
  });
};
String.prototype.hashCode = function(){
	var hash = 0;
	if (this.length == 0) return hash;
	for (i = 0; i < this.length; i++) {
		char = this.charCodeAt(i);
		hash = ((hash<<5)-hash)+char;
		hash = hash & hash; // Convert to 32bit integer
	}
	return hash;
}

String.prototype.format_f = function() {
  var args = arguments;
  ret = this;
  for (i in args)
	ret = ret.replace("%s",args[i]);
  return ret;
};
function ajax_replace() {
	if (typeof(go_to) == 'function') {
		$('.normal-link').attr("href", function (i,h) { return "javascript:go_to('"+h+"')"; }).removeClass('normal-link').addClass('ajax-link');
	}
	$('.a-button').button();
}
function partial_notify(x, w) {
	url_error = $( "#"+x );
	url_error.addClass( "ui-state-"+w ).show();
	setTimeout(function() {
		url_error.removeClass( "ui-state-"+w ).hide(400);
	}, 1000 );
}
function notify(t,x, w) {
	url_error = $( "#"+x );
	url_error.html( t ).addClass( "ui-state-"+w ).show();
	setTimeout(function() {
		url_error.removeClass( "ui-state-"+w ).hide(400);
	}, 1000 );
}
function quotes(str) {
	str = str.replace(/\"/g, "&quot;");
	str = str.replace(/\'/g, "&apos;");
	return str;
}