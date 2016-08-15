<?php

	/**
	 * Class IDEAFormDefaultsILDisabilities
	 * Add default in IL Disabilities
	 * Used in Parent/Guardian Notification of Conference Recommendations
	 *
	 */
	class IDEAFormDefaultsILDisabilities extends IDEAFormDefaults implements IDEAFormDefaultsInterface {
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
		 * @return IDEAFormDefaultsILDisabilities
		 */
		public static function factory($tsRefID) {
			return new IDEAFormDefaultsILDisabilities($tsRefID);
		}

		/**
		 * Inits default values
		 *
		 * @param int $tsRefID
		 */
		private function init($tsRefID) {
			$disab = db::execSQL("
				SELECT plpgsql_recs_to_str (
					'SELECT COALESCE(dcdesc,'''') AS column
                       FROM webset.std_disabilitymst sd
                        	INNER JOIN webset.statedef_disablingcondition st ON st.dcrefid = sd.dcrefid
                 	  WHERE sd.stdrefid = ". $tsRefID ."
                      ORDER BY sdtype,sdrefid desc ', ', '
                )
			")->getOne();
			$this->values['d2'] = $disab;
		}
	}
?>
