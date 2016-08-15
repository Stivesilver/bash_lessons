<?php

	Security::init();
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$spconsid = io::geti('spconsid');
	if (isset($spconsid) && $spconsid > 0) {
		DBImportRecord::factory('webset.std_spconsid', 'sscmrefid')
			->key('sscmrefid', $spconsid)
			->set('stdrefid', $tsRefID)
			->set('saveapp', 'Y')
			->set('lastuser', db::escape(SystemCore::$userUID))
			->set('lastupdate', 'NOW()', true)
			->import();
	}

	$tabs = new UITabs('tabs');

	$tabs->addTab('Decision')
		->url(CoreUtils::getURL('./esy_main.php', array('dskey' => $dskey)));

	$tabs->addTab('Participants')
		->url(CoreUtils::getURL('./meet_participants.php', array('dskey' => $dskey)));

	$tabs->addTab('Services')
		->url(CoreUtils::getURL('./esy_list.php', array('dskey' => $dskey)));

	print $tabs->toHTML();
?>
