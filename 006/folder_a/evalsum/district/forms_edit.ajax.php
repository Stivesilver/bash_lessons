<?php

	Security::init();

	$formID = io::geti('id');

	$url = IDEAFormEditor::factory('webset.es_disdef_evalforms', 'efrefid', 'form_title', 'form_xml', $formID, true)->getUrlPanel();

	header('Location: ' . $url);

?>
