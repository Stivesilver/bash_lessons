<?php

	function saveRecord($RefID, &$data) {

		$xmltext = '
				<record>
					<name>' . io::post('pname') . '</name>
					<role>' . io::post('prole') . '</role>
					<type>' . io::post('ptype') . '</type>
				</record>
			';

		DBImportRecord::factory('webset.disdef_validvalues', 'refid')
			->key('refid', $RefID)
			->set('validvalue', $xmltext)
			->set('valuename', 'DefaultParticipants')
			->set('vndrefid', SystemCore::$VndRefID)
			->set('lastuser', SystemCore::$userUID)
			->set('lastupdate', 'now()', true)
			->import();

	}

?>
