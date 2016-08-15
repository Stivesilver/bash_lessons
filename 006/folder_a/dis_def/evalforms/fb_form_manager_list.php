<?php

	Security::init();

	$list = new ListClass('list1');

	$list->showSearchFields = true;

	$list->title = 'FB Form Templates';
	$list->SQL = "
		SELECT dfrefid,
               mfcpdesc,
               title,
               df.lastuser,
               df.lastupdate,
               CASE WHEN df.fb_type = 2 THEN 'Y' ELSE 'N' END AS status
          FROM webset.disdef_forms AS df
               INNER JOIN webset.def_formpurpose AS fprp ON (fprp.mfcprefid  = df.mfcprefid)
               LEFT JOIN webset.statedef_forms AS stf ON (stf.mfcrefid = df.mfcrefid)
         WHERE df.vndrefid = " . SystemCore::$VndRefID . "
         ORDER BY mfcpdesc, mfcdoctitle
	";

	$list->addSearchField('Form Title')->sqlField('mfcdoctitle');
	$list->addSearchField(FFSelect::factory('Form Purpose'))
		->sql("
			SELECT mfcprefid, mfcpdesc
			  FROM webset.def_formpurpose
	         ORDER BY mfcpdesc
		")
		->sqlField('fprp.mfcprefid');

	$list->addColumn('Form Title')
		->sqlField('mfcpdesc')
		->type('group');

	$list->addColumn('Form Purpose')
		->sqlField('title');

	$list->addColumn('Last User', '20%')
		->sqlField('lastuser');

	$list->addColumn('Last Update', '20%', 'datetime')
		->sqlField('lastupdate');

	$list->addColumn('From Warehouse')
		->sqlField('status')
		->dataCallback('whTitle')
		->cssCallback('markWH')
		->hint('The form is downloaded from Warehouse')
		->width('20%');

	$list->editURL = $list->addURL = CoreUtils::getURL('./fb_form_manager_edit.php');

	$list->addSafeDeleteTie(
		DBSafeDeleteTie::factory(
			'Students Data',
			'webset.std_forms',
			'dfrefid'
		)->restrictDeletion(true)
	);

	$list->deleteTableName = 'webset.disdef_forms';
	$list->deleteKeyField = 'dfrefid';

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

	$list->addButton("Download from Warehouse", "copy()");

	$list->printList();

	function markWH($data) {
		if ($data['status'] == 'Y') {
			return array(
				'background-color' => 'green'
			);
		} else {
			return array(
				'background-color' => 'red'
			);
		}
	}

	function whTitle($data) {
		if ($data['status'] == 'Y') {
			return 'Yes';
		} else return 'No';
	}

?>

<script>
	function copy() {
		var wnd = api.window.open(
			'Download from Warehouse',
			api.url('./fb_copy_list.php')
		);
		wnd.resize(950, 700);
		wnd.center();
		wnd.addEventListener(
			ObjectEvent.COMPLETE,
			function (e) {
				api.reload();
			}
		);
		wnd.show();

	}

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
</script>
