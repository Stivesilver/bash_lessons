<?php

	/**
	 * Class IDEAFormDefaultsIDMedicateIEP
	 * Add default in IEP School Based Services - Medicaid Reimbursable
	 *
	 */
	class IDEAFormDefaultsIDMedicateIEP extends IDEAFormDefaults implements IDEAFormDefaultsInterface {
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
		 * @return IDEAFormDefaultsIDMedicateIEP
		 */
		public static function factory($tsRefID) {
			return new IDEAFormDefaultsIDMedicateIEP($tsRefID);
		}

		/**
		 * Inits default values
		 *
		 * @param int $tsRefID
		 */
		private function init($tsRefID) {
			$values = db::execSQL("
				SELECT TO_CHAR(ts.stdenrolldt, 'MM/DD/YYYY') as stdenrolldt,
                	   TO_CHAR(ts.stdiepmeetingdt, 'MM/DD/YYYY') as stdiepmeetingdt,
		               TO_CHAR(ts.stdcmpltdt, 'MM/DD/YYYY') as stdcmpltdt,
		               TO_CHAR(ts.stdevaldt, 'MM/DD/YYYY') as stdevaldt,
		               TO_CHAR(ts.stdtriennialdt, 'MM/DD/YYYY') as stdtriennialdt,
		               TO_CHAR(stdenterdt, 'MM/DD/YYYY') as stdenterdt,
		               TO_CHAR(stdexitdt, 'MM/DD/YYYY') as stdexitdt
	              FROM webset.sys_teacherstudentassignment ts
	             WHERE tsrefid = " . $tsRefID
			)->assocAll();
			foreach ($values as $key => $value) {
				$this->values[$key] = $value;
			}
			$this->values['CurrDate'] = "";
		}
	}
?>
