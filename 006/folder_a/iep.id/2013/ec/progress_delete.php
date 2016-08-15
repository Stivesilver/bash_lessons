<?php
	Security::init();

	$dskey = io::get('dskey');
	$siymrefid = io::geti('siymrefid');
	$sprrefid = io::geti('sprrefid');
	$esy = io::get('ESY') == 'Y' ? 'Y' : 'N';

	db::execSQL("
        DELETE FROM webset.std_general WHERE refid = " . $sprrefid . "
    ");

	header('Location: ' . CoreUtils::getURL('progress_list.php', array('dskey' => $dskey, 'ESY' => $esy, 'siymrefid' => $siymrefid)));
?>