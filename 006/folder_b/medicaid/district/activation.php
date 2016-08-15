<?php

	Security::init();

	$edit = new EditClass('edit1', 0);

	$edit->title = 'Medicaid Management System';

	$edit->addGroup('General Information');

	$key = SystemRegistry::factory(SystemRegistry::SR_ENTERPRISE)
		->readKey('webset', 'medicaid', 'activated', SystemRegistry::SR_ENTERPRISE);

	if (is_array($key)) {
		foreach ($key as $defVal) {
			break;
		}
	} else {
		$defVal = 'N';
	}

	$edit->addControl(
		FFSwitchYN::factory('Enable Medicaid Management System')
	)
	->name('activation')
	->value($defVal);

	$isAdmin       = preg_match('@^(g|d)support[0-9]*$@', SystemCore::$userUID) != 0;
	$saveAndEdit   = FFButton::factory('Save & Edit')
		->onClick('save()')
		->css('width: 115px;');

	if (!$isAdmin && SystemCore::$VndRefID != 1) {
		$saveAndEdit->disabled(true);
	}

	$edit->addButton($saveAndEdit);

	$edit->printEdit();
?>

<script type="text/javascript">
	function save() {
		api.ajax.post(
			'activation_save.ajax.php',
			{
				'activation': $('#activation').val()
			},
			function(answer) {
				if (answer.res == 1) {
					api.reload();
				}
			}
		);
	}
</script>