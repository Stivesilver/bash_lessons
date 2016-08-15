<?php

	/**
	 * Class Valid Value Definition
	 * This class provides methods for working with the Valid Value Definition.
	 * The logic of this class depends and expands the following webset.glb_validvalues Tables:
	 * Using this class you can you can get basic Valid Value data:
	 *
	 * @author Nick Ignatushko
	 * @category Lumen Touch, 2013
	 */
	class IDEADefValidValue extends EntityRecordR {

		/**
		 * Valid Value ID
		 */
		const F_REFID = 'refid';

		/**
		 * Valid Value Area
		 */
		const F_AREA = 'valuename';

		/**
		 * Valid Value
		 */
		const F_VALUE = 'validvalue';

		/**
		 * Valid Value ID
		 */
		const F_VALUE_ID = 'validvalueid';

		/**
		 * Display Sequence
		 */
		const F_SEQUENCE = 'sequence_number';

		/**
		 * Valid Value ID
		 */
		const F_END_DATE = 'glb_enddate';

		/**
		 * Class Constructor
		 * The 1st argument is a reference to webset.glb_validvalues.refid
		 *
		 * @param int|EntityRecordData $refid
		 * @return IDEADefValidValue
		 */
		public function __construct($refid) {
			CoreUtils::checkArguments('[int|EntityRecordData]');
			parent::__construct($refid);
			$this->setProperty(self::F_REFID, 'int');
			$this->setProperty(self::F_AREA, 'string');
			$this->setProperty(self::F_VALUE, 'string');
			$this->setProperty(self::F_VALUE_ID, 'string');
			$this->setProperty(self::F_SEQUENCE, 'string');
			$this->setProperty(self::F_END_DATE, 'string');
		}

		/**
		 * Returns the instance of this class.
		 * The 1st argument is a reference to webset.glb_validvalues.refid
		 *
		 * @param int $refid
		 * @return IDEADefValidValue
		 */
		public static function factory($refid) {
			return new IDEADefValidValue($refid);
		}

		/**
		 * Creates and returns the instances of the IDEADefValidValue.
		 * The 1st argument is a list of IDs.
		 *
		 * @param array $ids
		 * @param DBConnection $db
		 * @return array.<IDEADefValidValue>
		 */
		public static function createInstances($ids, DBConnection $db = null) {
			return parent::createEntityInstances($ids, __CLASS__, 'refid', null, $db);
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
				  FROM webset.glb_validvalues
				 WHERE refid IN (" . implode(', ', $ids) . ")
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
	
