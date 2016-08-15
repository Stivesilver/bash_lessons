<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$list = new ListClass();

	$list->title = 'Services to be provided during Extended School Year';

	$list->SQL = "
        SELECT sesysdrefid ,
               COALESCE(desddesc || ': ' || other, desddesc),
			   CASE WHEN LOWER(esfumdesc) LIKE '%other%' THEN sesysdservicefreqother ELSE esfumdesc END,
               deslddesc,
               sesysdservicebegdate,
               sesysdserviceenddate
          FROM webset.std_esy_service_dtl std
               INNER JOIN webset.disdef_esy_services srv ON srv.desdrefid = std.serv_id
               INNER JOIN webset.statedef_esy_serv_freq_desc freq ON  freq.esfdrefid = std.sesysdservicefreqrefid
               INNER JOIN webset.statedef_esy_serv_freq_unit_of_measur meas ON meas.esfumrefid = std.sesysdservicefrequomrefid
               INNER JOIN webset.disdef_esy_serv_loc loc ON loc.desldrefid = std.sesysdservicelocationrefid
         WHERE stdrefid = " . $tsRefID . "
		   AND iepyear = $stdIEPYear
         ORDER BY desddesc
    ";

	$list->addColumn("ESY Service");
	$list->addColumn("Frequency Desc");
	$list->addColumn("Location");
	$list->addColumn("Initiation Date")->type('date');
	$list->addColumn("Ending Date")->type('date');

	$list->addURL = CoreUtils::getURL('esy_add.php', array('dskey' => $dskey, 'iep' => 1));
	$list->editURL = CoreUtils::getURL('esy_add.php', array('dskey' => $dskey, 'iep' => 1));

	$list->deleteTableName = "webset.std_esy_service_dtl";
	$list->deleteKeyField = "sesysdrefid";

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$list->printList();
?>
