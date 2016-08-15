<?php

	/**
	 * IDEA Student Entity for the Form Builder
	 *
	 * @author Alex Kalevich
	 * @copyright Lumen Touch, 2015
	 */
	class FBIDEAStudentEntity extends FBFieldEntity {

		/**
		 * Class Constructor
		 *
		 * @return FBIDEAStudentEntity
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
			$this->addField(FBDataField::factory('teacher', 'Teacher'));
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
			$this->addField(FBDataField::factory('cur_iep_year', 'Current Iep Year'));
			$this->addField(FBDataField::factory('disability', 'Disability'));
			$this->addField(FBDataField::factory('initdate', 'IEP Initiation Date'));
			$this->addField(FBDataField::factory('iepmeetdt', 'IEP Meeting Date'));
			$this->addField(FBDataField::factory('cur_evaluation', 'Current Evaluation Date'));
			$this->addField(FBDataField::factory('stdenterdt', 'Date Entered Sp Ed Program'));
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
			$disability = $student->getDisability();
			return array(
				'student_full_name' => $student->get('stdfirstname') . ' ' . $student->get('stdlastname'),
				'student_fname' => $student->get('stdfirstname'),
				'student_lname' => $student->get('stdlastname'),
				'dob' => $student->get('stddob'),
				'age' => $student->get('stdage'),
				'pr_language' => $student->get('prim_lang'),
				'school_distr' => SystemCore::$VndName,
				'school_name' => $student->get('vouname'),
				'res_school_distr' => $student->get('vndname_res'),
				'res_school_name' => $student->get('vouname_res'),
				'grade' => $student->get('grdlevel'),
				'gender' => $student->get('stdsex'),
				'teacher' => $student->get('cmname'),
				'full_address' => $student->get('stdaddress'),
				'street_address' => $student->get('stdhadr1'),
				'city_state_zip' => $student->get('stdhcity') . ', ' .$student->get('stdhstate') . ' ' . $student->get('stdhzip'),
				'city' => $student->get('stdhcity'),
				'state' => $student->get('stdhstate'),
				'zip' => $student->get('stdhzip'),
				'phone' => $student->get('stdhphn'),
				'stdid' => $student->get('stdschid'),
				'fedid' => $student->get('stdfedidnmbr'),
				'cur_iep_year' => $student->get('stdiepyeartitle'),
				'disability' => isset($disability[0]['disability']) ? $disability[0]['disability'] : '',
				'initdate' => $student->get('stdenrolldt'),
				'iepmeetdt' => $student->get('stdiepmeetingdt'),
				'cur_evaluation' => $student->get('stdevaldt'),
				'stdenterdt' => $student->get('stdenterdt'),
				'stdstateidnmbr' => $student->get('stdstateidnmbr'),
				'ethdesc' => $student->get('ethdesc')
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
