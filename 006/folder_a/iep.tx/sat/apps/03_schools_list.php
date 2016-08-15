<?php

	Security::init();

	$dskey      = io::get('dskey');
	$RefID      = io::geti('RefID');
	$ds         = DataStorage::factory($dskey, true);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = $ds->safeGet('tsRefID');
	$list       = new ListClass();

	$list->SQL = "
		SELECT refid,
          	   school,
       	       district,
       	       dates,
               grades,
          	   seqnum
      	  FROM webset_tx.std_sat_schools  std
       	 WHERE stdrefid = $tsRefID
      	 ORDER BY seqnum, refid
        ";

	$list->addColumn("School");
	$list->addColumn("District/State/Country");
	$list->addColumn("Dates of Enrollment");
	$list->addColumn("Grade Level");
	$list->addColumn("Sequence");

	$list->title           = "Previous Schools Attended";
	$list->addURL          = CoreUtils::getURL('03_schools_edit.php', array('dskey' => $dskey));
	$list->editURL         = CoreUtils::getURL('03_schools_edit.php', array('dskey' => $dskey));
	$list->deleteTableName = "webset_tx.std_sat_schools";
	$list->deleteKeyField  = "refid";

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->printList();

?>