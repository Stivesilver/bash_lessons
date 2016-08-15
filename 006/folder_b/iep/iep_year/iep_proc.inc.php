<?php

    function processYears($RefID, &$data) {

        $student = new IDEAStudent($data['stdrefid']);
	    db::beginTrans();
        # Set all student IEP year as none current
        DBImportRecord::factory('webset.std_iep_year', 'stdrefid')
            ->key('stdrefid', $data['stdrefid'])
            ->set('siymcurrentiepyearsw', null)
            ->import();

        # Set newly added IEP yeas as current
        DBImportRecord::factory('webset.std_iep_year', 'siymrefid')
            ->key('siymrefid', $RefID)
            ->set('siymcurrentiepyearsw', 'Y')
	        ->setUpdateInformation()
            ->import();

        # Update IEP Dates and register Dates change
        IDEAStudentEvent::addEvent($data['stdrefid'], '<=.IEPMTDate.=>', $data['siymiepbegdate'], $student->getDate('stdiepmeetingdt'));
        IDEAStudentEvent::addEvent($data['stdrefid'], '<=.IEPInitDate.=>', $data['siymiepbegdate'], $student->getDate('stdenrolldt'));
        IDEAStudentEvent::addEvent($data['stdrefid'], '<=.IEPPrjAnlRvwDate.=>', $data['siymiependdate'], $student->getDate('stdcmpltdt'));

        DBImportRecord::factory('webset.sys_teacherstudentassignment', 'tsrefid')
            ->key('tsrefid', $data['stdrefid'])
            ->set('stdiepmeetingdt', $data['siymiepbegdate'])
            ->set('stdenrolldt', $data['siymiepbegdate'])
            ->set('stdcmpltdt', $data['siymiependdate'])
            ->setUpdateInformation()
            ->import();

        if (IDEAFormat::get('id') == 3) {

            DBImportRecord::factory('webset.sys_teacherstudentassignment', 'tsrefid')
                ->key('tsrefid', $data['stdrefid'])
                ->set(
	                'stdtriennialdt',
	                "CASE
                        WHEN '" . $data['siymiepbegdate'] . "'::DATE < stdtriennialdt
                        THEN stdtriennialdt
                        ELSE NULL
                     END",
	                true
                )
                ->set('stddraftiepcopydt', null)
                ->set('stdiepcopydt', null)
                ->set('addcomments', null)
                ->set('lastuser', SystemCore::$userUID)
                ->set('lastupdate', 'NOW()', true)
                ->import();
        }
	    db::commitTrans();
    }
?>
