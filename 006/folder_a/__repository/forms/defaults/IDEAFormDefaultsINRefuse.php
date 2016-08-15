<?php

	/**
	 * Class IDEAFormDefaultsINRefuse
	 *
	 * @author Alex Kalevich
	 * Created 23-10-2014
	 */
	class IDEAFormDefaultsINRefuse extends IDEAFormDefaults implements IDEAFormDefaultsInterface {
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
		 * @return IDEAFormDefaultsINRefuse
		 */
		public static function factory($tsRefID) {
			return new IDEAFormDefaultsINRefuse($tsRefID);
		}

		/**
		 * Inits default values
		 *
		 * @param int $tsRefID
		 */
		private function init($tsRefID) {
			$studentIN = IDEAStudentIN::factory($tsRefID);
			$iepyear = $studentIN->get('stdiepyear');
			$d = $studentIN->getConstructionData('141', $iepyear);
			$d1 = $studentIN->getEligibility();
			$lre = $studentIN->getLREQuestions();
			$d2 = (isset($lre[0]) ? $lre[0]['qarejectiondesc'] : '');
			$d2 .= (isset($lre[1]) ? "\n" . $lre[1]['qarejectiondesc'] : '');
			$d3 = (isset($lre[count($lre) - 1]) ? $lre[count($lre) - 1]['qarejectiondesc'] : '');
			$this->values['d'] = $d['eval_description'];
			$this->values['d1'] = $d1[0]['edesc'];
			$this->values['d2'] = $d2;
			$this->values['d3'] = $d3;
		}
	}
?>
