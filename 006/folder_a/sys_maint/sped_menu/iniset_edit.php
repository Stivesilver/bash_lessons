<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;

	$edit->title = "Add/Edit Menu Set Option Value";

	$edit->setSourceTable('webset.sped_ini_set', 'isrefid');

	$edit->addGroup('General Information');

	$edit->addControl(FFSelect::factory("Option"))
		->sql("
			SELECT irefid,
                   ini_name
              FROM webset.sped_ini
             ORDER BY irefid
        ")
		->sqlField('irefid');

	$edit->addControl("Value", "textarea")
		->name('value')
		->sqlField('value');

	$edit->addUpdateInformation();
	$edit->addControl("IEP Format", "hidden")
		->value(io::get("iepformat"))
		->sqlField('srefid');

	$edit->finishURL = CoreUtils::getURL('./iniset_list.php', array('iepformat' => io::get("iepformat")));
	$edit->cancelURL = CoreUtils::getURL('./iniset_list.php', array('iepformat' => io::get("iepformat")));

	$edit->setPresaveCallback('backupPrevious', './iniset.inc.php');

	$button = new IDEABackup(null, 'webset.sped_ini_set', 213, 'revertData');
	$editButton = $button->previewBackup('isrefid', $RefID);
	$edit->addButton($editButton);

	$edit->printEdit();

?>
<script>
	function revertData(param) {
		api.ajax.process(
			UIProcessBoxType.DATA_UPDATE,
			api.url('./iniset_revert.ajax.php'),
			{
				'param': JSON.stringify(param)
			},
			true
		).addEventListener(
			ObjectEvent.COMPLETE,
			function (e) {
				for (var p in e.param.arr) {
					var selector = $('#' + p);
					if (selector.length && p != 'lastuser' && p != 'lastupdate') {
						console.log(p);
						selector.val(e.param.arr[p]);
					}
				}
			}
		);
	}
</script>
