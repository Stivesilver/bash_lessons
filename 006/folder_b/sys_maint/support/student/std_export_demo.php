<?php
	Security::init();
	
    $stdrefid  = io::geti('stdrefid');
    $iniOptions = IDEAFormat::getIniOptions();
    $template = $iniOptions['student_demo_xml'];	
	$template = str_replace('stdrefid=stdrefid', 'stdrefid=' . $stdrefid, $template);
	$edit = new EditClass('edit1', 0);
		
    $edit->title = 'Export Student Demogrpahics and Sp Ed Enrollment Data';
    
    $edit->addGroup('General Information');
	$edit->addControl('Root ID')
    	->name('root_id')        
        ->value(SystemCore::$VndRefID)
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
		
	$edit->addControl('Result Delete', 'textarea')
        ->name('delete')
		->css('width', '100%')
		->css('height', '100px');
		
	$edit->addButton('Run')
		->css('width', '100px')
		->onClick("
			$('#result').val('');
			$('#delete').val('');
			api.ajax.post(
				'std_export.ajax.php', 
				{'root_id' : $('#root_id').val(), 'template' : $('#template').val()},
				function(answer) {
					$('#result').val(answer.xml_data);
					$('#delete').val(answer.xml_del);
				}
			)
		");

	$edit->printEdit();
	

?>