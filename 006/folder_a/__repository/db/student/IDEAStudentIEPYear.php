<?php

	/**
	 * Class Student IEP Year.
	 * This class provides methods for working with the Student IEP Year.
	 * The logic of this class depends and expands the following webset.std_iep_year Tables:
	 * Using this class you can you can get basic IEP Year data:
	 *
	 * @author Nick Ignatusdhko
	 * @category Lumen Touch, 2013
	 */
	class IDEAStudentIEPYear extends EntityRecordR {

		/**
		 * Student Sp Ed Enrollment ID
		 * Reference to webset.sys_teacherstudentassignment.tsrefid
		 */
		const F_STDREFID = 'stdrefid';

		/**
		 * IEP Year ID
		 */
		const F_IEP_YEAR_ID = 'siymrefid';

		/**
		 * IEP Year Begin Date
		 */
		const F_BEG_DATE = 'siymiepbegdate';

		/**
		 * IEP Year End Date
		 */
		const F_END_DATE = 'siymiependdate';

		/**
		 * IEP Year Title
		 */
		const F_TITLE = 'ieptitle';

		/**
		 * Class Constructor
		 * The 1st argument is a reference to webset.std_iep_year.siymrefid
		 *
		 * @param int|varchar $siymrefid
		 * @return IDEAStudentIEPYear
		 */
		public function __construct($siymrefid) {
			CoreUtils::checkArguments('[int|string]');
			parent::__construct($siymrefid);
			$this->setProperty(self::F_STDREFID, 'int');
			$this->setProperty(self::F_IEP_YEAR_ID, 'int');
			$this->setProperty(self::F_BEG_DATE, 'string');
			$this->setProperty(self::F_END_DATE, 'string');
			$this->setProperty(self::F_TITLE, 'string');
		}

		/**
		 * Returns the instance of this class.
		 * The 1st argument is a reference to webset.std_iep_year.siymrefid
		 *
		 * @param int|varchar $siymrefid
		 * @return IDEAStudentIEPYear
		 */
		public static function factory($siymrefid) {
			return new IDEAStudentIEPYear($siymrefid);
		}

		/**
		 * Creates and returns the instances of the IDEAStudentIEPYear.
		 * The 1st argument is a list of IDs.
		 *
		 * @param array $ids
		 * @param DBConnection $db
		 * @return array.<IDEAStudentIEPYear>
		 */
		public static function createInstances($ids, DBConnection $db = null) {
			return parent::createEntityInstances($ids, __CLASS__, 'siymrefid', null, $db);
		}

		/**
		 * Returns SQL for loading record's data.
		 * The 1st argument is a list of IDs.
		 * The 2nd argument is a list of additional arguments (that are also present in child's class constructor).
		 *
		 * @param array $ids
		 * @return string
		 */
		protected function getDataSQL(array $ids, array $args = null) {
			return "
				SELECT *
				  FROM webset.std_iep_year
				 WHERE siymrefid IN (" . implode(', ', $ids) . ")
			";
		}

		/**
		 * Returns readable period.
		 * Period format: mm/dd/yyyy - mm/dd/yyyy
		 *
		 * @return string
		 */
		public function getIEPYearPeriod() {
			return CoreUtils::formatDate($this->data[self::F_BEG_DATE], 'm-d-Y') . ' - ' . CoreUtils::formatDate($this->data[self::F_END_DATE], 'm-d-Y');

		}

		/**
		 * Returns summarized info about the object.
		 * Implementation of interface IOTraceableInterface.
		 * This method helps to correct output the object using the class IOTrace (static alias is io::trace() )
		 *
		 * @return mixed
		 */
		public function trace() {
			$data = array();
			$data['Record\'s Properties'] = parent::trace();
			$data['IEP Year Period'] = $this->getIEPYearPeriod();

			return $data;
		}

	}
	
