<?php

	Security::init();

	IDEAStudentRegistry::saveStdKey(
		io::post('tsRefID'),
		io::post('keyGroup'),
		io::post('keyName'),
		io::post('text'),
		io::post('stdIEPYear')
	);

	io::ajax('res', 1);

?>