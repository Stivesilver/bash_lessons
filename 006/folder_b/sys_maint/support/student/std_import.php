<?php
	Security::init();
	
    $tsRefID   = io::geti('tsRefID');
    $iniOptions = IDEAFormat::getIniOptions();
    $template = $iniOptions['student_data_xml'];

    $edit = new EditClass('edit1', 0);

    $edit->title = 'Import Student Sp Ed Data';

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

	$edit->addControl('Data', 'textarea')
        ->name('importdata')
		->css('width', '100%')
		->css('height', '100px')
        ->req();

    $edit->addButton('Run')
		->css('width', '100px')
		->onClick("			
			api.ajax.post(
				'std_import.ajax.php', 
				{'root_id' : $('#root_id').val(), 
				 'template' : $('#template').val(),
				 'importdata' : $('#importdata').val()},
				function() {
					api.alert('Data has been imported.')
				}
			)
		");		

	$edit->printEdit();

?>