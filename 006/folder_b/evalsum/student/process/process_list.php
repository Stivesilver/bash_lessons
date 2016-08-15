<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');

	$list = new listClass();

	$list->title = "Student Evaluation Process Tracking";

	$list->SQL = "
		SELECT ep.eprefid,
		       TO_CHAR(ep.date_start, 'MM-DD-YYYY') AS sdata,
		       rp.essrtdescription,
		       ep.ep_current_sw,
		       ep.lastuser,
		       ep.lastupdate
		  FROM webset.es_std_evalproc AS ep
		       INNER JOIN webset.es_statedef_reporttype AS rp ON rp.essrtrefid = ep.ev_type
		 WHERE (1=1) ADD_SEARCH
		   AND stdrefid = " . $tsRefID . "
		 ORDER BY ep.date_start DESC
    ";

	$list->addColumn("Evaluation Start Date")->sqlField('sdata')->dataCallback('markCurrentEval');
	$list->addColumn("Evaluation Type")->sqlField('essrtdescription')->dataCallback('markCurrentEval');
	$list->addColumn('Last User')->sqlField('lastuser');
	$list->addColumn('Last Update')->sqlField('lastupdate')->type('datetime');

	if (SystemCore::$AccessType == "1") {

		$list->addRecordsProcess('Delete')
			->width('80px')
			->message('Do you really want to delete this Evaluation Process?')
			->url(CoreUtils::getURL('./eval_proc_delete.ajax.php', array('dskey' => $dskey)))
			->type(ListClassProcess::DATA_UPDATE)
			->progressBar(false);
	}

	$list->addURL = CoreUtils::getURL('./process_add.php', array('dskey' => $dskey));
	$list->editURL = CoreUtils::getURL('./process_add.php', array('dskey' => $dskey));

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.es_std_evalproc')
			->setKeyField('eprefid')
			->setXMLTemplate('
				<template>
				  <tables>
					<webset.es_std_evalproc>
					  <webset.es_std_evalproc_forms parent_id="evalproc_id"/>
					  <webset.es_std_common parent_id="evalproc_id"/>
					  <webset.es_std_red parent_id="evalproc_id">
						<webset.es_std_redds parent_id="redrefid"/>
					  </webset.es_std_red>
					  <webset.es_std_red_concl parent_id="evalproc_id"/>
					  <webset.es_std_red_part parent_id="evalproc_id"/>
					  <webset.std_constructions parent_id="evalproc_id"/>
					  <webset.es_std_er_generalinfo parent_id="eprefid"/>
					  <webset.es_std_er_casehistory parent_id="eprefid"/>
					  <webset.es_std_scr parent_id="eprefid"/>
					  <webset.es_std_join parent_id="eprefid"/>
					  <webset.es_std_er_observation parent_id="eprefid"/>
					  <webset.es_std_er_conclusions parent_id="eprefid"/>
					  <webset.es_std_er_participants parent_id="eprefid"/>
					  <webset.es_std_er_participants_sld parent_id="eprefid"/>
					  <webset.es_std_er_providecopy parent_id="eprefid"/>
					</webset.es_std_evalproc>
				  </tables>
				  <fields/>
				</template> 
			')
			->applyListClassMode()
	);

	$list->printList();

	function markCurrentEval($data, $col) {
		if ($data['ep_current_sw'] == 'Y') {
			return UILayout::factory()
				->addHTML($data[$col], '[color:blue; font-weight: bold;]')
				->toHTML();
		} else {
			return $data[$col];
		}
	}
?>
<script>
	document.getElementById('_idAdd_but').style.width = "150px";
</script>
