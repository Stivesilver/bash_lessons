<?php

	function update_cover_page($RefID, &$data) {
		IDEAStudentRegistry::saveStdKey(io::post('tsRefID'), 'ct_iep', 'cover_page_high_school', io::post('high_school'), io::post('stdIEPYear'));
		IDEAStudentRegistry::saveStdKey(io::post('tsRefID'), 'ct_iep', 'hscredits', io::post('hscredits'), io::post('stdIEPYear'));
	}

?>
 
