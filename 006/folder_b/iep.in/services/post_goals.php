<?php

	Security::init();	
	
    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
   
	$list = new ListClass();
	
	$list->title = 'Post Secondary Goals';
	
	$list->SQL = "
		SELECT refid,
			   gdssdesc,
			   gsptext || ' ' || gdskgaaction || ' ' || gdskgccontent  || ' ' || COALESCE(crbasis || ' ','') || cdesc,
			   itstrue,
			   sequence
		  FROM webset.std_in_postgoals std
		   	   LEFT OUTER JOIN  webset.disdef_bgb_goaldomainscope scope ON scope.gdsrefid = std.scope
			   LEFT OUTER JOIN  webset.disdef_bgb_goalsentencepreface preface ON preface.gsfrefid = std.preface
			   LEFT OUTER JOIN  webset.disdef_bgb_ksaksgoalactions action ON action.gdskgarefid = std.action
			   LEFT OUTER JOIN  webset.disdef_bgb_scpksaksgoalcontent content ON content.gdskgcrefid = std.content
			   LEFT OUTER JOIN  webset.disdef_bgb_ksaconditions condition ON condition.crefid = std.condition
		 WHERE stdrefid = " . $tsRefID . "
		 ORDER BY gdssdesc, sequence, 2
	";

	$list->addColumn('Area');
	$list->addColumn('Goals');
	$list->addColumn('Reviewed');
	$list->addColumn('Sequence');

    $list->addURL = CoreUtils::getURL('post_goals_add.php', array('dskey' => $dskey));
    $list->editURL = CoreUtils::getURL('post_goals_add.php', array('dskey' => $dskey));

    $list->deleteTableName = 'webset.std_in_postgoals';
    $list->deleteKeyField = 'refid';

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