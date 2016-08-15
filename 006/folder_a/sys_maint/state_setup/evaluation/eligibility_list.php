<?php

	Security::init();

	$staterefid = io::geti('staterefid');

	$list = new ListClass();
	$list->title = 'Eligibility Criteria';
	$list->showSearchFields = true;

	$list->SQL = "
		SELECT elrefid,
		       elcode,
		       eldesc,
		       seqnum,
		       CASE
		       WHEN NOW() > recdeactivationdt THEN 'N'
		       ELSE 'Y'
		       END AS status
		  FROM webset.es_statedef_eligibility AS t
		 WHERE screfid = $staterefid
		 ORDER BY seqnum, elcode, eldesc
	";

	$list->addSearchField("ID", "(elrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");
	$list->addSearchField(FFSelect::factory('Status'))
		->sqlField('(CASE recdeactivationdt<now() WHEN true THEN 2 ELSE 1 END)')
		->sql("(SELECT 1, 'Active') UNION (SELECT 2, 'In-Active')")
		->value(1);

	$list->addColumn('ID')->sqlField('elrefid');
	$list->addColumn("Code")->sqlField('elcode');
	$list->addColumn("Eligibility Criteria")->sqlField('eldesc');
	$list->addColumn("Status")->sqlField('status')->type('switch');
	$list->addColumn("Subcategories")->dataCallback('subcat')->dataHintCallback('subcatshint');
	$list->addColumn("Order #")->sqlField('seqnum');

	$list->addURL = CoreUtils::getURL('./eligibility_edit.php', array('staterefid' => io::get("staterefid")));
	$list->editURL = CoreUtils::getURL('./eligibility_edit.php', array('staterefid' => io::get("staterefid")));

	$list->addButton(
		FFIDEAExportButton::factory()
			->setTable('webset.es_statedef_eligibility')
			->setKeyField('elrefid')
			->setNesting('webset.es_statedef_eligibility_sub', 'elsrefid', 'elrefid', 'webset.es_statedef_eligibility', 'elrefid')
			->applyListClassMode()
	);

	$list->printList();

	function subcat($data) {
		$count = db::execSQL("
			SELECT count(elsrefid)
			  FROM webset.es_statedef_eligibility_sub
             WHERE elrefid = " . $data['elrefid'] . "
		")->getOne();
		return UIAnchor::factory("Subcategories(" . $count . ")")->onClick('subcat(AF_REFID, event)')->toHTML();
	}

	function subcatshint($data) {
		$names = db::execSQL("
			SELECT elsdesc
			  FROM webset.es_statedef_eligibility_sub
             WHERE elrefid = " . $data['elrefid'] . "
		")->assocAll();
		$res = '';
		foreach ($names as $name) {
			$res .= $name['elsdesc'] . "<br/>";
		}
		return $res;
	}
?>
<script>
	function subcat(id, evt) {
		api.event.cancel(evt);
		var win = api.window.open('Doc Types', api.url("./eligibility_sub_list.php?elrefid=" + id));
		win.resize(1200, 700);
		win.show();
	}
</script>
