<?php

	Security::init();

	$name    = $_POST['f_name'];
	$pdfCont = $_POST['f_cont'];

	SystemCore::$FS->rename(CoreUtils::getPhysicalPath($_POST['f_path']), SystemCore::$secDisk . '/Iep/' . $name);

	DBImportRecord::factory('webset_tx.std_fie_arc', 'siepmtrefid')
		->set('pdf_cont',       $pdfCont)
		->set('stdrefid',       io::post('stdrefid'))
		->set('siepmdocdate',   'NOW()', true)
		->set('siepmdocfilenm', $name)
		->set('form_ids',       null)
		->set('lastuser',       db::escape(SystemCore::$userUID))
		->set('lastupdate',     'NOW()', true)
		->import();

	io::ajax('finish', 1);

?>