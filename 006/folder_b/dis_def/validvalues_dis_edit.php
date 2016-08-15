<?php
    Security::init();

	$edit = new EditClass('edit1', io::geti('RefID'));

	$edit->title = (io::get('title') ? io::get('title') : 'Valid Values');

	$edit->setSourceTable('webset.disdef_validvalues', 'refid');

	$edit->addGroup('General Information');
	$edit->addControl('Value Text', 'textarea')->name('validvalue')->sqlField('validvalue')->req();
	$edit->addControl('Value Code (if needed)', 'edit')->sqlField('validvalueid')->size(10);
	$edit->addControl('Deactivation Date', 'date')->sqlField('glb_enddate');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('District ID', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');
	$edit->addControl('Area ID', 'hidden')->name('area')->value(io::get('area'))->sqlField('valuename');

	$edit->addSQLConstraint('Such Location already exists', "
			SELECT 1
			  FROM webset.disdef_validvalues
			 WHERE vndrefid = VNDREFID
			   AND valuename = '[area]'
			   AND validvalue = '[validvalue]'
			   AND refid != AF_REFID
	");

	$edit->finishURL = CoreUtils::getURL(
		"./validvalues_dis_list.php", 
		array(
			'area' => io::get('area'),
			'title' => io::get('title')
		)
	);
	$edit->cancelURL = $edit->finishURL;

	$edit->firstCellWidth = '30%';

	$edit->printEdit();

?>
