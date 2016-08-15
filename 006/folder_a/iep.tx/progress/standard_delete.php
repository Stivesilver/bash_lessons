<?php
    Security::init();
    
    $dskey      = io::get('dskey');
    $siymrefid  = io::geti('siymrefid');
    $sprrefid   = io::geti('sprrefid');
    $esy        = io::get('ESY') == 'Y' ? 'Y' : 'N';
    
    db::execSQL("
        DELETE FROM webset_tx.std_sb_progress WHERE sprrefid = ".$sprrefid."
    ");

    header('Location: '.CoreUtils::getURL('standard_main.php', array('dskey'=>$dskey, 'ESY'=>$esy, 'siymrefid'=>$siymrefid)));    
?>