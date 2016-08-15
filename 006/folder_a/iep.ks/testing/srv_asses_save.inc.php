<?php

	function addAsses() {
		$marefid      = explode(',', io::post('marefid'));
		$saashortdesc = io::post('saashortdesc');
		$aaarefid     = io::get('aaarefid');
		$stdrefid     = io::get('stdrefid');

		foreach ($marefid as $id) {
			DBImportRecord::factory('webset.std_assess_acc', 'saarefid')
				->set('marefid',      $id)
				->set('saashortdesc', $saashortdesc)
				->set('aaarefid',     $aaarefid)
				->set('stdrefid',     $stdrefid)
				->set('lastuser',     SystemCore::$userUID)
				->set('lastupdate',  'now()', true)
				->import();
		}
	}

?>