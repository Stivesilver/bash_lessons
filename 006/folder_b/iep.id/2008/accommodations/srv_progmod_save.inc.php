<?php

    function updateProgmod($RefID, &$data) {
        $modID      = explode(',', io::post('modifications'));
        $stdIEPYear = io::post('stdIEPYear');
        $tsRefID    = io::get('tsRefID');
        $student    = new IDEAStudent($tsRefID);

        foreach ($modID as $id) {
            DBImportRecord::factory('webset.std_srv_progmod', 'ssmrefid')
                ->set('stsrefid',   $id)
                ->set('stdrefid',   $data['stdrefid'])
                ->set('bcpdesc',    $data['bcpdesc'])
                ->set('iepyear',    $stdIEPYear)
                ->set('ssmbegdate', $student->getDate('stdcmpltdt'))
                ->set('lastuser',   SystemCore::$userUID)
                ->set('lastupdate', 'now()', true)
                ->import();
        }

    }

?>