<?php

	Security::init();

	$staterefid = io::get('staterefid');

	$list = new ListClass('list1');

	$list->showSearchFields = true;

	$list->title = 'FB Form Templates';
	$list->SQL = "
		SELECT mfcrefid,
               mfcpdesc,
               mfcdoctitle,
               onlythisip,
               length(fb_content) AS length,
               xmlform_id,
               stf.lastuser,
               stf.lastupdate
          FROM webset.statedef_forms AS stf
               INNER JOIN webset.def_formpurpose AS fprp ON fprp.mfcprefid  = stf.mfcprefid
         WHERE stf.screfid = " . $staterefid . "
           AND fb_type = 1
         ORDER BY mfcpdesc, mfcdoctitle
	";

	$list->addSearchField("ID", "(mfcrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField('Form Title')->sqlField('mfcdoctitle');
	$list->addSearchField(FFSelect::factory('Form Purpose'))
		->sql("
			SELECT mfcprefid, mfcpdesc
			  FROM webset.def_formpurpose
	         ORDER BY mfcpdesc
		")
		->sqlField('fprp.mfcprefid');
	$list->addSearchField('District Only')->sqlField('onlythisip');

	$list->addColumn('Form Purpose')
		->sqlField('mfcpdesc')
		->type('group');

	$list->addColumn('ID')->sqlField('mfcrefid');
	$list->addColumn('Form Title')
		->sqlField('mfcdoctitle');

	$list->addColumn('District Only')
		->sqlField('onlythisip');

	$list->addColumn('Length')
		->sqlField('length');

	$list->addColumn("XML Fields", "", "LINK", "javascript:fb2xml('AF_REFID', 'AF_COL3');", "")->sqlField('xmlform_id');

	$list->addColumn('Last User', '20%')
		->sqlField('lastuser');

	$list->addColumn('Last Update', '20%', 'datetime')
		->sqlField('lastupdate');

	$list->editURL = $list->addURL = CoreUtils::getURL('./fb_form_manager_edit.php', array('staterefid' => $staterefid));

	$list->deleteTableName = 'webset.statedef_forms';
	$list->deleteKeyField = 'mfcrefid';

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	$list->addButton(
		FFButton::factory('Print')
			->onClick('getForms();')
			->width(80)
			->leftIcon('./img/printer.png')
	);

	$list->printList();
?>


<script>
	function getForms() {
		var selVal = ListClass.get().getSelectedValues().values.join(',');
		if (selVal != '') {
			api.ajax.process(
				UIProcessBoxType.REPORT,
				api.url('./gen_forms.ajax.php'),
				{
					'selVal': selVal
				}
			);
		} else {
			alert('Please select Form(s)')
		}
	}

	function fb2xml(id) {
		var win = api.window.open('IEP Blocks', api.url("../../../../../../applications/webset/sys_maint/state_setup/general/evalforms/fb2xml.php"));
		win.resize(1200, 700);
		win.show();
	}
</script>
