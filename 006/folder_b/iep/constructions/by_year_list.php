<?php

	Security::init();

	require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");

	$refID = io::get('RefID', true);

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$refID = io::get('RefID', true);
	$constr = io::get('area');

	//STATE DATA
	$SQL = "
        SELECT *
          FROM webset.sped_constructions
         WHERE cnrefid = " . $constr . "
    ";
	$result = db::execSQL($SQL);

	$title = $result->fields['cnname'];
	$xml_temlate = '<doc>' . $result->fields['cnbody'] . '</doc>';
	$file_defaults = $result->fields['file_defaults'];

	$SQL = "
            SELECT *
              FROM webset.std_constructions
	         WHERE stdrefid = " . $tsRefID . "
	           AND iepyear = " . $refID . "
	           AND constr_id = " . $constr . "
        ";

	$result = db::execSQL($SQL);

	if ($result->fields['refid'] > 0) {
		$RefID = $result->fields['refid'];
		$xml_values = base64_decode($result->fields['values']);
	} else {
		$RefID = 0;
		$xml_values = IDEAFormDefaults::factory($tsRefID)->getXML();
		//if ($file_defaults!="") include($g_physicalRoot . $file_defaults);
	}

	//PROCESS XML DOCUMENT
	$doc = new xmlDoc();
	$doc->edit_mode = 'yes';
	$doc->edit_prefix = 'constr_';
	$doc->border_color = 'silver';
	$doc->includeStyle = 'yes';
	$mergedDocData = $doc->xml_merge($xml_temlate, $xml_values);
	$doc->xml_data = $mergedDocData;
	$html_result = $doc->getHtml();
	$doc->edit_mode = "no";

	$edit = new EditClass('edit1', '');

	$edit->title = $title;

	$edit->addGroup('General Information');
	$edit->addHTML($doc->getHtml());

	$edit->addGroup('Update Information', true);
	$edit->addControl("Last User", "protected")->value($_SESSION["s_userUID"])->name('lastuser');
	$edit->addControl("Last Update", "protected")->value(date("m-d-Y H:i:s"))->name('lastupdate');

	//$edit->saveLocal = false;
	$edit->firstCellWidth = '0%';

	$edit->topButtons = true;
	$param = array('dskey' => $dskey, 'refid' => $result->fields['refid']);
	$edit->addButton('Populate', "populate( " . json_encode($param) . ")");

	$edit->printEdit();
?>

<script type="text/javascript">
	function populate(param) {
		api.ajax.process(
			UIProcessBoxType.DATA_UPDATE,
			api.url('./populate_apply.ajax.php'),
			{
				'param': JSON.stringify(param)
			},
			true
		).addEventListener(
			ObjectEvent.COMPLETE,
			function (e) {
				api.window.dispatchEvent(ObjectEvent.COMPLETE);
				api.window.destroy();
			}
		)
	}
</script>
