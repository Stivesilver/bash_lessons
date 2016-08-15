<?php

	Security::init();

	$list      = new ListClass();
	$list->SQL = "
		SELECT attr.*,
			   prov.*,
			   state.state,
			   values_pres.validvalue AS prescriptions,
			   values_app.validvalue AS approval
          FROM webset.med_state_provider_attr attr
          	   INNER JOIN webset.med_def_provider prov ON attr.mdp_refid = prov.mdp_refid
          	   INNER JOIN webset.glb_statemst state ON attr.screfid = state.staterefid
          	   INNER JOIN webset.glb_validvalues values_pres ON values_pres.validvalueid = attr.mspa_prescriptions
          	   INNER JOIN webset.glb_validvalues values_app ON values_app.validvalueid = attr.mspa_approval
         WHERE values_pres.valuename='Medicaid_Prescription'
           AND values_app.valuename='Medicaid_Approval'
           AND ADD_SEARCH
         ORDER BY attr.mdp_refid desc
		";

	$list->title           = 'Medicaid Provider Type State Attributes';
	$list->addURL          = CoreUtils::getURL('provider_attr_edit.php', array());
	$list->editURL         = CoreUtils::getURL('provider_attr_edit.php', array());
	$list->deleteTableName = 'webset.med_state_provider_attr';
	$list->deleteKeyField  = 'mspa_refid';

	$list->addSearchField('State', 'state')
		->name('state');

	$list->addColumn('State')
		->sqlField('state');

	$list->addColumn('Code')->sqlField('mdp_provider_type_code');
	$list->addColumn('Provider Type')->sqlField('mdp_provider_type');
	$list->addColumn('Prescriptions')->sqlField('prescriptions');
	$list->addColumn('Needs Approval')->sqlField('approval');

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->printList();

