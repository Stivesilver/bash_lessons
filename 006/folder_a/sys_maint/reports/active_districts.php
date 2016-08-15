<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Active Districts';
	$list->showSearchFields = true;
	$list->printable = true;
	$list->hideCheckBoxes = false;

	$list->SQL = "
        SELECT vndrefid,
        	   vndname as district_name,
        	   vndstate,
	           (SELECT vourefid FROM sys_voumst o WHERE o.vndrefid = t.vndrefid ORDER BY voumain = 'Y' DESC LIMIT 1) as main_location,
		       (SELECT count(1) FROM sys_voumst o WHERE o.vndrefid = t.vndrefid) as schools_count,
		       (SELECT count(1) FROM sys_usermst o WHERE o.vndrefid = t.vndrefid) as users_count,
		       (SELECT count(1) FROM webset.dmg_studentmst o WHERE o.vndrefid = t.vndrefid) as students_count
		  FROM sys_vndmst t
		 WHERE vndstatus = 'Y'
		   AND COALESCE(vndstate, '') in ('CT', 'MO', 'TX', 'ID','IL', 'IN', 'KS', 'OH')
		   AND t.vndrefid IN (SELECT a.vndrefid
		                        FROM sys_vndapp a
		                       WHERE vapstartdate <= current_date AND vapenddate >= current_date)
		   AND EXISTS (SELECT 1
		  				 FROM sys_usermst u
		 				WHERE umlastlogindt > (now() - interval '3 months')
		   				  AND t.vndrefid = u.vndrefid)

		   AND vndrefid > 1 and vndname NOT LIKE '%Demo%'
		 ORDER BY vndname

    ";

	$list->addSearchField("ID", "(vndrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField('District Name', 'vndname');

	$list->addColumn('ID')->sqlField('vndrefid');
	$list->addColumn("District Name");
	$list->addColumn("State");
	$list->addColumn("Main Location");
	$list->addColumn("Locations Number");
	$list->addColumn("Users Number");
	$list->addColumn("Students Number");

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('public.sys_vndmst')
			->setKeyField('vndrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
