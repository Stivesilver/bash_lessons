<?php

	/**
	 * Class IDEAFormDefaultsAOM
	 * Add default 18th DOB to Student Forms
	 *
	 */
	class IDEAFormDefaultsAOM extends IDEAFormDefaults implements IDEAFormDefaultsInterface {
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
		 * @return IDEAFormDefaultsAOM
		 */
		public static function factory($tsRefID) {
			return new IDEAFormDefaultsAOM($tsRefID);
		}

		/**
		 * Inits default values
		 *
		 * @param int $tsRefID
		 */
		private function init($tsRefID) {
			$SQL = "
				SELECT TO_CHAR(stdDOB + interval '18 year', 'MM/DD/YYYY')
				  FROM webset.sys_teacherstudentassignment t0
				       INNER JOIN webset.dmg_studentmst t1 ON t0.stdrefid = t1.stdrefid
				 WHERE tsrefid = " . $tsRefID . "
			";
			$this->values['CurrentDate'] = db::execSQL($SQL)->getOne();
		}
	}
?>
