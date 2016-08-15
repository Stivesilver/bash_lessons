<?php
    Security::init();

    function getDisplayValue($defrefid, $paramValue) {
        
        $SQL = "
            SELECT dckey,
                   dcsql
              FROM webset.def_discontrol
             WHERE dcrefid = $defrefid
        ";

        $result = db::execSQL($SQL);
        
        if ($result->fields['dckey']=='SQL') {
            $dcsql  = $result->fields['dcsql'];
            $dcsql = str_replace('AF_STATEREFID',  VNDState::factory()->id, $dcsql);
            $dcsql = str_replace('AF_VNDREFID',    SystemCore::$VndRefID, $dcsql);

            $result = db::execSQL($dcsql);
            while (!$result->EOF) {
                if ($result->fields[0]==$paramValue) return $result->fields[1];
                $result->MoveNext();
            }

        } else {
            if ($paramValue=='Y') {
                return 'Yes';
            } elseif ($paramValue=='N') {
                return 'No';
            }
        }

    }
    
    //Updates parameter value
    DBImportRecord::factory('webset.disdef_control', 'crefid')
       ->key('defrefid', io::post('defrefid'))
       ->key('vndrefid', SystemCore::$VndRefID)
       ->set('paramvalue', db::escape(io::post('paramvalue')))
       ->set('displvalue', db::escape(getDisplayValue(io::post("defrefid"), io::post("paramvalue"))))
       ->set('lastuser', db::escape(SystemCore::$userUID))
       ->set('lastupdate', 'NOW()', true)
       ->import();
    
    Header('Location: ' . CoreUtils::getURL('vnd_control.php', array('category' => io::get('category'))));
?>
