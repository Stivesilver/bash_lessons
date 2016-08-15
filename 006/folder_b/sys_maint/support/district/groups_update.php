<?php
    Security::init();

	$ids = io::get('ids');
	$forms = array();
	$forms[] = 0;
    foreach (explode(',', io::get('forms')) as $form) {
		if ($form > 0) {
			$forms[] = $form;
		}
	}

    $list = new ListClass();

    $list->title = 'Insert/Update Selected Procedures to Following Districts';
    $list->showSearchFields = true;

    $SQL = "
		SELECT scrdesc || ': ' || hspdesc,
		       md5(xml_test)
		  FROM webset.es_scr_disdef_proc proc
               INNER JOIN webset.es_statedef_screeningtype sarea ON sarea.scrrefid = proc.screenid
         WHERE hsprefid IN (" . implode(',', $forms) . ")
    ";
	$titles_arr = db::execSQL($SQL)->indexCol(0);
	$bodies_arr = db::execSQL($SQL)->indexCol(1);
	$titles_txt = "'" . implode("','", $titles_arr) . "'";
	$bodies_txt = "'" . implode("','", $bodies_arr) . "'";
                                     
    $list->SQL = "
        SELECT vnd.vndrefid,
               vndname, 
               COALESCE(form_title, 'Does not exist'),
               length(xml_test),               
               proc.lastuser,
               proc.lastupdate,
			   CASE WHEN md5(xml_test) IN (" . $bodies_txt . ") THEN 'Y' ELSE 'N' END as status,
               'XML'
	      FROM sys_vndmst vnd
			   LEFT OUTER JOIN (
			       SELECT scrdesc || ': ' || hspdesc as form_title, 
						  proc.hsprefid, 
						  proc.vndrefid, 
						  proc.xml_test,
						  proc.lastuser,
						  proc.lastupdate,
						  proc.recdeactivationdt
			         FROM webset.es_scr_disdef_proc proc 
			              INNER JOIN webset.es_statedef_screeningtype sarea ON sarea.scrrefid = proc.screenid 
			        WHERE scrdesc || ': ' || hspdesc IN (" . $titles_txt . ")
					  AND CASE WHEN NOW() > recdeactivationdt THEN 'N' ELSE 'Y' END = 'Y'
				) as proc ON vnd.vndrefid = proc.vndrefid
         WHERE ADD_SEARCH
		   AND vnd.vndrefid IN (" . $ids . ")
		 ORDER BY vndname, form_title
    ";

    $list->addSearchField('District', "LOWER(vndname)  like '%' || LOWER('ADD_VALUE') || '%'");
    $list->addSearchField('Area', 'sarea.scrrefid', 'select')
        ->sql("
			SELECT scrrefid,
	               scrdesc
	          FROM webset.es_statedef_screeningtype                   
	         WHERE screfid = " . VNDState::factory()->id . "	           
	         ORDER BY scrseq, 2
		");
    $list->addSearchField('Form', "LOWER(form_title)  like '%' || LOWER('ADD_VALUE') || '%'");
    $list->addSearchField("Text in Body (XML)", "LOWER(encode(decode(xml_test, 'base64'),'escape'))  like '%' || LOWER('ADD_VALUE')|| '%'");
    $list->addSearchField(FFIDEAStatus::factory())->sqlField("CASE WHEN NOW() > recdeactivationdt THEN 'N' ELSE 'Y' END");

    $list->addColumn('District');
    $list->addColumn('Procedure');
    $list->addColumn('Length');
    $list->addColumn('Last User');
    $list->addColumn('Last Update');
    $list->addColumn('Same to Current')->type('switch')->sqlField('status');

    $list->addRecordsProcess('Insert/Update')
        ->url(CoreUtils::getURL('groups_update.ajax.php', array('forms' => implode(',', $forms))))
        ->type(ListClassProcess::DATA_UPDATE)
        ->progressBar(false);

    $list->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.es_scr_disdef_proc')
            ->setKeyField('hsprefid')
            ->applyListClassMode()
    );

       
    $list->printList();
?>
