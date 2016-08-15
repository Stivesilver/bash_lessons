<?php
	Security::init();
	
	$dskey      = io::get('dskey');    
    $ds         = DataStorage::factory($dskey);
    $tsRefID    = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
	$student    = IDEAStudent::factory($tsRefID);
    $smode      = io::get('smode');
  
	$edit = new EditClass('edit1', io::get('RefID'));
		
	$edit->setSourceTable('webset.std_srv_all', 'ssmrefid');

    $edit->title = 'Add/Edit ' . ($smode == 'A') ? 'Amended Service' : 'Service';

	$edit->addGroup('General Information');
	$edit->addControl('IEP Date', 'protected')->outerHTML($student->get('stdiepyearbgdt'));
	$edit->addControl('Fiscal Year', 'protected')->outerHTML($student->get('stdiepyeartitle'));
	
	$edit->addControl('Type of Service', 'select_radio')
		->value(db::execSQL("
			SELECT trefid
	          FROM webset.statedef_services_type
             WHERE def_serv = 'Y' 
               AND screfid = ".VNDState::factory()->id."
               AND (recdeactivationdt IS NULL OR NOW()< recdeactivationdt)
		")->getOne())
		->sqlField('srv_type')
		->name('srv_type')
		->sql("
			SELECT trefid,
			       typedesc
		      FROM webset.statedef_services_type
			 WHERE screfid = ".VNDState::factory()->id."
               AND (recdeactivationdt IS NULL OR NOW()< recdeactivationdt)
                " . ($smode == 'A' ? "AND trefid in (1,2)" : "") . "
		     ORDER BY typedesc desc")
		->req();
        
	$edit->addControl('Service', 'select')
		->sqlField('srvrefid')
		->name('srvrefid')
		->sql("SELECT stsrefid, 
		              COALESCE(stscode, ' ') || ' - ' || stsdesc
 				 FROM webset.statedef_services_all
			    WHERE screfid = ".VNDState::factory()->id."
                  AND (recdeactivationdt IS NULL OR NOW()< recdeactivationdt)
                  AND type_id = VALUE_01 
			    ORDER BY 2")
		->tie('srv_type')
		->emptyOption(true)
		->req();

	$edit->addControl('Setting', 'select')
		->sqlField('setting_whole')
		->sql("SELECT ksssstatecode,  
		              ksssstatecode || COALESCE(' - ' || substring(ksssstatedesc, 0, 120), ' ')
	             FROM webset.statedef_services_set
	            WHERE screfid = ".VNDState::factory()->id."
	              AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
	            ORDER BY 1")
	    ->emptyOption(true)
	    ->req(($smode == 'A' ? false : true));

	$edit->addControl('Services comment', 'edit')->sqlField('comments')->size(90);
	
	$edit->addControl('Beginning Date', 'date')
        ->sqlField('begdate')
        ->value($student->getDate('stdenrolldt'));        
        
    $edit->addControl('Ending Date', 'date')
        ->sqlField('enddate')
        ->value($student->getDate('stdcmpltdt'));
        
	$edit->addControl('Minutes', 'edit')->sqlField('srv_minutes')->size(5);
	        
	$edit->addControl('Frequency', 'select')
		->sqlField('freq_id')
		->sql("SELECT sfrefid, sfdesc
                 FROM webset.disdef_frequency
                WHERE (enddate>now() or enddate is Null)
                  AND vndrefid = VNDREFID
                ORDER BY seqnum, sfdesc");
        
	$edit->addGroup('Provider Information');
	$edit->addControl('Providers', 'edit')
		->sqlField('provider')
		->name('provider')
		->size(50)
		->append(FFButton::factory('Find Provider')->onClick('selectUser();'));

	$edit->addControl('Provider SSN', 'hidden')->sqlField('provider_ssn')->name('provider_ssn');
		
    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');        
    $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
    $edit->addControl('IEP Year', 'hidden')->value($stdIEPYear)->sqlField('iep_year');    
    $edit->addControl('Service Mode', 'hidden')->value($smode)->sqlField('smode');    

	$edit->firstCellWidth = '30%';

    $edit->finishURL = CoreUtils::getURL('srv_list.php', array('dskey'=>$dskey, 'smode'=>$smode));
    $edit->cancelURL = CoreUtils::getURL('srv_list.php', array('dskey'=>$dskey, 'smode'=>$smode));

	$edit->printEdit();

?>
<script type="text/javascript">
    function selectUser() {
        var wnd = api.window.open('Find Provider', '<?=CoreUtils::getURL('srv_user.php', array('dskey'=>$dskey));?>');
        wnd.resize(950, 600);
        wnd.center();
        wnd.addEventListener('user_selected', onEvent);
        wnd.show();
    }

    function onEvent(e) {
        var name = e.param.name;
        var id = e.param.id;
        $("#provider").val(name);
        $("#provider_ssn").val(id);
    }

</script>