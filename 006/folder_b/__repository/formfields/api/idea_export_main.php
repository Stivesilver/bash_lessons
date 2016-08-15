<?php

    Security::init();

	$export_dskey = io::get('export_dskey');
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($export_dskey);
	$xmlTemplate = $ds->get('xmlTemplate');

	$tabs = new UITabs('tabs');
	$tabs->indent(10);
	$tabs->addTab('INSERT', CoreUtils::getURL('./idea_export_dtl.php', array_merge($_GET, array('mode' => 'insert'))));
	$tabs->addTab('UPDATE', CoreUtils::getURL('./idea_export_dtl.php', array_merge($_GET, array('mode' => 'update'))));
	$tabs->addTab('SELECT', CoreUtils::getURL('./idea_export_dtl.php', array_merge($_GET, array('mode' => 'select'))));
	$tabs->addTab('LIST', CoreUtils::getURL('./idea_export_list.php', array_merge($_GET, array('mode' => 'select'))));
	if ($dskey != '') {
		$tabs->addTab('ACTUAL', CoreUtils::getURL('idea_export_dtl.php', array_merge($_GET, array('mode' => 'actual'))));
	}
	if ($xmlTemplate != '') {
		$tabs->addTab('XML', CoreUtils::getURL('./idea_export_xml.php', $_GET));
	}
	$tabs->addTab('TTL', CoreUtils::getURL('./idea_export_ttl.php', $_GET));
	$tabs->addTab('ALTER', CoreUtils::getURL('./idea_export_ttl.php', array_merge($_GET, array('alter' => 'yes'))));
	$tabs->addTab('BACKUP', CoreUtils::getURL('./idea_export_ttl.php', array_merge($_GET, array('data' => 'yes'))));
	if (SystemCore::$coreVersion == '1') {
		$tabs->addTab('SQL', CoreUtils::getURL('/applications/webset/support/sql_login.php'));
	}

    print $tabs->toHTML();
?>
