<?php

	Security::init();

	$RefID = io::get('RefID');
	$edit  = new EditClass('edit1', $RefID);
	$sql   = "
		SELECT progmod.stsrefid,
			   COALESCE(pcat.macdesc || ': ', '') || progmod.stsdesc
	      FROM webset.statedef_mod_acc progmod
	           LEFT OUTER JOIN webset.statedef_mod_acc_cat pcat ON pcat.macrefid = progmod.macrefid
	     WHERE progmod.stsrefid = $RefID
	       AND progmod.modaccommodationsw = 'Y'
    ";

	$result = db::execSQL($sql);

	$edit->title = "Edit Linkage";
	$edit->saveAndAdd = false;
	$edit->setSourceTable('webset.statedef_mod_acc', 'stsrefid');

	$edit->addGroup("General Information");

	$edit->addControl('Program Modifications and Accommodations', 'protected')
		->value($result->fields[1]);

	$edit->addControl('Assessment Accommodations', 'select')
		->sql("
			SELECT stsrefid,
                   TRIM(COALESCE(aacdesc, '')||': '||stsdesc, ': ')
              FROM webset.statedef_mod_acc macc
                   LEFT OUTER JOIN webset.statedef_assess_acc_cat cat ON cat.aacrefid = macc.aacrefid
             WHERE macc.screfid = " . VNDState::factory()->id . "
               AND UPPER(assessmentsw) = 'Y'
               AND (macc.recdeactivationdt IS NULL OR NOW() < macc.recdeactivationdt)
               AND macc.ids_assessments IS NULL
             ORDER BY stsseq, stscode, stsdesc
		")
		->emptyOption(true)
		->sqlField('ids_assessments')
		->name('ids_assessments');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');

	/*$edit->addSQLConstraint('Such Assessment already exists', "
                SELECT 1
                  FROM webset.statedef_mod_acc
                 WHERE vndrefid = VNDREFID
                   AND dwadesc = '[dwadesc]'
                   AND dwarefid != AF_REFID
    ");*/

	$edit->addSQLConstraint('Such Assessment already exists', "
        SELECT 1
          FROM webset.statedef_mod_acc
         WHERE screfid = " . VNDState::factory()->id . "
           AND ids_assessments = '[ids_assessments]'
           AND stsrefid != AF_REFID
    ");

	$edit->printEdit();

?>