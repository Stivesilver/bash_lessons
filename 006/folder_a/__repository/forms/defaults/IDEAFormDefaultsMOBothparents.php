<?php

	/**
	 * Class IDEAFormDefaultsMOBothparents
	 * Add Default Both Parents
	 *
	 */
	class IDEAFormDefaultsMOBothparents extends IDEAFormDefaults implements IDEAFormDefaultsInterface {
		/**
		 * Constructor
		 *
		 * @param int $tsRefID
		 */
		public function __construct($tsRefID) {
			parent::__construct($tsRefID);
			$this->init($tsRefID);
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param int $tsRefID
		 * @return IDEAFormDefaultsMOBothparents
		 */
		public static function factory($tsRefID) {
			return new IDEAFormDefaultsMOBothparents($tsRefID);
		}

		/**
		 * Inits default values
		 *
		 * @param int $tsRefID
		 */
		private function init($tsRefID) {
			$this->values['ParentName'] = $this->values['ParentsBoth'];
			$this->values['CurrDate'] = "";
		}
	}
?>
