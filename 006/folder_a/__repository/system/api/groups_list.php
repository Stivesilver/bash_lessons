<?php
	Security::init();

	$ids = io::get('ids');
	$table = io::get('table');
	$key_field = io::get('key_field');
	$name_field = io::get('name_field');
	$cont_field = io::get('cont_field');
	$where = io::get('where');
	$join = io::get('join');
	$searches = json_decode(io::get('search'));
	$keys = json_decode(io::get('keys'));
	$forms = array();
	$forms[] = 0;
	foreach (explode(',', io::get('forms')) as $form) {
		if ($form > 0) {
			$forms[] = $form;
		}
	}

	$list = new ListClass();

	$list->title = 'Insert/Update Selected Records to Following Districts';
	$list->showSearchFields = true;

	$data = db::execSQL("
		SELECT $name_field,
		       md5($cont_field) AS $cont_field,
		       " . implode(',', $keys) . "
		  FROM $table AS t
		       $join
         WHERE $key_field IN (" . implode(',', $forms) . ")
    ")->assocAll();

	$inner_where = '';
	$bodies = '';
	foreach ($data AS $item) {
		if ($bodies) {
			$bodies .= " ,'" . $item[$cont_field] . "'";
		} else {
			$bodies .= "'" . $item[$cont_field] . "'";
		}
		if ($inner_where) {
			$inner_where .= " OR (" . $name_field . " = '" . $item[$name_field]. "'";
			foreach ($keys AS $key) {
				$inner_where .= " AND " . $key . " = '" . $item[$key]. "'";
			}
			$inner_where .= ")";
		} else {
			$inner_where .= " (" . $name_field . " = '" . $item[$name_field]. "'";
			foreach ($keys AS $key) {
				$inner_where .= " AND " . $key . " = '" . $item[$key]. "'";
			}
			$inner_where .= ")";
		}
	}
	if (!$bodies) {
		$bodies = 'NULL';
	}
	if (!$inner_where) {
		$inner_where = '2 = 1';
	}

	$list->SQL = "
        SELECT vnd.vndrefid,
               vndname,
               COALESCE(form_title::VARCHAR, 'Does not exist'),
               length($cont_field),
               t.lastuser,
               t.lastupdate,
			   CASE WHEN md5($cont_field) IN (" . $bodies . ") THEN 'Y' ELSE 'N' END as status,
               'XML'
	      FROM sys_vndmst vnd
			   LEFT OUTER JOIN (
			       SELECT $name_field as form_title,
						  t.$key_field,
						  t.vndrefid,
						  t.$cont_field,
						  t.lastuser,
						  t.lastupdate
			         FROM $table AS t
			              $join
			        WHERE $inner_where
			        $where
			        ADD_SEARCH
				) as t ON vnd.vndrefid = t.vndrefid
         WHERE vnd.vndrefid IN (" . $ids . ")
		 ORDER BY vndname, form_title
    ";

	$list->addSearchField('Form', "LOWER($name_field)  like '%' || LOWER('ADD_VALUE') || '%'");
	if (isset($searches)) {
		foreach ($searches as $search) {
			$list->addSearchField($search->title, $search->sqlField, $search->type);
		}
	}

	$list->addColumn('District');
	$list->addColumn('Name');
	$list->addColumn('Length');
	$list->addColumn('Last User');
	$list->addColumn('Last Update');
	$list->addColumn('Same to Current')->type('switch')->sqlField('status');

	$list->addRecordsProcess('Insert/Update')
		->url(CoreUtils::getURL('./groups.ajax.php', array('forms' => implode(',', $forms), 'keys' => io::get('keys'), 'cont_field' => $cont_field, 'key_field' => $key_field, 'table' => $table)))
		->type(ListClassProcess::DATA_UPDATE)
		->progressBar(false);

	$list->printList();

	$list2 = new ListClass();

	$list2->title = 'Exportable List';
	$list2->showSearchFields = true;
	$list2->hideCheckBoxes = false;

	$list2->SQL = "
        SELECT $key_field,
               vndname,
               COALESCE(form_title::VARCHAR, 'Does not exist'),
               length($cont_field),
               t.lastuser,
               t.lastupdate,
			   CASE WHEN md5($cont_field) IN (" . $bodies . ") THEN 'Y' ELSE 'N' END as status,
               'XML'
	      FROM sys_vndmst vnd
			   LEFT OUTER JOIN (
			       SELECT $name_field as form_title,
						  t.$key_field,
						  t.vndrefid,
						  t.$cont_field,
						  t.lastuser,
						  t.lastupdate
			         FROM $table AS t
			              $join
			        WHERE $inner_where
			        $where
			        ADD_SEARCH
				) as t ON vnd.vndrefid = t.vndrefid
         WHERE vnd.vndrefid IN (" . $ids . ")
		 ORDER BY vndname, form_title
    ";

	$list2->addSearchField('Form', "LOWER($name_field)  like '%' || LOWER('ADD_VALUE') || '%'");
	if (isset($searches)) {
		foreach ($searches as $search) {
			$list2->addSearchField($search->title, $search->sqlField, $search->type);
		}
	}

	$list2->addColumn('District');
	$list2->addColumn('Procedure');
	$list2->addColumn('Length');
	$list2->addColumn('Last User');
	$list2->addColumn('Last Update');
	$list2->addColumn('Same to Current')->type('switch')->sqlField('status');

	$list2->addButton(
		FFIDEAExportButton::factory()
			->setTable($table)
			->setKeyField($key_field)
			->applyListClassMode()
	);

	$list2->printList();


	$list2->addButton(
		FFIDEAExportButton::factory()
			->setTable($table)
			->setKeyField($key_field)
			->applyListClassMode()
	);
?>
