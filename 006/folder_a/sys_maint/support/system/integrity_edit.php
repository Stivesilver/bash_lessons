<?php
    Security::init();
    
    $edit = new EditClass('edit', io::geti('RefID'));
    $edit->title = 'Add/Edit Integrity Process';
    
    $defaultXml = "<integrity_process>" . PHP_EOL;
    $defaultXml .= "    <profile></profile>" . PHP_EOL;
    $defaultXml .= "    <checks>" . PHP_EOL;
    $defaultXml .= "        <check>" . PHP_EOL;
    $defaultXml .= "            <method></method>" . PHP_EOL;    
    $defaultXml .= "            <param></param>" . PHP_EOL;    
    $defaultXml .= "        </check>" . PHP_EOL;
    $defaultXml .= "    <report></report>" . PHP_EOL;
	$defaultXml .= "</integrity_process>" . PHP_EOL;
    	

    $edit->setSourceTable('webset.sys_sql_archive', 'refid');

    $edit->addGroup('General Information');
    $edit->addControl('Process Name')
    	->sqlField('sql_about')
    	->size(80)
    	->req();
    	
    $edit->addControl('Process XML', 'textarea')
    	->sqlField('sql_body')
    	->name('sql_body')
    	->value($defaultXml)
    	->css('width', '100%')
    	->css('height', '200px')
    	->req();
    
    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');       
    
    
    $edit->addSQLConstraint("XML should start with tag integrity", 
            "
            SELECT 1 WHERE trim('[sql_body]') NOT LIKE '<integrity_process>%'
    ");

    $edit->cancelURL = 'integrity_list.php';
    $edit->finishURL = 'integrity_list.php';

    $edit->printEdit();
?>
