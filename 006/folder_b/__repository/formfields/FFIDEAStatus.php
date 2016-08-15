<?php
	/**
	* Select Field with Active/Inactive choices
	*
	* @copyright Lumen Touch, 2012
	*/
	class FFIDEAStatus extends FFSelect {

		/**
		* Class Constructor
		*
		*/
		public function __construct($caption) {
			parent::__construct();
			$this->caption = $caption;	
			$this->value('Y');
			$this->name('ideastatus');
            $this->sqlField("CASE WHEN NOW() > enddate THEN 'N' ELSE 'Y' END");		
			$this->data(
				array(
					'Y' => 'Active',
					'N' => 'Inactive'
				)
			);
		}

		/**
		* Creates an instance of this class
		*
		* @static
		* @return FFIDEAStatus
		*/
		public static function factory($caption = 'Status') {
			return new FFIDEAStatus($caption);
		}

	}

?>