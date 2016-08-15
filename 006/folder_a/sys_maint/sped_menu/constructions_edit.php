<?php
	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass('edit1', $RefID);

	$edit->setSourceTable('webset.sped_constructions', 'cnrefid');

	$edit->title = "Add/Edit Blocks Constructions";

	$edit->addGroup('General Information');

	$edit->addControl(FFSelect::factory("IEP Type"))
		->sql("
			SELECT srefid, shortdesc
              FROM webset.sped_menu_set
             WHERE (enddate IS NULL OR now()< enddate)
             ORDER BY shortdesc
		")
		->sqlField('setrefid');

	$edit->addControl('Group', 'select')
		->sql("
			SELECT cgrefid,
			       cgname
			  FROM webset.sped_constructions_group
			 WHERE (enddate IS NULL OR now()< enddate)
			 ORDER BY cgname
		")
		->sqlField('group_id')
		->emptyOption(true);

	$edit->addControl("Name")->sqlField('cnname')->width(300);
	$edit->addControl("Description", "textarea")->sqlField('cndesc');
	$edit->addControl("Default Values file")->sqlField('file_defaults')->width(300);
	$edit->addControl("Class with Defaults")->sqlField('class_defaults')->width(300);
	$edit->addControl("XML", "textarea")
		->sqlField('cnbody')
		->name('cnbody')
		->append(
			FFButton::factory('Preview')
				->onClick('tryit();')
				->toHTML()
		);

	$edit->addControl('Deactivation Date', 'date')->sqlField('deactivation_date')->name('deactivation_date');

	$edit->addControl('cnrefid', 'hidden')
		->sqlField('cnrefid')
		->name('cnrefid');

	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./constructions_list.php', array('staterefid' => -1));
	$edit->cancelURL = CoreUtils::getURL('./constructions_list.php', array('staterefid' => -1));

	$edit->printEdit();
?>

<script>
	function tryit() {
		var txt = $('#cnbody').val();
		var block = btoa(txt);
		var win = api.window.open('Block Construction', api.url("./construction_print.php?block"), {'block' : block});
		win.maximize();
		win.show();
	}
</script>
