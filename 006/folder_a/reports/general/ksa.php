<?php

	Security::init();

	$list = new listClass();
	$list->title = 'Domain/Scope/Key Skill Area';
	$list->showSearchFields = true;
	$list->printable = true;

	$state = VNDState::factory()->code;

	$list->SQL = "
       SELECT webset.disdef_bgb_goaldomain.gdsdesc AS firstletter,
    			   ' ' || webset.disdef_bgb_goaldomain.gdsdesc || ' - ' || webset.disdef_bgb_goaldomainscope.gdssdesc || ' - ' || gdsksdesc AS desc
             FROM  webset.disdef_bgb_goaldomainscopeksa,
 				   webset.disdef_bgb_goaldomain,
				   webset.disdef_bgb_goaldomainscope
     	 	 WHERE webset.disdef_bgb_goaldomainscopeksa.gdsrefid = webset.disdef_bgb_goaldomainscope.gdsrefid and
				   webset.disdef_bgb_goaldomainscope.gdrefid = webset.disdef_bgb_goaldomain.gdrefid and
				   webset.disdef_bgb_goaldomain.vndrefid = " . $_SESSION["s_VndRefID"] ."
             ORDER BY webset.disdef_bgb_goaldomain.gdsdesc,
                      webset.disdef_bgb_goaldomainscope.gdssdesc,
				      gdsksdesc
    ";

	$list->addSearchField('Name', "(lower(webset.disdef_bgb_goaldomain.gdsdesc)  like '%' || lower(ADD_VALUE::varchar)|| '%' or lower(webset.disdef_bgb_goaldomainscope.gdssdesc)  like '%' || lower(ADD_VALUE::varchar)|| '%' or lower(gdsksdesc)  like '%' || lower(ADD_VALUE::varchar)|| '%')")
		->sqlMatchType(FormFieldMatch::SUBSTRING);

	$list->addColumn('')->sqlField('desc');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.disdef_bgb_goaldomainscopeksa')
			->setKeyField('gdsrefid')
			->applyListClassMode()
	);

	$list->printList();
?>
