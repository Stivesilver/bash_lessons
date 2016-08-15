<?php

	Security::init();

	require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");

	$dskey = io::get('dskey');

	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$evalproc_id = $ds->safeGet('evalproc_id');
	$screenURL = $ds->safeGet('screenURL');
	$group = io::get('group');
	$title = db::execSQL("
		SELECT cgname
		  FROM webset.sped_constructions_group
		 WHERE cgrefid = $group
	")->getOne();

	io::jsVar('dskey', $dskey);
	io::jsVar('group', $group);

	if (io::exists('help')) {
		$help = FFIDEAHelpButton::factory()
			->setHTMLByConstruction(io::get('help'));
	} else {
		$help = '';
	}

	//STD DATA
	if (io::get('other_id') != '') {
		$where = ' AND other_id = ' . io::get('other_id');
	} else {
		$where = ' AND other_id IS NULL';
	}

	$list = new ListClass();

	$list->SQL = "
		SELECT refid,
	           cs.cnname,
	           scs.lastuser,
	           scs.lastupdate
          FROM webset.std_constructions AS scs
               INNER JOIN webset.sped_constructions	AS cs ON (scs.constr_id = cs.cnrefid)
         WHERE stdrefid = " . $tsRefID . "
           AND evalproc_id = " . $evalproc_id . "
           AND cs.group_id = " . $group . "
           " . $where . "
         ORDER BY order_num
	";

	$list->title = $title;

	$list->addColumn('Document')->sqlField('cnname');
	$list->addColumn('Last User')->sqlField('lastuser');
	$list->addColumn('Last Update')->sqlField('lastupdate')->type('date');

	$list->deleteTableName = 'webset.std_constructions';
	$list->deleteKeyField = 'refid';

	$list->addButton(
		FFButton::factory('Add New')
			->leftIcon('plus.png')
			->balloon(
				UIBalloon::factory()
					->showInTopFrame(false)
					->showCloseButton(true)
					->addObject(
						UILayout::factory()
							->addHTML(
								FFSelect::factory()
									->name('templ_id')
									->caption('Template')
									->sql("
										SELECT cs.cnrefid,
										       cs.cnname
										  FROM webset.sped_constructions AS cs
										 WHERE cs.group_id = $group
										   AND NOT EXISTS (
												SELECT 1
												  FROM webset.std_constructions AS std
												 WHERE std.constr_id = cs.cnrefid
												   AND std.evalproc_id = " . $evalproc_id . "
										       )
									       AND (deactivation_date IS NULL or now()< deactivation_date)
										 ORDER BY order_num
									")
									->toHTML(),
								'[padding: 10px 15px]'
							)
							->addDividingLine()
							->addObject(
								FFButton::factory('Create & Open')
									->onClick('openSubmission($("#templ_id").val()); UIBalloon(this).destroy()'),
								'[padding: 4px] center'
							)
					)
			)
	);

	$list->editURL = CoreUtils::getURL('group_edit.php', array('dskey' => $dskey, 'group' => $group, 'help' => io::get('help')));

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable($list->deleteTableName)
			->setKeyField($list->deleteKeyField)
			->applyListClassMode()
	);

	if ($help) {
		$list->addButton($help);
	}

	$list->addButton(
		IDEAFormat::getPrintButton(array('tsRefID' => $tsRefID, 'dskey' => $dskey))
	);

	$list->printList();

?>
<script>
	function openSubmission(constr) {
		api.goto(
			api.url('./group_edit.php', {'constr': constr, 'dskey': dskey, 'group': group})
		);
	}
</script>
