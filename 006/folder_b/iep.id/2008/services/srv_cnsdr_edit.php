<?php

	Security::init();
	
	$dskey   	  = io::get('dskey');
	$ds 	 	  = DataStorage::factory($dskey, true);
	$stdIEPYear   = $ds->safeGet('stdIEPYear');
	$tsRefID 	  = $ds->safeGet('tsRefID'); 
	$edit    	  = new editClass('edit1', 0);
    
    $edit->title       = 'Other Considerations';
    $edit->saveAndEdit = true;
    
    $edit->addGroup('General Information');
    $edit->addControl('IEP Builder will show', 'select_radio')
    	 ->name('selected')
    	 ->data(
    	 	array(
				'iep' => 'IEP Considerations', 
				'sp'  => 'SP Considerations'
    	 	)
    	 )
    	 ->value(IDEAStudentRegistry::readStdKey($tsRefID, 'id_iep', 'considerations', $stdIEPYear));
    
    $edit->addControl('tsRefID', 'hidden')
	    ->name('tsRefID')
	    ->value($tsRefID);
	    
    $edit->addControl('stdIEPYear', 'hidden')
    	->name('stdIEPYear')
    	->value($stdIEPYear);
    	
    $edit->setPresaveCallback('updateCnsdr', 'srv_cnsdr_save.inc.php');

    $edit->printEdit();

?>
