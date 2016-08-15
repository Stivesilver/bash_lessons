<?php
	Security::init();
	require("$g_physicalRoot/applications/webset/includes/xmlDocs.php");


	if (io::post("block") > 0) {
		$SQL = "SELECT cnbody FROM webset.sped_constructions WHERE cnrefid = " . io::post("block");
		$xml_temlate = db::execSQL($SQL);
	} else {
		$xml_temlate = base64_decode(io::post("block"));
	}

	$xml_temlate = "<doc>$xml_temlate</doc>";

	$doc = new xmlDoc();

	//die("<textarea name=xmldata style='width:100%; height:70%'>$xml_temlate</textarea>");

	$doc->xml_data = $xml_temlate;

	$doc->edit_mode = "yes";

	print($doc->getHtml());

	print "<br><br><br><br><br><br><input type=button class=zButton value=Close onclick='zWindow.destroy();'>";
?>
