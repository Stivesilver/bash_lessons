<?php

    Security::init();
	IDEAFormat::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $screenURL = $ds->safeGet('screenURL');
	$set_id = IDEAFormat::get('id');
	$set_ini = IDEAFormat::getIniOptions();

    $edit = new EditClass('edit1', $tsRefID);

    $edit->title = 'Classroom Accommodations';

    $edit->addGroup('General Information');

    $edit->addControl('Classroom Accommodations', 'select_check')
        ->name('progmods')
        ->value(
            implode(',', db::execSQL("
        		SELECT stsrefid
        		  FROM webset.std_srv_progmod
        		 WHERE stdrefid = " . $tsRefID . "
				   AND stsrefid IS NOT NULL
        	")->indexCol(0))
        )
        ->sql("
			SELECT refid,
				   pmdesc
			  FROM webset.disdef_progmod t0
				   INNER JOIN webset.disdef_progmodcat t1 ON t1.catrefid = t0.catrefid
			 WHERE categor = 'Classroom Accommodations'
			   AND (t0.enddate IS NULL or now()< t0.enddate)
			   AND t0.vndrefid = VNDREFID
			 ORDER BY 2
        ")
        ->breakRow(true)
        ->req();

    $edit->addControl('Specify if Other', 'textarea')
        ->name('other')
        ->value(
            db::execSQL("
        		SELECT ssmshortdesc
        		  FROM webset.std_srv_progmod
        		 WHERE stdrefid = " . $tsRefID . "
				   AND stsrefid IS NULL
        	")->getOne()
        );

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected');
    $edit->addControl('Last Update', 'protected');

    $edit->setPostsaveCallback('savePurpose', 'classroom.inc.php');
    $edit->finishURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));
    $edit->cancelURL = CoreUtils::getURL($screenURL, array('dskey' => $dskey));

    $edit->saveAndAdd = false;
    $edit->saveAndEdit = true;
	$edit->topButtons = true;
    $edit->firstCellWidth = '30%';

    $edit->addButton(
        FFIDEAExportButton::factory()
            ->setTable('webset.std_srv_progmod')
            ->setKeyField('stdrefid')
            ->applyEditClassMode()
    );

    $edit->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

    $edit->printEdit();
?>
