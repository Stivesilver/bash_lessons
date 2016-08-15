<?php

	Security::init();

	$SQL = "
		SELECT stdrefid::varchar || UPPER(gdlnm) || UPPER(gdfnm)
		  FROM webset.dmg_guardianmst
		 GROUP BY stdrefid, UPPER(gdlnm), UPPER(gdfnm)
		HAVING count(1)>1
	";
	$names = db::execSQL($SQL)->indexCol(0);
	$big_word = db::escape(implode('__', $names));


	$SQL = "
		SELECT  nameg FROM (SELECT stdrefid::varchar || UPPER(gdlnm) || UPPER(gdfnm) as nameg
		  FROM webset.dmg_guardianmst
		 UNION ALL
		SELECT stdrefid::varchar || UPPER(gdfnm) || UPPER(gdlnm) as nameg
		  FROM webset.dmg_guardianmst
		) as t
		 GROUP BY t.nameg
		HAVING count(1)>1
	";
	$names = db::execSQL($SQL)->indexCol(0);
	$big_versa = db::escape(implode('__', $names));

	$list = new listClass();

	$list->customSearch = "yes";
	$list->showSearchFields = "yes";
	$list->title = "Duplicated Guardians by Name";
	$list->multipleEdit = "no";
	$list->SQL = "
		SELECT gdrefid,
               'Student: ' || std.stdlnm || ', ' || std.stdfnm,
               grd.gdlnm || ', ' || grd.gdfnm,
               grd.lastuser,
               grd.lastupdate,
               stdstatus,
               std_deleted_sw
          FROM webset.dmg_studentmst std
               INNER JOIN webset.dmg_guardianmst grd ON std.stdrefid = grd.stdrefid
               LEFT OUTER JOIN webset.sys_teacherstudentassignment ts ON std.stdrefid = ts.stdrefid
         WHERE std.vndrefid = VNDREFID
               ADD_SEARCH
         ORDER BY UPPER(stdlnm), UPPER(stdfnm)
	";

	$list->addSearchField(FFStudentName::factory());
	$list->addSearchField(
		FFSelect::factory('Search Mode')
			->data(
				array('1' => 'Duplicated Names', '2' => 'Vise Versa Names')
			)
			->value('1')
			->sqlField("
				CASE
					WHEN ('$big_versa' LIKE '%' || std.stdrefid::varchar || UPPER(gdfnm) || UPPER(gdlnm) || '%' OR '$big_versa' LIKE '%' || std.stdrefid::varchar || UPPER(gdlnm) || UPPER(gdfnm) || '%') THEN '2'
					WHEN '$big_word' LIKE '%' || std.stdrefid::varchar || UPPER(gdlnm) || UPPER(gdfnm) || '%' THEN '1'
					ELSE '0'
				END
			")
		);

	$list->addColumn("Student", "", "group");
	$list->addColumn("Guardian");
	$list->addColumn("Guardian Last User", "");
	$list->addColumn("Guardian Last Update", "");

	$list->deleteTableName = 'webset.dmg_guardianmst';
	$list->deleteKeyField = 'gdrefid';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.dmg_guardianmst')
			->setKeyField('gdrefid')
			->applyListClassMode()
	);

	$list->printList();

?>
