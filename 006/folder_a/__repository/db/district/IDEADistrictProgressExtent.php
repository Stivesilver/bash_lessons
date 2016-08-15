<?php

	/**
	 * Class Progress Extent.
	 * This class provides methods for working with the District Progress Extent.
	 * The logic of this class depends and expands the following webset.disdef_progressrepext Tables:
	 * Using this class you can you can get basic Progress Extent data:
	 *
	 * @author Nick Ignatusdhko
	 * @category Lumen Touch, 2013
	 */
	class IDEADistrictProgressExtent extends EntityRecordR {

		/**
		 * Disrtict ID
		 * Reference to public.sys_vndmst.vndrefid
		 */
		const F_VNDREFID = 'vndrefid';

		/**
		 * Progress Extent ID
		 */
		const F_REFID = 'eprefid';

		/**
		 * Progress Extent Code
		 */
		const F_CODE = 'epsdesc';

		/**
		 * Progress Extent Description
		 */
		const F_DESCRIPTION = 'epldesc';

		/**
		 * Progress Extent Title
		 */
		const F_SEQUENCE = 'epseq';

		/**
		 * Class Constructor
		 * The 1st argument is a reference to webset.disdef_progressrepext.eprefid
		 *
		 * @param int|EntityRecordData $eprefid
		 * @return IDEADistrictProgressExtent
		 */
		public function __construct($eprefid) {
			CoreUtils::checkArguments('[int|EntityRecordData]');
			parent::__construct($eprefid);
			$this->setProperty(self::F_VNDREFID, 'int');
			$this->setProperty(self::F_REFID, 'int');
			$this->setProperty(self::F_CODE, 'string');
			$this->setProperty(self::F_DESCRIPTION, 'string');
			$this->setProperty(self::F_SEQUENCE, 'int');
		}

		/**
		 * Returns the instance of this class.
		 * The 1st argument is a reference to webset.disdef_progressrepext.eprefid
		 *
		 * @param int|varchar $eprefid
		 * @return IDEADistrictProgressExtent
		 */
		public static function factory($eprefid) {
			return new IDEADistrictProgressExtent($eprefid);
		}

		/**
		 * Creates and returns the instances of the IDEADistrictProgressExtent.
		 * The 1st argument is a list of IDs.
		 *
		 * @param array $ids
		 * @param DBConnection $db
		 * @return array.<IDEADistrictProgressExtent>
		 */
		public static function createInstances($ids, DBConnection $db = null) {
			return parent::createEntityInstances($ids, __CLASS__, 'eprefid', null, $db);
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
				  FROM webset.disdef_progressrepext
				 WHERE eprefid IN (" . implode(', ', $ids) . ")
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
	
