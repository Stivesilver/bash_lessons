<?php

	Security::init();

	$list    = new ListClass();
	$dskey   = io::get('dskey');

	$list->editURL      = CoreUtils::getURL('linkage_edit.php', array('dskey' => $dskey));
	$list->multipleEdit = false;
	$list->title        = 'Linkage list';
	$list->SQL          = "
		SELECT progmod.stsrefid,
               COALESCE(pcat.macdesc || ': ', '') || progmod.stsdesc,
               COALESCE(acat.aacdesc || ': ',  accomod.macrefid::varchar) || accomod.stsdesc
          FROM webset.statedef_mod_acc progmod
               LEFT OUTER JOIN webset.statedef_mod_acc_cat pcat ON pcat.macrefid = progmod.macrefid
               LEFT OUTER JOIN webset.statedef_mod_acc accomod ON progmod.ids_assessments::int = accomod.stsrefid
               LEFT OUTER JOIN webset.statedef_assess_acc_cat acat ON acat.aacrefid = accomod.aacrefid
         WHERE progmod.screfid = " . VNDState::factory()->id . "
           AND progmod.modaccommodationsw = 'Y'
           AND (progmod.recdeactivationdt IS NULL or now()< progmod.recdeactivationdt)
           AND (pcat.enddate IS NULL or now()< pcat.enddate)
         ORDER BY 2
	";

	$list->addColumn('Program Modifications and Accommodations');

	$list->addColumn('Assessment Accommodations');

	$list->printList();

?>