<?php
	Security::init();

    $tsRefID   = io::geti('tsRefID');
    $iniOptions = IDEAFormat::getIniOptions();
    $template = $iniOptions['student_data_xml'];

	$edit = new EditClass('edit1', 0);

    $edit->title = 'Export Student Sp Ed Data';

    $edit->addGroup('General Information');
	$edit->addControl('Root ID')
    	->name('root_id')
        ->value($tsRefID)
        ->css('width', '300px')
        ->req();

	$edit->addControl('XML Template', 'textarea')
        ->name('template')
        ->value($template)
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
		->css('height', '100px')
		->showIf('show_sql', 'Y');

	$edit->addControl('Result Insert', 'textarea')
		->name('insert')
		->css('width', '100%')
		->css('height', '100px')
		->showIf('show_sql', 'Y');

	$edit->addControl('Result Upadte', 'textarea')
		->name('update')
		->css('width', '100%')
		->css('height', '100px')
		->showIf('show_sql', 'Y');

	$edit->addButton('Run')
		->css('width', '100px')
		->onClick("
			$('#result').val('');
			$('#delete').val('');
			$('#insert').val('');
			$('#update').val('');
			api.ajax.post(
				'std_export.ajax.php',
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
