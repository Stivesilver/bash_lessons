<?php
	
	/**
	 * Class provides methods for working with service providers
	 *
	 * @author Michael Rogov
	 * @copyright LumenTouch, 2014
	 */
	class MedicaidServiceProviders extends RegularClass{
		
		/**
		 * Class Constructor
		 * 
		 * @return MedicaidServiceProviders
		 */
		public function __construct() {
			parent::__construct();
		}
		
		/**
		 * Creates and returns an instance of this class. 
		 * 
		 * @return MedicaidServiceProviders
		 */
		public static function factory() {
			return new MedicaidServiceProviders();
		}

		/**
		 * Creates service Providers
		 *
		 * @return void
		 */
		public function createServiceProviders() {
			$sql = "
				SELECT umrefid,
					   umfirstname,
				       umlastname,
					   username
				  FROM (SELECT DISTINCT ON (srv.umrefid) srv.umrefid,
				               umfirstname,
				               umlastname,
							   " . IDEAParts::get('username') . " AS username
						  FROM webset.sys_teacherstudentassignment ts
							   " . IDEAParts::get('studentJoin') . "
							   INNER JOIN webset.std_srv_rel AS srv ON ts.tsrefid = srv.stdrefid
							   INNER JOIN public.sys_usermst AS usr ON srv.umrefid = usr.umrefid
						 WHERE std.vndrefid = VNDREFID
						   AND " . IDEAParts::get('stdActive') . "
						   AND " . IDEAParts::get('spedActive') . "
					   ) as t
				 ORDER BY 2
			";
			$listProviders = $this->execSQL($sql)
				->assocAll();

			foreach ($listProviders as $provider) {
				DBImportRecord::factory('webset.med_disdef_providers', 'mp_refid', $this->db)
					->key('mp_id', null)
					->key('mpt_refid', null)
					->key('umrefid', $provider['umrefid'])
					->key('mp_fname', $provider['umfirstname'])
					->key('mp_lname', $provider['umlastname'])
					->set('mp_status_sw', 'A')
					->set('vndrefid', SystemCore::$VndRefID)
					->set('lastuser', SystemCore::$userUID)
					->set('lastupdate', 'NOW()', true)
					->import(DBImportRecord::INSERT_ONLY);
			}
		}
	}
?>