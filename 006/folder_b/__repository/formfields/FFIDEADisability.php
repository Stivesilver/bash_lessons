<?php
	/**
	* Select Field with State Disabilities
	* 
	* @copyright Lumen Touch, 2012
	*/
	class FFIDEADisability extends FFSelect {
		
		/**
		* Class Constructor
		* 
		*/
		public function __construct() {
			parent::__construct();
			$state = VNDState::factory(); 
			
			$this->caption = 'Disabilty';
            $this->sqlField = "EXISTS (SELECT 1 
                                         FROM webset.std_disabilitymst dsb 
                                        WHERE dsb.stdrefid=tsrefid 
                                          AND dsb.dcrefid = ADD_VALUE)";			
			if ($state->code=="IL") {				
				$this->sql("
					SELECT dcrefid,
	                       COALESCE (validvalue, '  ') || ' - ' || dcdesc
	                  FROM webset.statedef_disablingcondition dc
                           LEFT OUTER JOIN webset.glb_validvalues v ON dc.dcrefid = CAST(v.validvalueid as INTEGER) AND valuename = 'IL_Disability_Codes'
	                 WHERE (screfid = " . $state->id . ")
	                   AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
	                 ORDER BY dccode
				");
			} else {
				$this->sql("
					SELECT dcrefid,
                           COALESCE (dccode, '  ') || ' - ' || dcdesc
                      FROM webset.statedef_disablingcondition
                     WHERE (screfid = " . $state->id . ")
                       AND (recdeactivationdt IS NULL or now()< recdeactivationdt)
                     ORDER BY dccode
				");				
			}
		}
		
		/**
		* Creates an instance of this class
		* 
		* @static
		* @return FFIDEADisability
		*/
		public static function factory() {
			return new FFIDEADisability();
		}
		
	}
	
?>
