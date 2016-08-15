<?php

	Security::init();

	$as_refid = io::geti('as_refid');

	$list = new ListClass('Indicator');

	$list->title = 'Indicators';

	$list->SQL = "
		SELECT ind_refid,
			   ind_symbol,
			   ind_desc,
			   met_mastery
		  FROM webset.std_bgb_indicator
		 WHERE as_refid = $as_refid
		   AND vndrefid = VNDREFID
	";

	$list->addColumn('Indicator Symbol')
		->sqlField('ind_symbol');

	$list->addColumn('Description')
		->sqlField('ind_desc');

	$list->addColumn('Met Mastery')
		->sqlField('met_mastery');

	$list->addURL = CoreUtils::getURL('./bgb_indicator_edit.php', array('as_refid' => $as_refid));
	$list->editURL = CoreUtils::getURL('./bgb_indicator_edit.php', array('as_refid' => $as_refid));

	$list->deleteTableName = 'webset.std_bgb_indicator';
	$list->deleteKeyField = 'ind_refid';

	$list->printList();
?>