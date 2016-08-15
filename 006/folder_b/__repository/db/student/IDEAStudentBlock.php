<?php

	/**
	 * Class IDEAStudentBlock
	 *
	 * @author Alex Kalevich
	 * @copyright LumenTouch, 2014
	 */
	class IDEAStudentBlock {

		/**
		 * Student Id
		 *
		 * @var $tsRefID
		 */
		private $tsRefID;

		/**
		 * Year student
		 *
		 * @var int
		 */
		protected $stdIEPYear;

		/**
		 * Class Constructor
		 *
		 * @param int $tsRefID
		 * @param int $stdIEPYear
		 * @return IDEAStudentBlock
		 */
		public function __construct($tsRefID = null, $stdIEPYear = null) {
			$this->tsRefID = $tsRefID;
			$this->stdIEPYear = $stdIEPYear;
		}

		/**
		 * Creates and returns an instance of this class.
		 *
		 * @param int $tsRefID
		 * @param int $stdIEPYear
		 * @return IDEAStudentBlock
		 */
		public static function factory($tsRefID = null, $stdIEPYear = null) {
			return new IDEAStudentBlock($tsRefID, $stdIEPYear);
		}

		public function getStudentBlocks($reptype) {
			$blocks = IDEAFormat::getDocBlocks($reptype);
			$inst = IDEAStudentChecker::factory($this->tsRefID, $this->stdIEPYear);
			$resBlocks = array();
			for ($i = 0; $i < count($blocks); $i++) {
				if (isset($blocks[$i]['check_method'])) {
					$method = $blocks[$i]['check_method'];
					// $this->items[$i]['param'], $this->items[$i]['mdlink']
					if ($blocks[$i]['check_param']) {
						$args = current(FileCSV::factory()->setDataAsString($blocks[$i]['check_param'])->toArray());
					} else {
						$args = array();
					}
					$params = call_user_func_array(array($inst, $method), $args);

					if ($params !== null) {
						foreach ($params as $key => $param) {
							if ($key == 'condition') {
								if ($param == 'N') {
									continue 2;
								}
							}
							// change item link
							if ($key == 'link') {
								$link = $param;
							}
							// rename item
							if ($key == 'item') {
								$item = $param;
							}
							// disable item
							if ($key == 'disable') {
								$disable = $param;
							}
						}
					}
				}
				$refid = $blocks[$i]['iepnum'];
				$resBlocks[$refid] = $blocks[$i]['iepdesc'];
			}
			return $resBlocks;
		}
	}
