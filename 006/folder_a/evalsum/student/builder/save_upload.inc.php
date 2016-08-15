<?php

	function save_upload($RefID, &$data, $array) {
		$dskey = $array['dskey'];
		$ds = DataStorage::factory($dskey);
		$tsRefID = $ds->safeGet('tsRefID');
		$title = io::post('uploaded_title');
		$file = SystemCore::$tempPhysicalRoot . '/' . basename(io::post('uploaded_filename'));

		if (file_exists($file)) {
			$dir  = SystemCore::$physicalRoot . '/uplinkos/temp';
			$filename  = $tsRefID . "_" . SystemCore::$userID . "." . pathinfo($file, PATHINFO_EXTENSION);
			copy($file, $dir . $filename);
			$file_cont = SystemCore::$FS->fileOpen($file)->read();
			$file_cont = $filename . "_filename_divider_". base64_encode($file_cont);

			DBImportRecord::factory('webset.es_std_esarchived', 'esarefid')
				->set('esaname', $title)
				->set('uploaded_file', $file_cont)
				->set('stdrefid', $tsRefID)
				->set('esadate', 'NOW()', true)
				->setUpdateInformation()
				->import(DBImportRecord::INSERT_ONLY);
		} else {
			io::err('file has not been uploaded');
		}
	}

?>
