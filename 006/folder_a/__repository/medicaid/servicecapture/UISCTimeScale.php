<?php

	/**
	 * HDM Time Tracking, User Interface Element
	 *
	 * @author Oleg Bychkovski
	 * @copyright Lumen Touch, 2014
	 */
	class UISCTimeScale extends UITimeScale {

		/**
		 * mp_refid
		 *
		 * @var int
		 */
		private $mp_refid;

		/**
		 * Type (MEDICAID_SERVICE or MEDICAID_PROVIDER)
		 *
		 * @var string
		 */
		private $type;

		/**
		 * Class Constructor
		 *
		 * @param int $type
		 * @param int $mp_refid
		 */
		public function __construct($type = SCTimeScale::MEDICAID_SERVICE, $mp_refid = null) {
			$this->type = $type;
			$this->mp_refid = $mp_refid;
			$timeScale = new SCTimeScale($type, $mp_refid);
			parent::__construct($timeScale);
		}

		/**
		 * Returns an instance of this class
		 *
		 * @param int $type
		 * @param int $mp_refid
		 * @return UISCTimeScale
		 */
		public static function factory($type = SCTimeScale::MEDICAID_SERVICE, $mp_refid = null) {
			return new UISCTimeScale($type, $mp_refid);
		}

		/**
		 * Returns HTML code of the element
		 *
		 * @param DBConnection $db
		 * @return string
		 */
		public function toHTML($db = null) {
			$acc = IDEAListParts::createListContent(
				'related',
				'std.stdrefid',
				false,
				false,
				null,
				db::execSQL("
					SELECT umrefid
			          FROM webset.med_disdef_providers
			         WHERE mp_refid = " . $this->mp_refid
				)->getOne()
				/*
				($this->type == SCTimeScale::MEDICAID_SERVICE ?
					null :
					" AND EXISTS(SELECT 1 FROM webset.std_srv_rel AS srv WHERE tsrefid = srv.stdrefid AND mp_refid IS NOT NULL)"
				)*/
			);

			$sel_sdt = UICustomHTML::factory(
				FFMultiSelect::factory()
					->rows(20)
					->name('select_item_student')
					->setSearchList($acc)
			)
				->css('display', 'none')
				->toHTML();

			$sel_sdt_not_visited = UICustomHTML::factory(
				FFMultiSelect::factory()
					->rows(20)
					->name('select_item_student_not_visited')
					->setSearchList($acc)
			)
				->css('display', 'none')
				->toHTML();

			$html = parent::toHTML() . $sel_sdt . $sel_sdt_not_visited;
			$html .= '
				<script>
					UITimeScale.get(' . json_encode($this->name) . ').addEventListener(
						UITimeScaleEvent.ENTERED_TIME_INTERVAL_OVERLAP,
						function(e) {
							e.preventDefault();
							PageAPI.singleton().alert("Warning: This service record overlaps the time period of another record. This record was NOT saved. Please correct the time period of the records.");
						}
					)
				</script>
			';
			return $html;

		}
	}

?>