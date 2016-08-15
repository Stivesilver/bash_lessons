<?php

	/**
	 * IDEA Parent Entity for the Form Builder
	 *
	 * @author Alex Kalevich
	 * @copyright Lumen Touch, 2015
	 */
	class FBParentEntity extends FBFieldEntity {

		/**
		 * Class Constructor
		 *
		 * @return FBParentEntity
		 */
		public function __construct() {
			parent::__construct('parent_entity', 'Parent Entity');
			$this->setDescription('This Field Entity created for Parent.');
			$this->addField(FBDataField::factory('parent_full_name', 'Parent Full Name'));
			$this->addField(FBDataField::factory('parent_fname', 'Parent First Name'));
			$this->addField(FBDataField::factory('parent_lname', 'Parent Last Name'));
			$this->addField(FBDataField::factory('full_address', 'Full Address'));
			$this->addField(FBDataField::factory('description', 'Description'));
			$this->addField(FBDataField::factory('gdemail', 'Email'));
			$this->addField(FBDataField::factory('gdhphn', 'Home Phone'));
			$this->addField(FBDataField::factory('gdmphn', 'Mobile Phone'));

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
			$stdrefid = $allData->getValue('stdrefid');
			$student = Student::factory($stdrefid);
			$guardians = $student->getGuardians();

			if (isset($guardians[$seqNumber])) {
				return array(
					'parent_full_name' => $guardians[$seqNumber]->getName('LF'),
					'parent_fname' => $guardians[$seqNumber]->getName('F'),
					'parent_lname' => $guardians[$seqNumber]->getName('L'),
					'full_address' => $guardians[$seqNumber]->getAddress(),
					'description' => $guardians[$seqNumber]->getRelationType(),
					'gdemail' => $guardians[$seqNumber]->getEmail(),
					'gdhphn' => $guardians[$seqNumber]->getPhone('W'),
					'gdmphn' => $guardians[$seqNumber]->getEmail('M')
				);
			} else {
				return array(
					'parent_full_name' => '',
					'parent_fname' => '',
					'parent_lname' => '',
					'full_address' => '',
					'description' => '',
					'gdemail' => '',
					'gdhphn' => '',
					'gdmphn' => ''
				);
			}
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
