<?php
	Security::init();

	$list = new listClass();

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT mfcprefid,
               mfcpdesc,
               (SELECT COUNT(1)
                  FROM webset.statedef_forms
                 WHERE webset.statedef_forms.mfcprefid =  purp.mfcprefid
                   AND (recdeactivationdt IS NULL or now()< recdeactivationdt) )
          FROM webset.def_formpurpose purp
         WHERE (1=1) ADD_SEARCH
         ORDER BY mfcpdesc
    ";

	$list->title = "Form Purpose";

	$list->addSearchField("ID", "(mfcprefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField(FFSelect::factory("State"))
		->sql("
			SELECT staterefid,
                   state || ' - ' || statename
              FROM webset.glb_statemst state
             WHERE EXISTS (SELECT 1
                             FROM webset.statedef_forms forms
                            WHERE (recdeactivationdt IS NULL or now()< recdeactivationdt)
                              AND state.staterefid = forms.screfid)
             ORDER BY 1
		")
		->sqlField('EXISTS (SELECT 1 FROM webset.statedef_forms forms WHERE forms.mfcprefid = purp.mfcprefid AND screfid = ADD_VALUE)');

	$list->addColumn('ID')->sqlField('mfcprefid');
	$list->addColumn("Form Purpose", "", "text", "", "", "");
	$list->addColumn("Active Forms")->dataCallback('activeFroms')->dataHintCallback('activeFromsHints');

	$list->addURL = CoreUtils::getURL('./form_purpose_add.php');
	$list->editURL = CoreUtils::getURL('./form_purpose_add.php');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.def_guardiantype')
			->setKeyField('gtrefid')
			->applyListClassMode()
	);

	$list->printList();

	function activeFroms($data) {
		$count = db::execSQL("
			SELECT count(mfcrefid)
              FROM webset.statedef_forms
                   INNER JOIN webset.glb_statemst ON staterefid = screfid
             WHERE MFCpRefId = " . $data['mfcprefid'] . "
               AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
		")->getOne();

		return UIAnchor::factory($count)->toHTML();
	}

	function activeFromsHints($data) {
		$names = db::execSQL("
			SELECT state, mfcdoctitle
                FROM webset.statedef_forms
                     INNER JOIN webset.glb_statemst ON staterefid = screfid
               WHERE MFCpRefId = " . $data['mfcprefid'] . "
                 AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
               ORDER BY 1,2
		")->assocAll();
		$res = '';
		foreach ($names as $name) {
			$res .= $name['state'] . ' ' . $name['mfcdoctitle'] . "<br/>";
		}
		return $res;
	}

?>
