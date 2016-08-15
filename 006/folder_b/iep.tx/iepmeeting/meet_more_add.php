<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$area_id = 113;

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit Related Services Dates';

	$edit->setSourceTable('webset.std_general', 'refid');

	$helpButon = FFMenuButton::factory('Populate');

	$texts = db::execSQL("
		SELECT validvalue
		  FROM webset.glb_validvalues
		 WHERE valuename = 'TXMoreDates'
		   AND (glb_enddate IS NULL or now()< glb_enddate)
		 ORDER BY validvalue
	")->assocAll();

	for ($i = 0; $i < count($texts); $i++) {
		$helpButon->addItem($texts[$i]['validvalue'], '$("#txt01").val(' . json_encode($texts[$i]['validvalue']) . ')');
	}

	$edit->addGroup('General Information');
	$edit->addControl('Related Services')
		->sqlField('txt01')
		->name('txt01')
		->append(count($texts) > 0 ? $helpButon : '')
		->size(70);

	$edit->addControl('Date', 'date')
		->sqlField('dat01');

	$edit->addControl('Order #', 'integer')
		->sqlField('order_num')
		->value((int) db::execSQL("
	                    SELECT count(1)
	                      FROM webset.std_general
	                     WHERE area_id = " . $area_id . "
						   AND stdrefid = " . $tsRefID . "
	                ")->getOne() + 1
		)
		->size(20);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('Area ID', 'hidden')->value($area_id)->sqlField('area_id');

	$edit->finishURL = CoreUtils::getURL('meet_more.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('meet_more.php', array('dskey' => $dskey));

	$edit->printEdit();
?>