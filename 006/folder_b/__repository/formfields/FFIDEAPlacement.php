<?php
	/**
	* Select Field with State Placements
	* 
	* @copyright Lumen Touch, 2012
	*/
	class FFIDEAPlacement extends FFSelect {
		
		/**
		* Class Constructor
		* 
		*/
		public function __construct() {
			parent::__construct();
			$state = VNDState::factory(); 
			
			$this->caption = 'Placement';
			$this->sql("
				SELECT plc.spcrefid,
					   plc.spccode || ' - ' || plc.spcdesc
				  FROM webset.statedef_placementcategorycode plc
					   INNER JOIN webset.statedef_placementcategorycodetype ec ON plc.spctrefid = ec.spctrefid
				 WHERE plc.screfid = ".VNDState::factory()->id ."
				   AND (plc.recdeactivationdt IS NULL OR now()< plc.recdeactivationdt)
				 ORDER BY 2, plc.spccode
            ");
			if ($state->code == "TX") {
				$this->sqlField = "
					EXISTS (SELECT 1 
							  FROM webset_tx.std_instruct_arrange plm 
							 WHERE plm.std_refid = tsrefid
							   AND plm.placement = ADD_VALUE)
				";
			} else {
				$this->sqlField = "
					EXISTS (SELECT 1 
							  FROM webset.std_placementcode plm 
							 WHERE plm.stdrefid = tsrefid
							   AND plm.spcrefid = ADD_VALUE)
				";
			}
		}
		
		/**
		* Creates an instance of this class
		* 
		* @static
		* @return FFIDEAPlacement
		*/
		public static function factory() {
			return new FFIDEAPlacement();
		}
		
	}
	
?>
