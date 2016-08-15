<?php	
	/**
	* Select Field with Grade Levels
	*
	* @author Nick Ignatushko
	* @copyright Lumen Touch, 2014
	*/
	class FFIDEAGradeLevel extends FFSelect {
		
		/**
		* Class Constructor
		* 
		*/
		public function __construct() {
			parent::__construct();
			$this->caption = 'Grade Level';
			$this->sql("
			    SELECT gl_refid,
			           gl_code
			      FROM c_manager.def_grade_levels
			     WHERE vndrefid = VNDREFID
			     ORDER BY gl_numeric_value, gl_code
			");
		}
		
		/**
		* Creates an instance of this class
		* 
		* @static
		* @return FFIDEAGradeLevel
		*/
		public static function factory() {
			return new FFIDEAGradeLevel();
		}
		
	}
	
?>
