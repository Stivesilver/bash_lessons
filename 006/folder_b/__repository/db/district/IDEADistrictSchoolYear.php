<?php

	/**
	 * Class Progress Extent.
	 * This class provides methods for working with the District Progress Extent.
	 * The logic of this class depends and expands the following webset.disdef_schoolyear Tables:
	 * Using this class you can you can get basic Progress Extent data:
	 *
	 * @author Nick Ignatushko
	 * @category Lumen Touch, 2013
	 */
	class IDEADistrictSchoolYear extends EntityRecordR {

		/**
		 * Disrtict ID
		 * Reference to public.sys_vndmst.vndrefid
		 */
		const F_VNDREFID = 'vndrefid';

		/**
		 * Progress Extent ID
		 */
		const F_REFID = 'dsyrefid';

		/**
		 * School Year Title
		 */
		const F_TITLE = 'dsydesc';

		/**
		 * School Year Start Date
		 */
		const F_BEGDATE = 'dsybgdt';

		/**
		 * School Year End Date
		 */
		const F_ENDDATE = 'dsyendt';

		/**
		 * Class Constructor
		 * The 1st argument is a reference to webset.disdef_schoolyear.dsyrefid
		 *
		 * @param int|EntityRecordData $dsyrefid
		 * @return IDEADistrictSchoolYear
		 */
		public function __construct($dsyrefid) {
			CoreUtils::checkArguments('[int|EntityRecordData]');
			parent::__construct($dsyrefid);
			$this->setProperty(self::F_VNDREFID, 'int');
			$this->setProperty(self::F_REFID, 'int');
			$this->setProperty(self::F_TITLE, 'string');
			$this->setProperty(self::F_BEGDATE, 'string');
			$this->setProperty(self::F_ENDDATE, 'string');
		}

		/**
		 * Returns the instance of this class.
		 * The 1st argument is a reference to webset.disdef_schoolyear.dsyrefid
		 *
		 * @param int|varchar $dsyrefid
		 * @return IDEADistrictSchoolYear
		 */
		public static function factory($dsyrefid) {
			return new IDEADistrictSchoolYear($dsyrefid);
		}

		/**
		 * Creates and returns the instances of the IDEADistrictSchoolYear.
		 * The 1st argument is a list of IDs.
		 *
		 * @param array $ids
		 * @param DBConnection $db
		 * @return array.<IDEADistrictSchoolYear>
		 */
		public static function createInstances($ids, DBConnection $db = null) {
			return parent::createEntityInstances($ids, __CLASS__, 'dsyrefid', null, $db);
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
				  FROM webset.disdef_schoolyear
				 WHERE dsyrefid IN (" . implode(', ', $ids) . ")
			";
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
			return $data;
		}

	}
	
