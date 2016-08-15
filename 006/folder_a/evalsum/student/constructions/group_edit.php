<?php

	Security::init();

	require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");

	$dskey = io::get('dskey');

	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');
	$screenURL = $ds->safeGet('screenURL');
	$group = io::get('group');

	if (io::exists('help')) {
		$help = FFIDEAHelpButton::factory()
			->setHTMLByConstruction(io::get('help'));
	} else {
		$help = '';
	}

	$valRefid = io::get('RefID');
	if ($valRefid != null) {
		$res = db::execSQL("SELECT std.values, cs.cnrefid
				FROM webset.std_constructions AS std
				     INNER JOIN webset.sped_constructions AS cs ON (cs.cnrefid = std.constr_id)
	           WHERE refid = $valRefid
	         ");
		$xml_values = base64_decode($res->fields['values']);
		$ds->del('constr_refid');
		$constr = $res->fields['cnrefid'];
		$constr_form = IDEAFormTemplateConstruction::factory($constr);
		$title = $constr_form->getTitle();
		$xml_temlate = $constr_form->getTemplate();
	} else {
		$constr = io::get('constr');
		$constr_form = IDEAFormTemplateConstruction::factory($constr);
		$title = $constr_form->getTitle();
		$xml_temlate = $constr_form->getTemplate();
		/** @var IDEAFormDefaults $get_xml_class_name */
		$get_xml_class_name = $constr_form->getClassDefaults();
		if (class_exists($get_xml_class_name) === false) {
			$get_xml_class_name = 'IDEAFormDefaults';
		}
		$obj = new $get_xml_class_name($tsRefID);
		$xml_values = $obj->getXML();
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

	$edit = new editClass('edit1', $valRefid);

	$edit->title = $title;

	$edit->setSourceTable('webset.std_constructions', 'refid');

	$edit->addGroup('General Information');
	$edit->addHTML($html_result);
	$edit->addControl('', 'hidden');
	$edit->addGroup('Update Information', true);
	$edit->addControl('User', 'protected')
		->value(SystemCore::$userUID)
		->sqlField('lastuser');

	$edit->addControl('Update', 'protected')
		->value(date('m-d-Y H:i:s'))
		->sqlField('lastupdate');

	$edit->addControl('Student', 'hidden')
		->value($tsRefID)
		->sqlField('stdrefid');

	$edit->addControl("Eval Process ID", "hidden")
		->value($evalproc_id)
		->sqlField('evalproc_id');

	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;

	if (io::get('top') != 'no') $edit->topButtons = true;

	$edit->saveLocal = false;

	$edit->firstCellWidth = '7%';

	$edit->finishURL = CoreUtils::getURL('./group_list.php', array('dskey' => $dskey, 'group' => $group));
	$edit->saveURL = CoreUtils::getURL('./group_save.php', array_merge($_GET, array('RefID' => $valRefid, 'constr' => $constr)));
	$edit->cancelURL = CoreUtils::getURL('./group_list.php', array('dskey' => $dskey, 'group' => $group));

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_constructions')
			->setKeyField('refid')
			->setRefids($valRefid)
			->setDsKey("'" . $dskey . "'")
	);

	if ($help) {
		$edit->addButton($help);
	}

	if (io::get('print') != "no") {
		$edit->addButton(
			IDEAFormat::getPrintButton(array('tsRefID' => $tsRefID, 'dskey' => $dskey))
		);
	}

	io::jsVar('dskey', $dskey);

	$edit->printEdit();

?>
