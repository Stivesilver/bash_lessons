<?php

	Security::init();

	$mspaRefID = io::post('mspaRefid');

	$SQL = "
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
           AND attr.mspa_refid = $mspaRefID
	";

	io::ajax('switch', db::execSQL($SQL)->assocAll());