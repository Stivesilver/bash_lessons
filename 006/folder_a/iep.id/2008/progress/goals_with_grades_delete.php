<?php
	Security::init();

	$dskey = io::get('dskey');
	$siymrefid = io::geti('siymrefid');
	$sprrefid = io::geti('sprrefid');
	$esy = io::get('ESY') == 'Y' ? 'Y' : 'N';

	db::execSQL("
        DELETE FROM webset.std_oth_progress WHERE sprrefid = " . $sprrefid . "
    ");

	header('Location: ' . CoreUtils::getURL('goals_with_grades.php', array('dskey' => $dskey, 'ESY' => $esy, 'siymrefid' => $siymrefid)));
?>