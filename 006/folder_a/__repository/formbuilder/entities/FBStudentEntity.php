<?php

	/**
	 * IDEA Student Entity for the Form Builder
	 *
	 * @author Alex Kalevich
	 * @copyright Lumen Touch, 2015
	 */
	class FBStudentEntity extends FBFieldEntity {

		/**
		 * Class Constructor
		 *
		 * @return FBStudentEntity
		 */
		public function __construct() {
			parent::__construct('student_entity', 'Student Entity');
			$this->setDescription('This Field Entity created for Student.');
			$this->addField(FBDataField::factory('student_full_name', 'Student Full Name'));
			$this->addField(FBDataField::factory('student_fname', 'Student First Name'));
			$this->addField(FBDataField::factory('student_lname', 'Student Last Name'));
			$this->addField(FBDataField::factory('dob', 'Date of Birth'));
			$this->addField(FBDataField::factory('age', 'Age'));
			$this->addField(FBDataField::factory('pr_language', 'Primary Language'));
			$this->addField(FBDataField::factory('school_distr', 'Attending District '));
			$this->addField(FBDataField::factory('school_name', 'Attending School'));
			$this->addField(FBDataField::factory('res_school_distr', 'Resident District '));
			$this->addField(FBDataField::factory('res_school_name', 'Resident School'));
			$this->addField(FBDataField::factory('grade', 'Grade Level'));
			$this->addField(FBDataField::factory('gender', 'Gender'));
			$this->addField(FBDataField::factory('ethdesc', 'Ethnic'));
			$this->addField(FBDataField::factory('full_address', 'Full Address'));
			$this->addField(FBDataField::factory('street_address', 'Street Address'));
			$this->addField(FBDataField::factory('city_state_zip', 'City State Zip'));
			$this->addField(FBDataField::factory('city', 'City'));
			$this->addField(FBDataField::factory('state', 'State'));
			$this->addField(FBDataField::factory('zip', 'Zip Code'));
			$this->addField(FBDataField::factory('phone', 'Phone'));
			$this->addField(FBDataField::factory('stdid', 'StdID'));
			$this->addField(FBDataField::factory('fedid', 'FedID'));
			$this->addField(FBDataField::factory('stdstateidnmbr', 'StateID'));
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
			$address = $student->getAddress('W', true);
			return array(
				'student_full_name' => $student->getName('LF'),
				'student_fname' => $student->getName('F'),
				'student_lname' => $student->getName('L'),
				'dob' => $student->getDob(),
				'age' => $student->getAge(),
				'pr_language' => $student->getPrimaryLanguage(),
				'school_distr' => $student->getDistrict('A'),
				'school_name' => $student->getSchool('A'),
				'res_school_distr' => $student->getDistrict('R'),
				'res_school_name' => $student->getSchool('R'),
				'grade' => $student->getGrade(),
				'gender' => $student->getGender(),
				'ethdesc' => $student->getEthnicity(),
				'full_address' => $student->getAddress('W'),
				'street_address' => $address['street'],
				'city_state_zip' => $address['city'] . ', ' . $address['state'] . ' ' . $address['zip'],
				'city' => $address['city'],
				'state' => $address['state'],
				'zip' => $address['zip'],
				'phone' => $student->getPhone(),
				'stdid' => $student->getValue('stdschid'),
				'fedid' => $student->getValue('stdfedidnmbr'),
				'stdstateidnmbr' => $student->getValue('stdstateidnmbr')
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
