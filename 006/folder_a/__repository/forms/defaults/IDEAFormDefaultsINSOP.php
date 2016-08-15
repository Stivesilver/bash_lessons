<?php

	/**
	 * Class IDEAFormDefaultsINSOP
	 * Add default in Summary of Perfomance Construction

	 */
	class IDEAFormDefaultsINSOP extends IDEAFormDefaults implements IDEAFormDefaultsInterface {
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
		 * @return IDEAFormDefaultsINSOP
		 */ public static function factory($tsRefID) {
			return new IDEAFormDefaultsINSOP($tsRefID);
		}

		/**
		 * Inits default values
		 *
		 * @param int $tsRefID
		 */
		private function init($tsRefID) {
			$studentIN = IDEAStudentIN::factory($tsRefID);
			$disability = $studentIN->getDisability();
			$goals = $studentIN->getPostGoals();
			$this->values['sop_01'] = $this->getSecondDisability($disability);
			$this->values['sop_04'] = $this->getGoalbyArea($goals, 'Education');
			$this->values['sop_20'] = $this->getGoalbyArea($goals, 'Employment');
			$this->values['sop_32'] = $this->getGoalbyArea($goals, 'Living');
		}

		/**
		 * Get Goal by Area
		 *
		 * @param int $tsRefID
		 */
		private function getGoalbyArea($goals, $area) {
			$goal = '';
			for ($i = 0; $i < count($goals); $i++) {
				if (count(explode(strtolower($area), strtolower($goals[$i]["area"])))>1)
					$goal .= $goals[$i]["goal"] . "\r";
			}
			return $goal;
		}
	
		/**
		 * Get second disability
		 *
		 * @param int $tsRefID
		 */
		private function getSecondDisability($disability) {
			$result = array();
			foreach ($disability AS $item) {
				if ($item['type'] == 'Secondary') {
					$result[] = $item['disability'];
				}
			}
			return implode(', ', $result);
		}

	}

?>
