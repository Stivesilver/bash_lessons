<?php

	Security::init();

	require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");

	$dskey = io::get('dskey');
	$constr = io::get('constr');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$screenURL = $ds->safeGet('screenURL');
	$constr_form = IDEAFormTemplateConstruction::factory($constr);
	if (io::exists('help')) {
		$help = FFIDEAHelpButton::factory()
			->setHTMLByConstruction(io::get('help'));
	} else {
		$help = '';
	}

	$title = $constr_form->getTitle();
	$xml_temlate = $constr_form->getTemplate();

	//STD DATA
	if (io::get('other_id') != '') {
		$where = ' AND other_id = ' . io::get('other_id');
	} else {
		$where = ' AND other_id IS NULL';
	}

	if (io::get('iep') == "no") {
		$SQL = "
            SELECT *
	          FROM webset.std_constructions
	         WHERE stdrefid = " . $tsRefID . "
	               AND constr_id = " . $constr . "
                   AND iepyear is NULL
                   " . $where . "
        ";
	} else {
		$SQL = "
            SELECT *
              FROM webset.std_constructions
	         WHERE stdrefid = " . $tsRefID . "
	           AND iepyear = " . $stdIEPYear . "
	           AND constr_id = " . $constr . "
               " . $where . "
        ";
	}

	if (io::get('list') == 'yes' && io::get('RefID') == '') {
		$list = new ListClass();

		$list->SQL = $SQL;

		$list->title = $title;

		$list->addColumn('Document')->dataCallback('showTitle');
		$list->addColumn('Last User')->sqlField('lastuser');
		$list->addColumn('Last Update')->sqlField('lastupdate')->type('date');

		$list->addURL = CoreUtils::getURL('main.php', $_GET);
		$list->editURL = CoreUtils::getURL('main.php', $_GET);

		$list->deleteTableName = 'webset.std_constructions';
		$list->deleteKeyField = 'refid';

		$list->getButton(ListClassButton::ADD_NEW)
			->disabled(db::execSQL($SQL)->getOne() > 0);

		$list->addButton(
			FFIDEAExportButton::factory()
				->setTable($list->deleteTableName)
				->setKeyField($list->deleteKeyField)
				->applyListClassMode()
		);

		if($help) {
			$list->addButton($help);
		}

		$list->addButton(
			IDEAFormat::getPrintButton(array('tsRefID' => $tsRefID, 'dskey' => $dskey))
		);

		$list->printList();
	} else {

		$result = db::execSQL($SQL);

		if ($result->fields['refid'] > 0) {
			$RefID = $result->fields['refid'];
		} else {
			$RefID = 0;
		}

		//constr ID from Populate
		$valRefid = $ds->get('constr_refid');
		if ($valRefid != null) {
			$res = db::execSQL("SELECT *
				FROM webset.std_constructions
	           WHERE refid = $valRefid
	         ");
			$xml_values = base64_decode($res->fields['values']);
			$ds->del('constr_refid');
		} else {
			if ($result->fields['refid'] > 0) {
				$xml_values = base64_decode($result->fields['values']);
			} else {
				/** @var IDEAFormDefaults $get_xml_class_name */
				$get_xml_class_name = $constr_form->getClassDefaults();
				if (class_exists($get_xml_class_name) === false) {
					$get_xml_class_name = 'IDEAFormDefaults';
				}
				$obj = new $get_xml_class_name($tsRefID);
				$xml_values = $obj->getXML();
			}
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

		$edit = new editClass('edit1', $RefID);

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

		$edit->addControl("IEP Year", "hidden")
			->value($stdIEPYear)
			->sqlField('iepyear');

		$edit->saveAndEdit = true;
		$edit->saveAndAdd = false;

		if (io::get('desktop') != 'yes' && io::get('nexttab') == '' && io::get('cancel') != 'backToScreen' && io::get('list') != 'yes') {
			$edit->getButton(EditClassButton::SAVE_AND_FINISH)->hide();
		}

		if (io::get('tabgo') == 'yes' && io::get('nexttab') != '') {
			unset($_GET['tabgo']);
			io::js('parent.switchTab(' . io::get('nexttab') . ')');
		}

		if (io::get('top') != 'no') $edit->topButtons = true;

		$edit->saveLocal = false;

		$edit->firstCellWidth = '7%';

		$edit->finishURL = CoreUtils::getURL('main.php', $_GET);
		$edit->saveURL = CoreUtils::getURL('save.php', array_merge($_GET, array('RefID' => $RefID)));

		unset($_GET['RefID']);
		if (io::get('cancel') == 'backToScreen' && io::get('list') != 'yes') {
			$edit->cancelURL = CoreUtils::getURL($screenURL, $_GET);
		} else {
			$edit->cancelURL = CoreUtils::getURL('main.php', $_GET);
		}

		$edit->addButton(
			FFIDEAExportButton::factory()
				->setTable('webset.std_constructions')
				->setKeyField('refid')
				->setRefids($RefID)
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

		if (io::get('iep') != "no") {
			$button = new IDEAPopulateIEPYear($dskey, $constr, '/apps/idea/iep/constructions/by_year_list.php');
			$listButton = $button->getPopulateButton();
			$edit->addButton($listButton);
		}


		io::jsVar('dskey', $dskey);

		$edit->printEdit();

	}

	function showTitle($data, $col) {
		global $title;
		return $title;
	}

?>
