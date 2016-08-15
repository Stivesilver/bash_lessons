<?php
	Security::init();

    $SQL = "
        SELECT stdrefid, stdfnm, stdlnm
          FROM webset.dmg_studentmst
         WHERE vndrefid = VNDREFID
           AND COALESCE(std_deleted_sw, 'N') = 'N'
           AND " . io::get('field') . " = '" . io::get('id') . "'
           AND COALESCE(" . io::get('field') . ", '')!=''
           AND stdrefid!=" . (io::get('stdrefid')==''?'0':io::get('stdrefid')) . "
    ";

    $result = db::execSQL($SQL);       

    if (!$result->EOF) {
        $message = 'This ID # already belongs to ' .  $result->fields['stdfnm'] . ' ' . $result->fields['stdlnm'] . ', Lumen ID #: ' . $result->fields['stdrefid'];
	    print UIMessage::factory($message, UIMessage::WARNING)->toHTML();
    }; 
    
     
    
?>