<?php

	Security::init();

	if (io::get('RefID') == '') {

		$list = new ListClass();

		$list->title            = 'Related Services';
		$list->showSearchFields = true;
		$list->SQL              = "
            SELECT dtrrefid,
                   strcode,
                   strdesc,
                   strtext,
                   nasw,
                   CASE WHEN NOW() > enddate  THEN 'In-Active' ELSE 'Active' END  as status,
                   provider.mdp_provider_type as provider_type
              FROM webset.disdef_services_rel services
              LEFT JOIN webset.med_def_provider provider ON provider.mdp_refid = services.mspa_refid
             WHERE vndrefid = VNDREFID 
                   ADD_SEARCH
             ORDER BY strcode, strdesc
        ";

		$list->addSearchField('Status', '', 'list')
			->value('1')
			->sqlField('(CASE enddate<now() WHEN true THEN 2 ELSE 1 END)')
			->data(array(1 => 'Active', 2 => 'Inactive'));

		$list->addSearchField('Service', 'strdesc');

		$list->addColumn('Code');
		$list->addColumn('Service');
		$list->addColumn('Description');
		$list->addColumn('N/A Switcher')->type('switch');
		$list->addColumn('Status');
		$list->addColumn('Medicaid Provider Type')
			->sqlField('provider_type');

		$list->addURL = 'relservice.php';
		$list->editURL = 'relservice.php';

		$list->addButton(
			FFIDEAExportButton::factory()
				->setTable('webset.disdef_services_rel')
				->setKeyField('dtrrefid')
				->applyListClassMode()
		);

		$list->printList();

		$message = 'Services will be used when 77th parameter <b>Use District Services</b> set to Yes. <br/>
                    Currently it is set to <b>' . (IDEACore::disParam(77) == 'Y' ? 'Yes' : 'No') . '</b>. 
                    See <a href="' . CoreUtils::getURL('/apps/idea/sys_maint/dis_control/vnd_control.php') . '"><b>District Parameters</b></a>.';
		print UIMessage::factory($message, UIMessage::NOTE)->toHTML();
	} else {

		$edit = new EditClass('edit1', io::geti('RefID'));

		$edit->title = 'Add/Edit Related Services';

		$edit->setSourceTable('webset.disdef_services_rel', 'dtrrefid');

		$edit->addGroup('General Information');
		$edit->addControl('Code')->sqlField('strcode')->name('strcode')->size(12)->req();
		$edit->addControl('Service')->sqlField('strdesc')->name('strdesc')->size(90)->req();

		$edit->addControl('Medicaid Provider Type', 'select')
			->name('mspa_refid')
			->sql("
	            SELECT attr.mspa_refid,
	               	   prov.mdp_provider_type
		          FROM webset.med_state_provider_attr attr
		          LEFT JOIN webset.med_def_provider prov ON attr.mdp_refid = prov.mdp_refid
	        ")
			->sqlField('mspa_refid')
			->onChange('getSwitchers()')
			->emptyOption(true);

		$edit->addControl('Prescriptions')
			->disabled()
			->name('prescriptions')
			->hideIf('mspa_refid', 0);

		$edit->addControl('Needs Approval', 'text')
			->disabled()
			->name('approval')
			->hideIf('mspa_refid', 0);

		$edit->addControl('Description', 'textarea')->sqlField('strtext')->css('WIDTH', '100%')->css('HEIGHT', '50px');
		$edit->addControl(FFSwitchYN::factory('N/A Switcher'))->sqlField('nasw')->value('N');
		$edit->addControl('Deactivation Date', 'date')->sqlField('enddate');

		$edit->addGroup('Update Information', true);
		$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
		$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
		$edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');

		$edit->addSQLConstraint('Such Service already exists', "
                SELECT 1 
                  FROM webset.disdef_services_rel
                 WHERE vndrefid = VNDREFID
                   AND (strcode = '[strcode]' OR strdesc = '[strdesc]')
                   AND dtrrefid!=AF_REFID
        ");

		$edit->finishURL = 'relservice.php';
		$edit->cancelURL = 'relservice.php';

		$edit->firstCellWidth = "30%";

		$edit->printEdit();
	}
?>

<script type="text/javascript">
	function getSwitchers() {
		var mspaRefid = $('#mspa_refid').val();
		if (mspaRefid > 0) {
			api.ajax.post(
				'med_provider_data.ajax.php',
				{'mspaRefid' : mspaRefid},
				function(answer) {
					$('#prescriptions').val(answer.switch[0].prescriptions);
					$('#approval').val(answer.switch[0].approval);
				}
			);
		} else {
			$('#prescriptions').val('');
			$('#approval').val('');
		}

	}
</script>