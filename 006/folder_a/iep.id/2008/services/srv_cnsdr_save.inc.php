<?php

	function updateCnsdr($RefID, &$data) {
		IDEAStudentRegistry::saveStdKey(io::post('tsRefID'), 'id_iep', 'considerations', io::post('selected'), io::post('stdIEPYear'));
	}
	
?>
