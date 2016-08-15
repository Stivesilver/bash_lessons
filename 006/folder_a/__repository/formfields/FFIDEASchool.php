<?php
	
	/**
	* Select Field with School Buildings
	* 
	* @copyright Lumen Touch, 2012
	*/
	class FFIDEASchool extends FFSelect {
		
		/**
		* Class Constructor
		* 
		*/
		public function __construct($reporting_school) {
			parent::__construct();
			$this->caption = 'Attending School';
			
			if (IDEACore::websisHere() and !$reporting_school) {
				$this->sqlField = 'std.att_wsds_refid';
				$this->sql("
					SELECT wsds_refid,
                           wsds_school_name
                     FROM (SELECT DISTINCT on (wsds_school_name)
                                  wsd.wsds_refid,
                                  wsds_school_name
	                         FROM c_manager.def_buildings def
           	                      INNER JOIN c_manager.def_websis_schools wsd ON def.wsds_refid = wsd.wsds_refid
	                        WHERE vndrefid = VNDREFID
                            ORDER BY 2) as vou
				");
			} else {
				$this->sqlField = 'std.vourefid';
				$this->sql("
					SELECT vourefid,
                           vouname
                     FROM (SELECT vourefid,
                                  vouname
         	                 FROM sys_voumst
                            WHERE vndrefid = VNDREFID
                            ORDER by vouname) as vou
				");				
			}
		}
		
		/**
		* Creates an instance of this class
		* 
		* @static
		* @return FFIDEASchool
		*/
		public static function factory($reporting_school = false) {
			return new FFIDEASchool($reporting_school);
		}
		
	}
	
?>
