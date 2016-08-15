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

    $edit->title = 'Import Data';

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

	$edit->addControl('Data', 'textarea')
        ->name('importdata')
		->css('width', '100%')
		->css('height', '100px')
        ->req();

    $edit->addButton('Run')
		->css('width', '100px')
		->onClick("			
			api.ajax.post(
				'import.ajax.php', 
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