<?php

    function processYears($RefID, &$data) {

        $student = IDEAStudent::factory($data['stdrefid']);

        //Set all student IEP year as none current
        DBImportRecord::factory('webset.std_iep_year', 'stdrefid')
            ->key('stdrefid', $data['stdrefid'])
            ->set('siymcurrentiepyearsw', null)
            ->import();

        //Set newly added IEP yeas as current
        DBImportRecord::factory('webset.std_iep_year', 'siymrefid')
            ->key('siymrefid', $RefID)
            ->set('siymcurrentiepyearsw', 'Y')
            ->set('lastuser', db::escape(SystemCore::$userUID))
            ->set('lastupdate', 'NOW()', true)
            ->import();

        if (IDEACore::disParam(89) == 'Y') {
            //Update IEP Dates and register Dates change
            IDEAStudentEvent::addEvent($data['stdrefid'], '<=.IEPMTDate.=>', $data['siymiepbegdate'], $student->getDate('stdiepmeetingdt'));
            IDEAStudentEvent::addEvent($data['stdrefid'], '<=.IEPPrjAnlRvwDate.=>', $data['siymiependdate'], $student->getDate('stdcmpltdt'));

            DBImportRecord::factory('webset.sys_teacherstudentassignment', 'tsrefid')
                ->key('tsrefid', $data['stdrefid'])
                ->set('stdiepmeetingdt', $data['siymiepbegdate'])
                ->set('stdcmpltdt', $data['siymiependdate'])
                ->set('lastuser', db::escape(SystemCore::$userUID))
                ->set('lastupdate', 'NOW()', true)
                ->import();
        }
    }

?>
