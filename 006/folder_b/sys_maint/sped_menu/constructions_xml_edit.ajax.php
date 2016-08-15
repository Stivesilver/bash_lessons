<?php

	Security::init();

	$formID = io::geti('id');

	$url = IDEAFormEditor::factory('webset.sped_constructions', 'cnrefid', 'cnname', 'cnbody', $formID)->getUrlPanel();

	header('Location: ' . $url);

?>
