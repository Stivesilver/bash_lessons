<?php
	Security::init();

	$strUrlEnd = "?AMRefID=" . io::get("AMRefID") . "&ADRefID=" . io::get("ADRefID");

	$edit = new editClass('edit1', io::get("RefID"));

	$edit->setSourceTable('webset.def_discontrol', 'dcrefid');

	$edit->title = "Add/Edit District Control Option";

	$edit->addGroup('General Information');

	$edit->addControl(FFSelect::factory('Category'))
		->sql("
			SELECT sdcatrefid, name
			  FROM webset.statedef_discontrol_cat
			 ORDER BY order_num, name
		")
		->emptyOption(true)
		->sqlField('sdcatrefid');

	$edit->addControl("District Control Option", "edit")
		->css('width', '100%')
		->sqlField('dcdesc')
		->name('dcdesc');

	$edit->addControl(
		FFMultiSelect::factory('Class Period')
			->sqlField('screfid')
			->sqlTable('webset.statedef_discontrol', 'dcrefid',
			array(
				'lastuser' => SystemCore::$userUID,
				'lastupdate' => date('m-d-Y H:i:s')
			))
			->sql("
				    SELECT staterefid, state || ' - ' || statename
                      FROM webset.glb_statemst
                     ORDER BY staterefid
			")
	);

	$edit->addControl("Key for Option")
		->sql("SELECT 'YesNo', 'Simple Yes/No', 1
                    UNION
                   SELECT 'SQL', 'SQL', 2
                    ORDER BY 3")
		->onKeyUp('checkSQL()')
		->help("&nbsp; - type in 'SQL' to set up query as option; TEXT - for simple text option; TEXTAREA - for multi-line text options;")
		->sqlField('dckey')
		->name('dckey');

	$edit->addControl("Specify SQL", 'textarea')
		->css('width', '100%')
		->css('height', '150px')
		->sqlField('dcsql')
		->showIf('dckey', 'SQL')
		->name('dcsql');

	$edit->addGroup('Update Information');
	$edit->addControl("Last User", "PROTECTED")->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl("Last Update", "PROTECTED")->value(date("m-d-Y H:i:s"))->sqlField('lastupdate');

	$edit->finishURL = "dis_control_list.php";
	$edit->cancelURL = "dis_control_list.php";

	$edit->printEdit();

?>
<table class=zText>
	<tr>
		<td class=zText>
			<b>Autofields for built-in SQL:</b> <br>
			AF_STATEREFID - State Refid (25 for MO) <br>
			AF_VNDREFID - District ID

		</td>
	</tr>
</table>
