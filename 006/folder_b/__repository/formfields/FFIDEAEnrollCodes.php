<?php	
	/**
	* Select Field with Sp Ed Enrollment Codes
	* 
	* @copyright Lumen Touch, 2012
	*/
	class FFIDEAEnrollCodes extends FFSelect {
		
		/**
		* Class Constructor
		* 
		*/
		public function __construct() {
			parent::__construct();
			$this->caption = 'Sp Ed Enrollment Code';			
			$this->sql("
				SELECT denrefid,
                       dencode || ' - ' || dendesc as enrollcode
                  FROM webset.disdef_enroll_codes district
                       INNER JOIN webset.statedef_enroll_codes state ON state.enrrefid = district.statecode_id
                 WHERE vndrefid = VNDREFID
                   AND (district.enddate IS NULL OR now()<district.enddate)
                 ORDER BY district.seqnum, dencode
			");
		}
		
		/**
		* Creates an instance of this class
		* 
		* @static
		* @return FFIDEAEnrollCodes
		*/
		public static function factory() {
			return new FFIDEAEnrollCodes();
		}
		
	}
	
?>
