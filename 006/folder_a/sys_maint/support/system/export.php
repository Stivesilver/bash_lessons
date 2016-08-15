<?php

	Security::init();

	$helpButon = FFMenuButton::factory('Populate');

	$texts = db::execSQL("
		SELECT sql_about,
			   sql_body
		  FROM webset.sys_sql_archive
		 WHERE LOWER(sql_about) LIKE LOWER('%xml template%')
		 ORDER BY refid desc
	")->assocAll();

	for ($i = 0; $i < count($texts); $i++) {
		$helpButon->addItem($texts[$i]['sql_about'], '$("#template").val(' . json_encode($texts[$i]['sql_body']) . ')');
	}

	$edit = new EditClass('edit1', 0);

	$edit->title = 'Export Data';

	$edit->addGroup('General Information');

	$edit->addControl('Root ID')
		->name('root_id')
		->css('width', '300px')
		->req();

	$edit->addControl('XML Template', 'textarea')
		->name('template')
		->append(count($texts) > 0 ? $helpButon : '')
		->css('width', '100%')
		->css('height', '100px')
		->req();

	$edit->addGroup('Result Information');
	$edit->addControl('Result XML', 'textarea')
		->name('result')
		->css('width', '100%')
		->css('height', '100px');

	$edit->addGroup('Result as SQL');

	$edit->addControl(FFSwitchYN::factory('Include SQL'))
		->name('show_sql')
		->value('N')
		->req();

	$edit->addControl('Result Delete', 'textarea')
		->name('delete')
		->css('width', '100%')
		->css('height', '100px');

	$edit->addControl('Result Insert', 'textarea')
		->name('insert')
		->css('width', '100%')
		->css('height', '100px');

	$edit->addControl('Result Upadte', 'textarea')
		->name('update')
		->css('width', '100%')
		->css('height', '100px');

	$edit->addButton('Run')
		->css('width', '100px')
		->onClick("
			$('#result').val('');
			$('#delete').val('');
			$('#insert').val('');
			$('#update').val('');
			api.ajax.post(
				'export.ajax.php',
				{'root_id' : $('#root_id').val(), 'template' : $('#template').val(), 'show_sql' : $('#show_sql').val()},
				function(answer) {
					$('#result').val(answer.xml_data);
					$('#delete').val(answer.xml_del);
					$('#insert').val(answer.xml_insert);
					$('#update').val(answer.xml_update);
				}
			)
		");

	$edit->printEdit();
?>
