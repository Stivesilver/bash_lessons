<?php
    Security::init();
    
    $dskey      = io::get('dskey');
    $siymrefid  = io::geti('siymrefid');
    $sprrefid   = io::geti('sprrefid');
    $esy        = io::get('ESY');       
    
    db::execSQL("
        DELETE FROM webset.std_progressreportmst WHERE sprrefid = ".$sprrefid."
    ");

    header('Location: '.CoreUtils::getURL('pr_progrepMain.php', array('dskey'=>$dskey, 'ESY'=>$esy, 'siymrefid'=>$siymrefid)));    
?>