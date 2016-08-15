<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::get('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit Efforts';

	$edit->setSourceTable('webset_tx.std_lre_efforts', 'refid');

	$edit->addGroup('Assessments');

	$edit->addControl('Efforts/Options', 'select')
		->sqlField('erefid')
		->name('erefid')
		->sql(
			$RefID == '0' ?
			"
			SELECT refid, 
			       edesc				   
			  FROM webset_tx.def_lre_efforts
			 WHERE NOT EXISTS (SELECT 1
                                 FROM webset_tx.std_lre_efforts
                                WHERE stdrefid = " . $tsRefID . "
                                  AND iep_year = " . $stdIEPYear . "
                                  AND smode = 'E'
                                  AND erefid = webset_tx.def_lre_efforts.refid)
		     ORDER BY CASE edesc WHEN 'Other:' THEN 2 ELSE 1 END, seqnum, edesc
			"
			:
			"
			SELECT refid, 
			       edesc				   
			  FROM webset_tx.def_lre_efforts
			 WHERE EXISTS (SELECT 1
                             FROM webset_tx.std_lre_efforts
                            WHERE refid = " . $RefID . "								
                              AND erefid = webset_tx.def_lre_efforts.refid)
			"
		)
		->req();

	$edit->addControl('Specify')
		->sqlField('other')
		->name('other')
		->showIf('erefid', db::execSQL("
                                  SELECT refid
                                    FROM webset_tx.def_lre_efforts
                                   WHERE SUBSTRING(LOWER(edesc), 1, 5) = 'other'
                                 ")->indexAll())
		->size(50);

	$edit->addControl('Skill Level', 'select_radio')
		->sqlField('mark')
		->name('mark')
		->data(array('S'=>'Successful', 'U'=>'Unsuccessful', ''=>'N/A'));
	
	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iep_year');
	$edit->addControl('Mode', 'hidden')->value('E')->sqlField('smode');

	$edit->finishURL = CoreUtils::getURL('efforts.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('efforts.php', array('dskey' => $dskey));
	
	$notAnswered = db::execSQL("
		SELECT count(1)
		  FROM webset_tx.def_lre_efforts
		 WHERE NOT EXISTS (SELECT 1
						     FROM webset_tx.std_lre_efforts
						    WHERE stdrefid = " . $tsRefID . "
							  AND iep_year = " . $stdIEPYear . "
							  AND smode = 'E'
							  AND erefid = webset_tx.def_lre_efforts.refid)
	")->getOne();
	
	if ($RefID == 0 && $notAnswered > 1 || $RefID > 0 && $notAnswered > 0 ) {
		$edit->saveAndAdd = true;
	} else {
		$edit->saveAndAdd = false;
	}

	$edit->printEdit();
?>
<script type="text/javascript">   
    var edit1 = EditClass.get();
    edit1.onSaveDoneFunc(
        function(refid) {
            if ($('input[name="RefID"]').val() == 0) {
                api.reload();
				
            }            
        }
    )
</script>
