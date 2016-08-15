<?php

	/**
	 * IDEA General Entity for the Form Builder
	 *
	 * @author Alex Kalevich
	 * @copyright Lumen Touch, 2015
	 */
	class FBIDEAGeneralEntity extends FBFieldEntity {

		/**
		 * Class Constructor
		 *
		 * @return FBIDEAGeneralEntity
		 */
		public function __construct() {
			parent::__construct('general_entity', 'General Entity');
			$this->setDescription('General Field Entity.');
			$this->addField(FBDataField::factory('curr_date', 'Current Date'));
			$this->addField(FBDataField::factory('curr_user', 'Current User'));

		}

		/**
		 * Saves form data for the entity.
		 * Returns TRUE if all OK or string of the error message if something wrong.
		 *
		 * @param int $seqNumber Sequence number of entity on the form
		 * @param FBDataMap $data Own entity data
		 * @param FBDataMap $allData Data for all fields in the form
		 * @return bool|string
		 */
		public function save($seqNumber, FBDataMap $data, FBDataMap $allData) {
			return true;
		}

		/**
		 * Loads entity data and returns associative array with list of field_name/value pairs.
		 * Output array format:
		 *  [
		 *      'field_name' => 'value',
		 *      'field_name' => 'value',
		 *      'field_name' => 'value',
		 *      ...
		 *  ]
		 *
		 * @param int $seqNumber Sequence number of entity on the form
		 * @param FBDataMap $allData Data for all fields in the form
		 * @return array
		 */
		public function load($seqNumber, FBDataMap $allData) {
			return array(
				'curr_date' => date("m/d/Y"),
				'curr_user' => SystemCore::$userName
			);
		}

		/**
		 * Returns TRUE if the form data for this entity is correct.
		 * Returns string of the error message if something wrong.
		 *
		 * @param FBDataMap $data Own entity data
		 * @param FBDataMap $allData Data for all fields in the form
		 * @return bool|string
		 */
		public function validate(FBDataMap $data, FBDataMap $allData) {
			// for overriding
			return true;
		}
	}

?>
