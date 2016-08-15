<?PHP
	Security::init();

	$refids = explode(',', io::post('RefID', true));

	foreach ($refids as $refid) {
		$data = db::execSQL("
			SELECT mfcrefid,
			       mfcprefid,
                   mfcdoctitle,
                   onlythisip,
                   fb_content,
                   stf.lastuser,
                   stf.lastupdate
              FROM webset.statedef_forms AS stf
             WHERE mfcrefid = $refid
		")->assoc();

		DBImportRecord::factory('webset.disdef_forms', 'dfrefid')
			->set('mfcprefid', $data["mfcprefid"])
			->set('title', $data["mfcdoctitle"])
			->set('fb_content', $data["fb_content"])
			->set('fb_type', 1)
			->set('vndrefid', SystemCore::$VndRefID)
			->setUpdateInformation()
			->import(DBImportRecord::UPDATE_OR_INSERT);
	}
?>
