<?php	
	/**
	* Select Field with Valid Values
	*
	* @copyright Lumen Touch, 2012 
	*/
	class FFIDEAValidValues extends FFMultiSelect {

		/**
		 * SQL Columns
		 * @var string $cols
		 */
		private $cols = 'refid, validvalue';

		/**
		 * Valid Values Area
		 * This is a code word used as filter webset.glb_validvalues.valuename
		 * @var string $cols
		 */
		private $area;
		

		/**
		 * Class Constructor
		 * @param string $area
		 */
		public function __construct($area) {
			parent::__construct();
			$this->area = $area;
			$this->sql(IDEADef::getValidValueSql($this->area, $this->cols));
		}

		/**
		* Class Constructor
		*
		*/
		public function setSQLCols($cols) {
			$this->sql(IDEADef::getValidValueSql($this->area, $this->cols));
		}
		
		/**
		* Creates an instance of this class
		* 
		* @static
		* @return FFIDEAValidValues
		*/
		public static function factory($area) {
			return new FFIDEAValidValues($area);
		}
		
	}
	
?>
