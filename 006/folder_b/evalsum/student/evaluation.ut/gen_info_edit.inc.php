<?php

    function saveTSDates($RefID, &$data) {

        $student = IDEAStudent::factory(io::post('tsrefid'));

        //Set all student IEP year as none current
        DBImportRecord::factory('webset.sys_teacherstudentassignment', 'tsrefid')
            ->key('tsrefid', io::post('tsrefid'))
            ->set('parentrightdt', io::post('parentrightdt'))
            ->set('stdprocsafeguarddt', io::post('stdprocsafeguarddt'))
            ->import();

    }

?>
