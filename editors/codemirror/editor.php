<?php
  $val = htmlspecialchars($value);
  $cssmode = "'parsecss.js'";
  $cssmodec = "'editors/codemirror/csscolors.css'";
  $xmlmode = "'parsexml.js'";
  $xmlmodec = "'editors/codemirror/xmlcolors.css'";
  $jsmode = "'tokenizejavascript.js', 'parsejavascript.js'";
  $jsmodec = "'editors/codemirror/jscolors.css'";
  $htmlmode = "$xmlmode,$cssmode,$jsmode,'parsehtmlmixed.js'";
  $htmlmodec = "$xmlmodec,$cssmodec,$jsmodec";
  $phpmode = "$cssmode,$xmlmode,$jsmode,'tokenizephp.js','parsephp.js','parsephphtmlmixed.js'";
  $phpmodec = "$htmlmodec,'editors/codemirror/phpcolors.css'";
  $amode = array("c" => $jsmode,"css" => $cssmode,"html" => $htmlmode,"js" => $jsmode,"php" => $phpmode,"xml" => $xmlmode);
  $amodec = array("c" => $jsmodec,"css" => $cssmodec,"html" => $htmlmodec,"js" => $jsmodec,"php" => $phpmodec,"xml" => $xmlmodec);
  $editor = <<<R
<style>
@import url("nii.css");
.CodeMirror-wrapping {
  background:#ffffff;
}
.fullsize {
	position: fixed;
	left: 0;
	top: 0;
	width: 98%;
	height: 98%;
	margin-right: 2%;
	margin-bottom: 2%;
}
</style>
<div id="div_$name">
<div style='border: 1px solid #BBB;width: 100%;padding-left: 12px;background-color: #DDD;text-align: left;border-image: initial;'><input class='imgsave' style='border:0;cursor:pointer' type="submit" value=''> <a class='imgprev' style='margin-bottom: -2px;' href='javascript:chn$name()'></a></div>
<textarea style="width:100%;height:100%;display:none" id="$name" name="$name">$val</textarea>
<div style="width:100%;height:100%;" id="im_$name" onmousemove="cdm$name()"><img src="images/ajload.gif"></div><br>
<script>
var full$name=0;
function chn$name() {
	$("#div_$name").toggleClass("fullsize");
}
function get_edt_{$name}_value() {
	return document.getElementById('$name').value
}
function cdm$name() {
loadobjs("editors/codemirror/codemirror.js");
if (typeof CodeMirror  == 'function') {
  if ($('#im_$name').css('display') != 'none') {
	editor = CodeMirror.fromTextArea(document.getElementById('$name'), { height:'100%',width:'100%', parserfile : [{$amode[$mode]}],stylesheet : [{$amodec[$mode]}],  path: 'editors/codemirror/',   continuousScanning: 500,    lineNumbers: true});
	document.getElementById('im_$name').style.display= 'none';
  }
} else setTimeout("cdm$name() ",50);
}
loadobjs("editors/codemirror/codemirror.js");
setTimeout("cdm$name()",100);
</script>
</div>
R;
?>