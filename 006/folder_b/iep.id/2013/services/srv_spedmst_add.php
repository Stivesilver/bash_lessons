<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = new IDEAStudent($tsRefID);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$servId = 'stsrefid';

	$previous = db::execSQL("
	    SELECT *
		  FROM webset.std_srv_sped
	      WHERE stdrefid = " . $tsRefID . "
		    AND iepyear = " . $stdIEPYear . "
	 	  ORDER BY 1 DESC
	")->assoc();

	$servSQL = "
		SELECT stsrefid, COALESCE(stscode || ' - ' , '') || stsdesc
		  FROM webset.statedef_services_sped
		 WHERE screfid = " . VNDState::factory()->id . "
		   AND (recdeactivationdt>now() or recdeactivationdt is Null)
		 ORDER BY CASE lower(stsdesc) WHEN 'other' THEN 2 ELSE 1 END, 2
    ";

	$naswSQL = "
		SELECT stsrefid
		  FROM webset.statedef_services_sped
		 WHERE screfid = " . VNDState::factory()->id . "
		   AND nasw = 'Y'
    ";

	$othSQL = "
		SELECT stsrefid
		  FROM webset.statedef_services_sped
		 WHERE screfid = " . VNDState::factory()->id . "
		   AND (recdeactivationdt>now() or recdeactivationdt is Null)
		   AND substring(lower(stsdesc), 1, 5) = 'other'
    ";

	$id_oth = db::execSQL($othSQL)->indexAll();
	$id_na = db::execSQL($naswSQL)->indexAll();

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit Special Education Services';

	$edit->setSourceTable('webset.std_srv_sped', 'ssmrefid');

	$edit->addGroup('General Information');

	$edit->addControl("Order #", "integer")
        ->sqlField('order_num')
        ->value((int) db::execSQL("
                    SELECT max(order_num)
                      FROM webset.std_srv_sped
                     WHERE stdrefid = " . $tsRefID . "
					   AND iepyear = ". $stdIEPYear ."
                ")->getOne() + 1
        )
        ->size(5);

	$edit->addControl('Service/Related Service', 'select')
		->sqlField($servId)
		->name($servId)
		->sql($servSQL)
		->emptyOption(true)
		->req();

	$edit->addControl('Specify')
		->sqlField('stsother')
		->showIf($servId, $id_oth)
		->size(50);

	$edit->addControl('Position Responsible')
		->sqlField('ssmteacherother')
		->value($previous['ssmteacherother'])
		->hideIf($servId, $id_na)
		->size(50);

	$edit->addControl('Service Implementor')
		->sqlField('impl_oth')
		->value($previous['impl_oth'])
		->hideIf($servId, $id_na)
		->size(50);

	$edit->addControl('Location', 'select')
		->sqlField('ssmclasstype')
		->name('ssmclasstype')
		->value($previous['ssmclasstype'])
		->sql("
            SELECT crtrefid, crtdesc
			  FROM webset.disdef_location
			 WHERE (enddate>now() or enddate is Null)
			   AND vndrefid = VNDREFID
			 ORDER BY CASE substring(lower(crtdesc), 1, 5)  WHEN 'other' THEN 'z' ELSE crtdesc END
        ")
		->emptyOption(true)
		->hideIf($servId, $id_na);

	$edit->addControl('Service Time (minutes)', 'integer')
		->sqlField('minutes')
		->size(5)
		->hideIf($servId, $id_na);

	$edit->addControl('Frequency', 'select')
		->sqlField('ssmfreq')
		->name('ssmfreq')
		->value(1)
		->sql("
			SELECT sfrefid, sfdesc
			  FROM webset.disdef_frequency
			 WHERE (enddate>now() or enddate is Null)
			   AND vndrefid = " . $_SESSION["s_VndRefID"] . "
			 ORDER BY sfdesc
        ")
		->emptyOption(true)
		->hideIf($servId, $id_na);

	$edit->addControl('Start Date', 'date')
		->sqlField('ssmbegdate')
		->value($student->getDate('stdenrolldt'))
		->hideIf($servId, $id_na);

	$edit->addControl('Ending Date', 'date')
		->sqlField('ssmenddate')
		->value($student->getDate('stdcmpltdt'))
		->hideIf($servId, $id_na);


	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');

	$edit->finishURL = CoreUtils::getURL('srv_spedmst.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('srv_spedmst.php', array('dskey' => $dskey));

	$edit->printEdit();
?>
