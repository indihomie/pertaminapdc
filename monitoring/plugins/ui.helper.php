<?php
if(!function_exists("uiSelect")){
	function uiSelect($sql, $key, $value, $identifier, $defValue = "", $placeholder = " ", $attr = "", $class = "", $parent = ""){
		$tmpl = "<select class=\"$class\" name=\"$identifier\" id=\"$identifier\" $attr>";
		
		if(!empty($placeholder) && $placeholder != " ")
			$tmpl .= "<option value=\"\">$placeholder</option>";

		$res = db($sql);
		while($r = mysql_fetch_assoc($res)){
			$selected = $defValue == $r[$key] ? "selected" : "";
			$chained = !empty($parent) ? "class=\"$r[$parent]\"" : "";
			$tmpl .= "<option value=\"$r[$key]\" $chained $selected>$r[$value]</option>";
		}

		$tmpl .= "</select>";
		return $tmpl;
	}
}

if(!function_exists("uiRadioArray")){
	function uiRadioArray($array = array(), $identifier, $defValue = "", $attr = "", $firstItemSelected = FALSE){
		$tmpl = "";
		$no = 0;
		foreach($array as $key => $value){
			$checked = $defValue == $key ? "checked=\"checked\"" : "";
			$checked = ($firstItemSelected == TRUE && empty($checked) && $no == 0) ? "checked=\"checked\"" : $checked;

			$tmpl .= "<input type=\"radio\" $checked name=\"$identifier\" id=\"$key\" value=\"$key\" $attr> $value &nbsp;&nbsp;";
			$no++;
		} 
		return $tmpl;
	}
}

if(!function_exists("uiRadioDb")){
	function uiRadioDb($sql, $key, $value, $identifier, $defValue = "", $attr = "", $firstItemSelected = FALSE){
		$tmpl = "";
		$no = 0;
		$res = db($sql);
		while($r = mysql_fetch_assoc($res)){
			$checked = $defValue == $r[$key] ? "checked=\"checked\"" : "";
			$checked = ($firstItemSelected == TRUE && empty($checked) && $no == 0) ? "checked=\"checked\"" : $checked;

			$tmpl .= "<input type=\"radio\" $checked name=\"$identifier\" id=\"$r[$key]\" value=\"$r[$key]\" $attr> $r[$value] &nbsp;&nbsp;";
			$no++;
		} 
		return $tmpl;
	}
}

if(!function_exists("jsChain")){
	function jsChain($childSelector, $parentSelector){
		$tmpl = "
		jQuery(document).ready(function(){
			jQuery(\"$childSelector\").chained(\"$parentSelector\");
			jQuery(\"$parentSelector\").trigger(\"chosen:updated\");

			jQuery(\"$parentSelector\").bind(\"change\", function(){
				jQuery(\"$childSelector\").trigger(\"chosen:updated\");
			});
		});";

		return $tmpl;
	}
}
if(!function_exists("hasAccess")){
	function hasAccess($access = "view", $kodeMenu = NULL){
		global $menuAccess, $s;
		$s = $kodeMenu != NULL ? $kodeMenu : $s;
		return isset($menuAccess[$s][$access]);
	}
}
?>