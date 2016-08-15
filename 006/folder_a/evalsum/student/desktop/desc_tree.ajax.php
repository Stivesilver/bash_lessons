<?php

	Security::init();
	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey, true);
	$evalproc_id = $ds->get('evalproc_id');
	$tsRefID = $ds->get('tsRefID');
	$builderTree = new IDEAMenuBuilder($evalproc_id, $tsRefID, io::get('screenID'));
	$tree = $builderTree->generateTree(false, 'eval_proc_title');
	$tree->expand(true);

	$tree->toAJAX();

?>
