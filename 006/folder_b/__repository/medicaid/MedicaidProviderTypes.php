<?php

	/**
	 * Class provides methods for working with services and provider types
	 *
	 * @author Michael Rogov
	 * @copyright LumenTouch, 2014
	 */
	class MedicaidProviderTypes extends RegularClass {

		/**
		 * Class Constructor
		 *
		 * @return MedicaidProviderTypes
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Creates and returns an instance of this class.
		 *
		 * @return MedicaidProviderTypes
		 */
		public static function factory() {
			return new MedicaidProviderTypes();
		}

		/**
		 * Creates services by provider type
		 *
		 * @param int $mpt_refid
		 * @return void
		 */
		public function createServicesByProviderType($mpt_refid) {
			$sql = "
				SELECT mds_refid
				  FROM webset.med_disdef_services AS mds
				 WHERE vndrefid = VNDREFID
			";
			$services = $this->execSQL($sql)
				->assocAll();

			foreach ($services as $service) {
				DBImportRecord::factory('webset.med_disdef_provider_types_services', 'mdpts_refid', $this->db)
					->key('mpt_refid', $mpt_refid)
					->key('mds_refid', $service['mds_refid'])
					->set('mdpts_status_sw', 'Y')
					->set('vndrefid', SystemCore::$VndRefID)
					->set('lastupdate', 'NOW()', true)
					->set('lastuser', SystemCore::$userUID)
					->import(DBImportRecord::INSERT_ONLY);
			}
		}

		/**
		 * Returns list of provider types by service
		 *
		 * @param int $mds_refid
		 * @return array
		 */
		public function getProviderTypesByService($mds_refid) {
			CoreUtils::checkArguments('int');

			if ($mds_refid != 0) {
				$sql = "
					SELECT mpt.mpt_refid, mpt.mpt_code, mdpts_status_sw
					  FROM webset.med_disdef_provider_types AS mpt
						   INNER JOIN webset.med_disdef_provider_types_services AS mdpts ON mdpts.mpt_refid = mpt.mpt_refid
					 WHERE mpt.vndrefid = VNDREFID
					   AND mdpts.mds_refid = " . $mds_refid;
				$provider_types = $this->execSQL($sql)
					->assocAll();

				return $provider_types;
			} else {
				$sql = "
					SELECT mpt_refid, mpt_code, NULL AS mdpts_status_sw
					  FROM webset.med_disdef_provider_types
					 WHERE vndrefid = VNDREFID
				";
				$provider_types = $this->execSQL($sql)
					->assocAll();

				return $provider_types;
			}
		}
	}
?>