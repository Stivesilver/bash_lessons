<?php

	function saveAnswers($RefID, &$data) {

		$quests = db::execSQL("
	        SELECT refid,
	               validvalue
	          FROM webset.disdef_validvalues
	         WHERE vndrefid = VNDREFID
	           AND valuename = 'IN_IRead'
	           AND (CASE glb_enddate<now() WHEN TRUE THEN 2 ELSE 1 END) = '1'
	         ORDER BY valuename, sequence_number, validvalue ASC
		")->assocAll();

		$values = "<record>" . PHP_EOL;
		foreach ($quests as $question) {
			$name = 'question_' . $question['refid'];
			$values .= '<' . $name . '>' . io::post($name) . '</' . $name . '>'  . PHP_EOL;
		}
		$values .= "</record>" . PHP_EOL;

		$dbrec = DBImportRecord::factory('webset.std_general', 'refid')
			->key('refid', $RefID)
			->set('txt02', $values)
			->set('lastuser', SystemCore::$userUID)
			->set('lastupdate', 'NOW()', true)
			->import();

	}

?>
