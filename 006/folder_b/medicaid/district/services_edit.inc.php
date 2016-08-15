<?php
	function postSave($RefID, &$data) {
		$ds = DataStorage::factory(io::get('dskey'));
		$provider_types = $ds->get('provider_types');
		foreach ($provider_types as $provider_type) {
			DBImportRecord::factory('webset.med_disdef_provider_types_services', 'mdpts_refid')
				->key('mpt_refid', $provider_type['mpt_refid'])
				->key('mds_refid', $RefID)
				->set('mdpts_status_sw', io::get('mpt_refid_' . $provider_type['mpt_refid']))
				->set('vndrefid', SystemCore::$VndRefID)
				->set('lastupdate', 'NOW()', true)
				->set('lastuser', SystemCore::$userUID)
				->import();
		}
	}
?>