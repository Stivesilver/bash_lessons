<?php

	Security::init();

	$dskey       = io::get('dskey');
	$ds          = DataStorage::factory($dskey, true);
	$stdIEPYear  = $ds->get('stdIEPYear');
	$tsRefID  = $ds->get('tsRefID');
	$builderTree = new IDEAMenuBuilder($stdIEPYear, $tsRefID);
	$tree        = $builderTree->generateTree();

	$tree->toAJAX();

?>
