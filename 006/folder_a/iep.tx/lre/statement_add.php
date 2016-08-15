<?php
	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::get('RefID');
	$QuestionID = io::get('QuestionID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit Consideration of Least Restrictive Environment';

	$edit->addGroup('Area');

	$SQL = $RefID == '0' ? "
			SELECT DISTINCT
				   area,
				   CASE area WHEN 'AFFECT' 			THEN '2. Describe How The Disability Affects'
                    		 WHEN 'DETERMINATION' 	THEN '3. Determination'
                    	     WHEN 'REMOVAL' 		THEN '4. Removal From General Education Classroom'
                    		 WHEN 'CAMPUS' 			THEN '5. Removal From General Education Campus'
                    		 WHEN 'PARTICIPATION' 	THEN '6a. Opportunity To Participate'
                             WHEN 'PARTICIPATION2' 	THEN '6b. Opportunity To Participate'
                    		 WHEN 'EFFECTSSTUDENT' 	THEN '7a. Effects on the Student'
                    		 WHEN 'EFFECTSERVICES' 	THEN '7b. Effects on the Quality of Services'
                    END
			  FROM webset_tx.def_lre_statement state
			 WHERE NOT EXISTS (
			     SELECT 1 
				   FROM webset_tx.std_lre_statements std
				  WHERE stdrefid=" . $tsRefID . "
					AND iep_year = " . $stdIEPYear . " 
					AND std.area = state.area
				)
			 ORDER BY 2
	" : "
			SELECT DISTINCT
				   area,
				   CASE area WHEN 'AFFECT' 			THEN '2. Describe How The Disability Affects'
                    		 WHEN 'DETERMINATION' 	THEN '3. Determination'
                    	     WHEN 'REMOVAL' 		THEN '4. Removal From General Education Classroom'
                    		 WHEN 'CAMPUS' 			THEN '5. Removal From General Education Campus'
                    		 WHEN 'PARTICIPATION' 	THEN '6a. Opportunity To Participate'
                             WHEN 'PARTICIPATION2' 	THEN '6b. Opportunity To Participate'
                    		 WHEN 'EFFECTSSTUDENT' 	THEN '7a. Effects on the Student'
                    		 WHEN 'EFFECTSERVICES' 	THEN '7b. Effects on the Quality of Services'
                    END
			  FROM webset_tx.def_lre_statement state
			 WHERE area = '" . $RefID . "'
	";

	if ($QuestionID == '') {
		$QuestionID = db::execSQL($SQL)->getOne();
	}

	$edit->addControl('Question', 'select')
		->name('area')
		->value($QuestionID)
		->onChange('javascript:api.goto(api.url("statement_add.php", {"dskey" : "' . $dskey . '", "RefID" : "0", "QuestionID" : this.value}))')
		->sql($SQL)
		->req();

	$controls = db::execSQL("
		SELECT drefid,
			   mst.srefid,
			   stmtext,
			   dtltext,
			   mst.othersw as mst_other,
			   dtl.othersw as dtl_other,
			   chckmode
		  FROM webset_tx.def_lre_statement mst
			   LEFT OUTER JOIN webset_tx.def_lre_statementdtl dtl on mst.srefid = dtl.srefid
		 WHERE area = '" . $QuestionID . "'
		 ORDER BY mst.seqnum, dtl.seqnum
	")->assocAll();

	$values = db::execSQL("
		SELECT all_objects
	      FROM webset_tx.std_lre_statements
	     WHERE stdrefid=" . $tsRefID . "
	       AND iep_year = $stdIEPYear
	       AND area = '" . $QuestionID . "'
	")->getOne();

	$id_mst = '';
	$id_dtl = '';
	for ($i = 0; $i < count($controls); $i++) {

		if ($id_mst != $controls[$i]['srefid']) {

			if ($controls[$i]['chckmode'] == 'D') {
				$control = FFSwitchYN::factory()
					->name('main_' . $controls[$i]['srefid'])
					->value(findValue('main_' . $controls[$i]['srefid'], $values))
				->append($controls[$i]['stmtext']);
			} else {
				$control = FFCheckBoxList::factory()
					->displaySelectAllButton(false)
					->name('main_' . $controls[$i]['srefid'])
					->value(findValue('main_' . $controls[$i]['srefid'], $values))
					->data(array('checked' => $controls[$i]['stmtext']));
			}

			$edit->addControl(
				$control
			);

			if ($controls[$i]['mst_other'] == 'Y') {
				$edit->addControl('', 'textarea')
					->name('main_other_' . $controls[$i]['srefid'])
					->value(findValue('main_other_' . $controls[$i]['srefid'], $values))
					->css('width', '60%')
					->css('height', '50px');
			}

			$id_mst = $controls[$i]['srefid'];
		}

		if ($id_dtl != $controls[$i]['drefid'] && $controls[$i]['drefid'] != '') {

			$edit->addControl(
				FFCheckBoxList::factory()
					->displaySelectAllButton(false)
					->name('second_' . $controls[$i]['drefid'])
					->value(findValue('second_' . $controls[$i]['drefid'], $values))
					->data(array('checked' => $controls[$i]['dtltext']))
			)->prepend(UILayout::factory()->addHTML('', '50px'));


			if ($controls[$i]['dtl_other'] == 'Y') {
				$edit->addControl('', 'textarea')
					->name('second_other_' . $controls[$i]['drefid'])
					->value(findValue('second_other_' . $controls[$i]['drefid'], $values))
					->css('width', '60%')
					->css('height', '50px')
					->prepend(UILayout::factory()->addHTML('', '50px'));
			}

			$id_dtl = $controls[$i]['drefid'];
		}
	}

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iep_year');

	$edit->finishURL = CoreUtils::getURL('statement.php', array('dskey' => $dskey));
	$edit->finishURL = CoreUtils::getURL('statement.php', array('dskey' => $dskey));
	$edit->saveURL = CoreUtils::getURL('statement_save.php', array('dskey' => $dskey, 'QuestionID' => $QuestionID));

	$notAnswered = db::execSQL("
		SELECT DISTINCT
			   area
		  FROM webset_tx.def_lre_statement state
		 WHERE NOT EXISTS (
			SELECT 1 
			  FROM webset_tx.std_lre_statements std
			 WHERE stdrefid=" . $tsRefID . "
	           AND iep_year = " . $stdIEPYear . " 
			   AND std.area = state.area
			 )
	")->recordCount();

	if ($RefID == '0' && $notAnswered > 1) {
		$edit->saveAndAdd = true;
	} else {
		$edit->saveAndAdd = false;
	}

	$edit->printEdit();

	function findValue($obj, $values) {
		preg_match("/" . $obj . "\|(.+?)!!!/", $values, $out);
		return isset($out[1]) ? $out[1] : '';
	}
?>
<script type="text/javascript">
		var edit1 = EditClass.get();
		edit1.onSaveDoneFunc(
			function(refid) {
				if ($('input[name="RefID"]').val() == '0') {
					alert(1);
					api.reload();
				}
			}
		)
</script>