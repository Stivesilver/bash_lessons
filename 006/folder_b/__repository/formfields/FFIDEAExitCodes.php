<?php	
	/**
	* Select Field with Sp Ed Exit Codes
	*
	* @copyright Lumen Touch, 2012 
	*/
	class FFIDEAExitCodes extends FFSelect {
		
		/**
		* Class Constructor
		* 
		*/
		public function __construct() {
			parent::__construct();
			$this->caption = 'Sp Ed Exit Code';			
			$this->sql("
				SELECT dexrefid,
                       COALESCE(dexcode || ' - ','') || dexdesc as exitcode
                  FROM webset.disdef_exit_codes district
                       LEFT OUTER JOIN webset.statedef_exitcategories state ON state.secrefid = district.statecode_id
                 WHERE vndrefid = VNDREFID
                   AND (state.recdeactivationdt IS NULL or now()<state.recdeactivationdt)
                   AND (district.enddate IS NULL OR now()<district.enddate)
                 ORDER BY seqnum, dexcode
			");
		}
		
		/**
		* Creates an instance of this class
		* 
		* @static
		* @return FFIDEAExitCodes
		*/
		public static function factory() {
			return new FFIDEAExitCodes();
		}
		
	}
	
?>
