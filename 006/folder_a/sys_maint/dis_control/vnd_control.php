<?php

	Security::init();

	define("SQL_EXISTS_IN_STATE_ONLY", "
		       (
				   EXISTS (
					SELECT 1
					  FROM webset.statedef_discontrol AS statedef
					       INNER JOIN webset.glb_statemst AS state ON state.staterefid = statedef.screfid
					 WHERE statedef.dcrefid = def.dcrefid
					   AND state.state = '" . VNDState::factory()->code . "'
				   )
				OR NOT EXISTS (
					SELECT 1
					  FROM webset.statedef_discontrol AS statedef
					 WHERE statedef.dcrefid = def.dcrefid
				   )
			   )
	");

	$list = new ListClass();
	$list->title = 'District Control';

	$list->multipleEdit = false;
	$list->showSearchFields = true;
	$list->hideNumberColumn = true;

	$list->SQL = "
		SELECT dcrefid,
		       dcdesc,
		       CASE dckey
		       WHEN 'SQL' THEN displvalue
		       WHEN 'SQL_CHECK' THEN array_to_string(
				           ARRAY(
				           SELECT screen.scrdesc
							 FROM webset.es_statedef_screeningtype screen
							WHERE ',' || paramvalue || ',' LIKE '%,' || scrrefid ::varchar || ',%'
							ORDER BY screen.scrseq
				           ),
				           ', '
				       )
		       WHEN 'TEXT' THEN paramvalue
		       WHEN 'TEXTAREA' THEN paramvalue
		       ELSE validvalue
		       END AS dckey,
		       sdc.name
		  FROM webset.def_discontrol AS def
		       LEFT OUTER JOIN webset.disdef_control AS disdef ON def.dcrefid = disdef.defrefid
		   AND disdef.vndrefid = VNDREFID
		       LEFT OUTER JOIN webset.glb_validvalues AS glb ON glb.validvalueid = disdef.paramvalue
		   AND glb.valuename = dckey
		       LEFT JOIN webset.statedef_discontrol_cat AS sdc ON def.sdcatrefid = sdc.sdcatrefid
		 WHERE " . SQL_EXISTS_IN_STATE_ONLY . " ADD_SEARCH
		 ORDER BY sdc.order_num, sdc.name, def.dcdesc
	";

	$list->addSearchField("ID", "(dcrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField('Option', '')
		->sqlField("dcdesc ILIKE '%ADD_VALUE%'");

	$list->addSearchField(FFSelect::factory("Category"))
		->sql("
			SELECT DISTINCT
			    ON (order_num, name) sdc.sdcatrefid,
			       name
			  FROM webset.statedef_discontrol_cat AS sdc
			       INNER JOIN webset.def_discontrol AS def ON def.sdcatrefid = sdc.sdcatrefid
			 WHERE " . SQL_EXISTS_IN_STATE_ONLY . "
	    ")
		->sqlField('def.sdcatrefid')
		->value(io::get('category'));

	$list->addColumn('Category', '', 'group')->sqlField('name');
	$list->addColumn('ID')->sqlField('dcrefid');
	$list->addColumn('District Control Option')->sqlField('dcdesc');
	$list->addColumn('Value')->sqlField('dckey');

	$list->editURL = CoreUtils::getURL('./vnd_control_add.php', array('category' => io::get('category')));

	$list->addButton(
		FFIDEAExportButton::factory()
		->setTable('webset.disdef_control')
		->setKeyField('defrefid')
		->applyListClassMode()
	);

	$list->printList();
?>
