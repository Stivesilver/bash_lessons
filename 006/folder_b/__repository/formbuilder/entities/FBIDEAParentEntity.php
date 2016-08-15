<?php

	/**
	 * IDEA Parent Entity for the Form Builder
	 *
	 * @author Alex Kalevich
	 * @copyright Lumen Touch, 2015
	 */
	class FBIDEAParentEntity extends FBFieldEntity {

		/**
		 * Class Constructor
		 *
		 * @return FBIDEAParentEntity
		 */
		public function __construct() {
			parent::__construct('parent_entity', 'Parent Entity');
			$this->setDescription('This Field Entity created for Parent.');
			$this->addField(FBDataField::factory('parent_full_name', 'Parent Full Name'));
			$this->addField(FBDataField::factory('parent_fname', 'Parent First Name'));
			$this->addField(FBDataField::factory('parent_lname', 'Parent Last Name'));
			$this->addField(FBDataField::factory('both_parents', 'Parents Both'));
			$this->addField(FBDataField::factory('full_address', 'Full Address'));
			$this->addField(FBDataField::factory('description', 'Description'));
			$this->addField(FBDataField::factory('gdcitycode', 'City Code'));
			$this->addField(FBDataField::factory('gdcity', 'City'));
			$this->addField(FBDataField::factory('gdstate', 'State'));
			$this->addField(FBDataField::factory('gdwplace', 'Place'));
			$this->addField(FBDataField::factory('gdemail', 'Email'));
			$this->addField(FBDataField::factory('gdhphn', 'Home Phone'));
			$this->addField(FBDataField::factory('gdmphn', 'Mobile Phone'));
			$this->addField(FBDataField::factory('gdwphn', 'Work Phone'));
			$this->addField(FBDataField::factory('gdlang', 'Language'));

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
			$student = IDEAStudent::factory($stdrefid);
			$guardians = $student->getGuardians();
			if (isset($guardians[$seqNumber])) {
				return array(
					'parent_full_name' => $guardians[$seqNumber]['gdfnm'] . ' ' . $guardians[$seqNumber]['gdlnm'],
					'parent_fname' => $guardians[$seqNumber]['gdfnm'],
					'parent_lname' => $guardians[$seqNumber]['gdlnm'],
					'full_address' => $guardians[$seqNumber]['gdadr1'] . ', ' . $guardians[$seqNumber]['gdcity'] . ', ' . $guardians[$seqNumber]['gdstate'] . ' ' . $guardians[$seqNumber]['gdcitycode'],
					'description' => $guardians[$seqNumber]['gtdesc'],
					'gdcitycode' => $guardians[$seqNumber]['gdcitycode'],
					'gdcity' => $guardians[$seqNumber]['gdcity'],
					'gdstate' => $guardians[$seqNumber]['gdstate'],
					'gdwplace' => $guardians[$seqNumber]['gdwplace'],
					'gdemail' => $guardians[$seqNumber]['gdemail'],
					'gdhphn' => $guardians[$seqNumber]['gdhphn'],
					'gdmphn' => $guardians[$seqNumber]['gdmphn'],
					'gdwphn' => $guardians[$seqNumber]['gdwphn'],
					'gdlang' => $guardians[$seqNumber]['gdlang'],
					'both_parents' => (isset($guardians[0]) ? $guardians[0]['gdfnm'] . ' ' . $guardians[0]['gdlnm'] : '') . (isset($guardians[1]) ? ', ' . $guardians[1]['gdfnm'] . ' ' . $guardians[1]['gdlnm'] : '')
				);
			} else {
				return array(
					'parent_full_name' => '',
					'parent_fname' => '',
					'parent_lname' => '',
					'full_address' => '',
					'description' => '',
					'gdcitycode' => '',
					'gdcity' => '',
					'gdstate' => '',
					'gdwplace' => '',
					'gdemail' => '',
					'gdhphn' => '',
					'gdmphn' => '',
					'gdwphn' => '',
					'gdlang' => '',
					'both_parents' => ''
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
