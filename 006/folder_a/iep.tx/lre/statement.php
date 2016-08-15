<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$list = new ListClass();

	$list->title = 'Consideration of Least Restrictive Environment';
	
	$list->hideNumberColumn = TRUE;

	$list->SQL = "
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
		 WHERE EXISTS (
			SELECT 1 
			  FROM webset_tx.std_lre_statements std
			 WHERE stdrefid=" . $tsRefID . "
	           AND iep_year = " . $stdIEPYear . " 
			   AND std.area = state.area
		 ) 
		 ORDER BY 2
    ";

	$list->addColumn('Question');

	$list->addURL = CoreUtils::getURL('statement_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('statement_add.php', array('dskey' => $dskey));

	$list->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);
	
	$list->addRecordsProcess('Delete')
		->message('Do you really want to delete this Record?')
		->url(CoreUtils::getURL('statement_delete.ajax.php', array('dskey' => $dskey)))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false);

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

	$list->getButton(ListClassButton::ADD_NEW)
		->disabled($notAnswered == 0);

	$list->printList();
?>