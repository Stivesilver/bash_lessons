<?php

	Security::init();

	$dskey = io::get('dskey');
	$id_field = io::get('id_field');

	$edit = new EditClass('edit1', 0);
	$edit->title = 'Accommodations';

	$edit->addGroup('General Information');

	$edit->addControl('Category', 'select')
		->name('sections')
		->sql("
			SELECT catrefid,
			       categor
			  FROM webset.disdef_progmodcat
			 WHERE ((CASE enddate<now() WHEN true THEN 2 ELSE 1 END) = 1)
			   AND vndrefid = VNDREFID
			 ORDER BY seqnum, categor
        ");


	$edit->addControl('Accommodations', 'select_check')
		->name('aitems')
		->sql("
			SELECT refid,
				   pmdesc
			  FROM webset.disdef_progmod acc
			 WHERE acc.vndrefid = VNDREFID
			   AND catrefid = VALUE_01
			 ORDER BY acc.seqnum, pmdesc
        ")
		->tie('sections')
		->breakRow()
		->req();

	$edit->cancelURL = 'javascript:api.window.destroy()';
	$edit->finishURL = '';
	$edit->topButtons = true;

	$edit->addButton('Populate')
		->css('width', '100px')
		->onClick("
			if ($('#aitems').val() == '') {api.alert('Select Accommodations first.'); return false;}
			api.ajax.post(
				'populate_process.ajax.php', 
				{'aitems' : $('#aitems').val()},
				function(answer) {
					api.window.dispatchEvent('items_selected', {id: '" . $id_field . "', text: answer.text});
					api.window.destroy();
				}
			)
		");

	$edit->printEdit();
?>
