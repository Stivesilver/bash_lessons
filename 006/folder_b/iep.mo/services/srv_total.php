<?php
	Security::init();

	require_once(SystemCore::$physicalRoot . "/applications/webset/iep.mo/documentation/builder_core/IEPDates.php");
	require_once(SystemCore::$physicalRoot . "/applications/webset/iep.mo/documentation/builder_core/XMLTemplates.php");
	require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$screenURL = $ds->safeGet('screenURL');
	$student = new IDEAStudent($tsRefID);

	$area_id = 122;

	#Finds table ID
	$record = db::execSQL("
    	SELECT refid,
    	       int03
          FROM webset.std_general
         WHERE area_id = " . $area_id . "
           AND stdrefid = " . $tsRefID . "
    ")->assoc();
	$refid = (int)$record['refid'];
	$std_minutes = (int)$record['int03'];

	#Finds default Bell to Bell minutes
	$school_data = db::execSQL("
    	SELECT validvalue as minutes,
    	       vouname
          FROM webset.disdef_validvalues vv
               INNER JOIN webset.vw_dmg_studentmst std ON vv.vourefid = std.vourefid
               INNER JOIN webset.sys_teacherstudentassignment ts ON std.stdrefid = ts.stdrefid
               INNER JOIN sys_voumst vou ON vv.vourefid = vou.vourefid
         WHERE tsrefid = " . $tsRefID . "
           AND valuename = 'BellToBellMinutes'
    ")->assoc();

	if (count($school_data) > 0) {
		$school_minutes = (int)$school_data['minutes'];
		$school_name = $school_data['vouname'];
		$minutes_comment = ($school_minutes > 0 && $std_minutes > 0 && $std_minutes != $school_minutes) ? 'Current Total <b>' . $school_name . '</b> minutes: <b>' . $school_minutes . '</b>' : '';
	} else {
		$school_minutes = '';
		$school_name = '';
		$minutes_comment = '';
	}

	#Starts EditClass Page
	$edit = new EditClass('edit1', $refid);

	$edit->title = 'Total Services';

	$edit->setSourceTable('webset.std_general', 'refid');

	$edit->addGroup('General Information');

	$edit->addControl('a. TOTAL Services minutes in locations not with regular education peers:', 'integer')
		->name('a')
		->sqlField('int01')
		->onBlur('re_calc()')
		->onChange('re_calc()')
		->onKeyUp('re_calc()')
		->req();

	$edit->addControl('b. TOTAL Services minutes in locations with regular education peers:', 'integer')
		->name('b')
		->sqlField('int02');

	$edit->addControl('c. Total Building minutes (generally "bell to bell" schedule):', 'integer')
		->name('c')
		->sqlField('int03')
		->value($school_minutes)
		->append($minutes_comment)
		->onBlur('re_calc()')
		->onChange('re_calc()')
		->onKeyUp('re_calc()')
		->req();

	$edit->addControl('Percent of time in regular education formula: (c-a)/c =', 'double')
		->sqlField('txt01')
		->name('d')
		->disabled()
		->css('background-color', 'silver');

	$edit->addControl('x 100', 'double')
		->sqlField('txt02')
		->name('e')
		->disabled()
		->css('background-color', 'silver');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('Area ID', 'hidden')->value($area_id)->sqlField('area_id');

	$edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey, 'desktop' => io::get('desktop')));
	$edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

	$edit->firstCellWidth = "60%";
	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;

	$edit->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.std_general')
			->setKeyField('refid')
			->applyEditClassMode()
	);

//	$edit->addButton(
//		IDEAFormat::getPrintButton(array('dskey' => $dskey))
//	);

	$edit->printEdit();

	$content = get_block(8, $tsRefID, 0, '', array('str' => "8"));
	$content .= get_block(9, $tsRefID, 0, '', array('str' => "9"));
	$doc = new xmlDoc();
	$doc->edit_mode = 'no';
	$doc->includeStyle = 'no';
	$doc->xml_data = '<doc>' . $content . '</doc>';

	print $doc->getHtml();
?>
<script type="text/javascript">
	function re_calc() {
		var post = {};
		a = $("#a");
		c = $("#c");
		d = $("#d");
		e = $("#e");

		if (a.val() >= 0 && c.val() > 0) {
			d.val(+((c.val() - a.val()) / c.val()).toFixed(4));
			e.val(+(100 * d.val()).toFixed(2));
		}
	}
</script>
