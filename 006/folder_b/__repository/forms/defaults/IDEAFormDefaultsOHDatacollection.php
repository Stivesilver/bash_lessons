<?php

	/**
	 * Class IDEAFormDefaultsOHDatacollection
	 * Add default in Datacollection
	 *
	 */
	class IDEAFormDefaultsOHDatacollection extends IDEAFormDefaults implements IDEAFormDefaultsInterface {
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
		 * @return IDEAFormDefaultsOHDatacollection
		 */
		public static function factory($tsRefID) {
			return new IDEAFormDefaultsOHDatacollection($tsRefID);
		}

		/**
		 * Inits default values
		 *
		 * @param int $tsRefID
		 */
		private function init($tsRefID) {
			$years = db::execSQL("
				SELECT CASE WHEN now()> (to_char(now(), 'yyyy') || '-06-01')::timestamp THEN to_char(now(), 'yyyy') ELSE to_char(now() - interval '1 year', 'yyyy') END as beg_year,
                       CASE WHEN now()> (to_char(now(), 'yyyy') || '-06-01')::timestamp THEN to_char(now() + interval '1 year', 'yyyy') ELSE to_char(now(), 'yyyy') END as end_year
			")->assocAll();
			$this->values['schoolyear'] = $years[0]['beg_year'];
			$this->values['yeardecsription'] = $years[0]['end_year'];
		}
	}
?>
