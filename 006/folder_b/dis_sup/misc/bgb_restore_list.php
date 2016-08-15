<?php

	Security::init();

	$list = new listClass();

	$list->customSearch     = "yes";
	$list->showSearchFields = "yes";
	$list->title 			= "Restore Deleted Baseline/Goal/Benchmarks";
	$list->multipleEdit 	= "no";
	$list->SQL				= "
		CREATE TEMPORARY TABLE temp_bgb_deleted_goals AS
		SELECT gprefid,
			   count(1) AS cnt
		  FROM webset.std_bgb_goal AS g
		 WHERE g.gprefid IS NOT NULL
		 GROUP BY g.gprefid;
		CREATE INDEX 
			ON temp_bgb_deleted_goals
		 USING btree ( gprefid );
		CREATE TEMPORARY TABLE temp_bgb_deleted_benchmarks AS
		SELECT COALESCE(g.blrefid, g.gprefid) as gprefid,
			   count(1) AS cnt
		  FROM webset.std_bgb_goal AS g
			   INNER JOIN webset.std_bgb_benchmark AS b ON g.grefid = b.bprefid
		 WHERE b.bprefid IS NOT NULL
		 GROUP BY COALESCE(g.blrefid, g.gprefid);
		CREATE INDEX 
			ON temp_bgb_deleted_benchmarks
		 USING btree ( gprefid );
		SELECT blrefid,
		       stdlnm || ' ' || stdfnm,
		       TO_CHAR (siymiepbegdate, 'mm/dd/yyyy') || ' - ' ||TO_CHAR(siymiependdate, 'mm/dd/yyyy') AS iepyear,
		       domain.gdsdesc || '/' || scope.gdssdesc || '/' || ksa.gdsksdesc,
		       bl.blbaseline,
		       CASE
		       WHEN bl.stdschoolyear IS NULL THEN 0
		       ELSE 1
		       END,
		       COALESCE(tmpg.cnt,0),
		       COALESCE(tmpb.cnt,0),
		       bl.lastuser,
		       bl.lastupdate
		  FROM webset.std_bgb_baseline bl
		       INNER JOIN webset.std_iep_year iep ON COALESCE(bl.siymrefid, bl.stdschoolyear) = iep.siymrefid
		       INNER JOIN webset.sys_teacherstudentassignment ts ON tsrefid = iep.stdrefid
		       INNER JOIN webset.disdef_bgb_goaldomainscopeksa ksa ON bl.blksa = ksa.gdskrefid
		       INNER JOIN webset.disdef_bgb_goaldomainscope scope ON ksa.gdsrefid = scope.gdsrefid
		       INNER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid
		       INNER JOIN webset.dmg_studentmst dmg ON dmg.stdrefid = ts.stdrefid
			   LEFT JOIN temp_bgb_deleted_goals tmpg ON bl.blrefid = tmpg.gprefid
			   LEFT JOIN temp_bgb_deleted_benchmarks tmpb ON bl.blrefid = tmpb.gprefid
		 WHERE (
				   bl.siymrefid IS NULL
				OR EXISTS (
					SELECT 1
					  FROM temp_bgb_deleted_goals
					 WHERE gprefid = bl.blrefid
				   )
				OR EXISTS (
					SELECT 1
					  FROM temp_bgb_deleted_benchmarks
					 WHERE gprefid = bl.blrefid
				   )
		       )
		   AND dmg.vndrefid = VNDREFID ADD_SEARCH
		 ORDER BY upper(stdlnm), upper(stdfnm), iep.siymiepbegdate DESC, blrefid
	";

	$list->addSearchField(FFStudentName::factory());
	
	$list->addColumn("Student", "");
	$list->addColumn("IEP Year");
	$list->addColumn("Area", "");
	$list->addColumn("Baseline", "");
	$list->addColumn("Deleted Baseline", "");
	$list->addColumn("Deleted Goals", "");
	$list->addColumn("Deleted Benchmarks", "");
	$list->addColumn("Deleted By", "");
	$list->addColumn("Deleted On", "");

	$list->addButton(
		FFIDEAExportButton::factory()
		->setTable('webset.std_bgb_baseline')
		->setKeyField('blrefid')
		->applyListClassMode()
	);
	$list->addRecordsProcess('Restore')
		->message('Do you really want to restore disabled Baselines?')
		->url(CoreUtils::getURL('bgb_restore.ajax.php'))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false); 
	$list->printList();

?>
