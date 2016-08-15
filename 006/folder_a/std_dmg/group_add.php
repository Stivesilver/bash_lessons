<?php
	Security::init();

    $RefID = io::geti('RefID');
    
    $edit = new EditClass('edit1', $RefID);
    $edit->title = 'Add/Edit Grouping'; 
  
	$edit->setSourceTable('webset.dmg_studentgroupingdtl', 'sgdrefid');
                         
	$edit->addTab('General Information');
	$edit->addControl('Group', 'select')
		->sqlField('sdgrefid')
		->name('sdgrefid')
        ->req()
		->sql("
            SELECT sdgrefid,
                   sdgname
              FROM webset.disdef_stddemogrouping
             WHERE vndrefid = VNDREFID 
             ORDER BY sdgname
    ");
                
	$edit->addSQLConstraint(
        'Student already belongs to this group', 
	    "
        SELECT 1 
	      FROM webset.dmg_studentgroupingdtl
	     WHERE stdrefid = " . io::get('stdrefid') . "           
           AND sdgrefid = [sdgrefid]
           AND sgdrefid!=AF_REFID
    ");
	        
	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value($_SESSION['s_userUID'])->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('stdrefid', 'hidden')->value(io::get('stdrefid'))->sqlField('stdrefid');

    $edit->finishURL = CoreUtils::getURL('group_list.php', array('stdrefid'=>io::get('stdrefid')));
    $edit->cancelURL = CoreUtils::getURL('group_list.php', array('stdrefid'=>io::get('stdrefid')));

    $edit->printEdit();

?>
