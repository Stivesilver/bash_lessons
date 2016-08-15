<?php	
	/**
	* Select Field with Evaluation Screening Type
	*
	*/
	class FFIDEAEvalScreenType extends FFSelect {
		
		/**
		* Class Constructor
		* 
		*/
		public function __construct() {
			parent::__construct();
			$this->caption = 'Area';
			$this->sql("
				SELECT scrrefid,
					   scrdesc
				  FROM webset.es_statedef_screeningtype
				 WHERE screfid = " . VNDState::factory()->id . "
				   AND (enddate IS NULL OR now()< enddate)
				 ORDER BY scrseq
			");
		}
		
		/**
		* Creates an instance of this class
		* 
		* @static
		* @return FFIDEAEvalScreenType
		*/
		public static function factory() {
			return new FFIDEAEvalScreenType();
		}
		
	}
	
?>
