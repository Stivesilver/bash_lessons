<?php

	/**
	 * Student Name.
	 * The input element with dropdown list of matched students
	 *
	 * @copyright 2015, LumenTouch
	 * @author Alex Kalevich
	 */
	class FFIDEAStudentName extends FFInputDropList {

		/**
		 * Class Constructor
		 *
		 * @param string $sqlAlias
		 * @param int $saveType
		 */
		public function __construct($sqlAlias = null, $saveType = null) {
			parent::__construct('Student Name', $saveType);
			$this->append('&nbsp;[Last], [First]');
			$this->searchMethod(FormFieldMatch::START_FROM);

			if ($sqlAlias === null || $sqlAlias == '') {
				$this->sqlField("(COALESCE(stdlnm, '') || ', ' || COALESCE(stdfnm, ''))");
			} else {
				$this->sqlField("(COALESCE(" . $sqlAlias . ".stdlnm, '') || ', ' || COALESCE(" . $sqlAlias . ".stdfnm, ''))");
			}
			$this->setSQL();
		}

		/**
		 * Generates SQL for DropList
		 *
		 * @return void
		 */
		private function setSQL() {
			$field = null;
			if ($this->saveType == FFInputDropList::SAVE_ID) {
				$field = 'stdrefid,';
			}
			$this->dropListSQL("
				SELECT " . $field . " COALESCE(stdlnm, '') || ', ' || COALESCE(stdfnm, '')
				  FROM webset.dmg_studentmst AS std
				       INNER JOIN webset.sys_teacherstudentassignment ts ON ts.stdrefid = std.stdrefid
				 WHERE std.vndrefid = VNDREFID
				   AND std_deleted_sw = 'N'
				   " . ($this->activeOnly ? "AND stdstatus = 'A'" : "") . "
				 ORDER BY LOWER(stdlnm), LOWER(stdfnm)
			");
		}

		/**
		 * Only Active Students
		 *
		 * @var bool
		 */
		protected $activeOnly = false;

		/**
		 * Adds condition for showing only active students.
		 *
		 * @param bool $val
		 * @return FFStudentName
		 */
		public function activeOnly($val = true) {
			$this->activeOnly = (bool)$val;
			$this->setSQL();
			return $this;
		}

		/**
		 * Creates an instance of this class
		 *
		 * @static
		 * @param string $sqlAlias
		 * @param int $saveType
		 * @return FFIDEAStudentName
		 */
		public static function factory($sqlAlias = null, $saveType = null) {
			return new FFIDEAStudentName($sqlAlias, $saveType);
		}
	}

?>
