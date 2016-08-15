<?php

	/**
	 * IDEA Core Class
	 * This class provides basic IDEA methods
	 *
	 * @copyright Lumen Touch, 2012
	 */
	class IDEAFormDefaults extends RegularClass {

		/**
		 * Initializes $values property
		 *
		 * @param int $tsRefID
		 */
		public function __construct($tsRefID = 0) {
			parent::__construct();
			$this->init($tsRefID);
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param int $tsRefID
		 * @return IDEAFormDefaults
		 */
		public static function factory($tsRefID) {
			return new IDEAFormDefaults($tsRefID);
		}

		/**
		 * Default fields array
		 *
		 * @var array
		 */
		protected $values = array();

		/**
		 * Inits default data
		 *
		 * @param int $tsRefID
		 */
		private function init($tsRefID) {
			$student = IDEAStudent::factory($tsRefID);
			$guardians = $student->getGuardians();
			$disability = $student->getDisability();

			$this->values['DistrictName'] = SystemCore::$VndName;
			$this->values['DistrictNameAddress'] = SystemCore::$VndName;
			$this->values['DistrictSchool'] = SystemCore::$VndName . ', ' . $student->get('vouname');
			$this->values['tsrefid'] = $tsRefID;
			$this->values['stdrefid'] = $student->get('stdrefid');
			$this->values['StdName'] = $student->get('stdname');
			$this->values['StdName1'] = $student->get('stdname');
			$this->values['stdfnm'] = $student->get('stdfirstname');
			$this->values['StdDOB'] = $student->get('stddob');
			$this->values['iepmeetdt'] = $student->get('stdiepmeetingdt');
			$this->values['initdate'] = $student->get('stdenrolldt');
			$this->values['stdcmpltdt'] = $student->get('stdcmpltdt');
			$this->values['stdevaldt'] = $student->get('stdevaldt');
			$this->values['stdtriennialdt'] = $student->get('stdtriennialdt');
			$this->values['StdAge'] = $student->get('stdage');
			$this->values['StdSex'] = $student->get('stdsex');
			$this->values['Gender'] = $student->get('stdsex');
			$this->values['StdSchoolLoc'] = $student->get('vouname');
			$this->values['StdSchool'] = $student->get('vouname');
			$this->values['StdSchool'] = $student->get('vouname');
			$this->values['resschool'] = $student->get('vouname');
			$this->values['TeacherName'] = $student->get('cmname');
			$this->values['CaseManager'] = $student->get('cmname');
			$this->values['Grade'] = $student->get('grdlevel');
			$this->values['StdGrade'] = $student->get('grdlevel');
			$this->values['StdIepYear'] = $student->get('stdiepyeartitle');

			$this->values['stdResDis'] = SystemCore::$VndName;
			$this->values['stdResSch'] = $student->get('vouname');
			$this->values['stdAttDis'] = SystemCore::$VndName;
			$this->values['stdAttSch'] = $student->get('vouname');

			$this->values['CurrDate'] = date("m/d/Y");
			$this->values['CurrentDate'] = date("m/d/Y");
			$this->values['CurrUser'] = SystemCore::$userName;

			$this->values['fullAddress'] = trim($student->get('stdhadr1') . ', ' .
				$student->get('stdhcity') . ', ' .
				$student->get('stdhstate') . ' ' .
				$student->get('stdhzip'));
			$this->values['Address'] = $this->values['fullAddress'];
			$this->values['StdAddress'] = $student->get('stdhadr1');
			$this->values['HomePhone'] = $student->get('stdhphn');
			$this->values['StdPhone'] = $student->get('stdhphn');
			$this->values['CellPhone'] = $student->get('stdhphnmob');
			$this->values['State'] = $student->get('stdhstate');
			$this->values['City'] = $student->get('stdhcity');
			$this->values['Zip'] = $student->get('stdhzip');
			$this->values['StdID'] = $student->get('stdschid');
			$this->values['FedID'] = $student->get('stdfedidnmbr');
			$this->values['StdStateID'] = $student->get('stdstateidnmbr');
			$this->values['StdEthnic'] = $student->get('prim_lang');
			$this->values['StdEth'] = $student->get('ethdesc');
			$this->values['ZipCode'] = $student->get('stdhzip');
			$this->values['CityStateZip'] = $student->get('stdhcity') . ', ' .
				$student->get('stdhstate') . ' ' .
				$student->get('stdhzip');

			$both_parents = array();
			if (isset($guardians[0])) {
				$this->values['ParentName'] = $guardians[0]['gdfnm'] . ' ' . $guardians[0]['gdlnm'];
				$both_parents[] = $this->values['ParentName'];
				$this->values['StdParent'] = $this->values['ParentName'];
				$this->values['Parents'] = $this->values['ParentName'];

				$this->values['WorkPhone'] = $guardians[0]['gdwphn'];
				$this->values['guardAddr1'] = $guardians[0]['gdadr1'] . ', ' .
					$guardians[0]['gdcity'] . ', ' .
					$guardians[0]['gdstate'] . ' ' .
					$guardians[0]['gdcitycode'];

				$this->values['GrdName1'] = $guardians[0]['gdfnm'] . ' ' . $guardians[0]['gdlnm'];
				$this->values['GrdType1'] = $guardians[0]['gtdesc'];
				$this->values['GrdPhone1'] = $guardians[0]['gdhphn'];

				$this->values['GrdLang1'] = $guardians[0]['gdlang'];
				$this->values['GrdWorkPhone1'] = $guardians[0]['gdwphn'];
				$this->values['GrdCellPhone1'] = $guardians[0]['gdmphn'];
				$this->values['GrdEmail1'] = $guardians[0]['gdemail'];
				$this->values['GrdRoad1'] = $guardians[0]['gdadr1'];
				$this->values['GrdCityEtc1'] = $guardians[0]['gdcity'] . ', ' .
					$guardians[0]['gdstate'] . ' ' .
					$guardians[0]['gdcitycode'];
				$this->values['GrdAdr1'] = $this->values['guardAddr1'];
				$this->values['GrdAdr2'] = $this->values['guardAddr1'];
			}

			if (isset($guardians[1])) {
				$this->values['ParentName1'] = $guardians[1]['gdfnm'] . ' ' . $guardians[1]['gdlnm'];
				$both_parents[] = $this->values['ParentName1'];
				$this->values['guardAddr2'] = $guardians[1]['gdadr1'] . ', ' .
					$guardians[1]['gdcity'] . ', ' .
					$guardians[1]['gdstate'] . ' ' .
					$guardians[1]['gdcitycode'];

				$this->values['GrdName2'] = $guardians[1]['gdfnm'] . ' ' . $guardians[1]['gdlnm'];
				$this->values['GrdType2'] = $guardians[1]['gtdesc'];
				$this->values['GrdPhone2'] = $guardians[1]['gdhphn'];
				$this->values['GrdLang2'] = $guardians[1]['gdlang'];
				$this->values['GrdWorkPhone2'] = $guardians[1]['gdwphn'];
				$this->values['GrdCellPhone2'] = $guardians[0]['gdmphn'];
				$this->values['GrdEmail2'] = $guardians[1]['gdemail'];
				$this->values['GrdRoad2'] = $guardians[1]['gdadr1'];
				$this->values['GrdCityEtc2'] = $guardians[1]['gdcity'] . ', ' .
					$guardians[1]['gdstate'] . ' ' .
					$guardians[1]['gdcitycode'];
			}

			$this->values['ParentsBoth'] = implode(', ', $both_parents);
			$this->values['Disability'] = isset($disability[0]['disability']) ? $disability[0]['disability'] : '';
		}

		/**
		 * Add new default values
		 *
		 * @param array $values
		 * @return IDEAFormDefaults
		 */
		public function addValues(array $values) {
			foreach ($values as $key => $value) {
				$this->values[$key] = $value;
			}
			return $this;
		}

		/** Add new default values through include file
		 *
		 * @param string $path
		 * @param array $get_values
		 * @return $this
		 */
		public function addValuesByInclude($path, array $get_values) {
			$files = explode(",", $path);
			$defaults = array();
			foreach ($get_values as $key => $get_value) {
				$defaults[$key] = $get_value;
			}
			for ($i = 0; $i < count($files); $i++) {
				if ($files[$i] != '' && file_exists(SystemCore::$physicalRoot . '/' . $files[$i])) {
					$fvalues = '';
					include(SystemCore::$physicalRoot . '/' . $files[$i]);
					$this->addValues($this->xml2array($fvalues));
				}
			}
			return $this;
		}

		/**
		 * Convert xml values into array
		 *
		 * @param string $values
		 * @return array
		 */
		public static function xml2array($values, $key = 'name') {

			$arr = array();
			$xml = new SimpleXMLElement($values);

			foreach ($xml as $child) {
				$arr[(string)$child[$key]] = (string)$child;
			}
			return $arr;
		}

		/**
		 * Creates XML with default Student fields
		 *
		 * @return string
		 */
		public function getXML() {
			$xml = '<values>' . chr(10);
			foreach ($this->values as $key => $value) {
				if ($key != '' && $value != '') $xml .= '<value name="' . $key . '">' . $value . '</value>' . chr(10);
			}
			$xml .= '</values>' . chr(10);
			return $xml;
		}

		/**
		 * Returns values
		 *
		 * @return string
		 */
		/**
		 * @param string $key
		 * @return string|array
		 */
		public function getValues($key = null) {
			if ($key === null) {
				return $this->values;
			} else {
				if (array_key_exists($key, $this->values)) {
					return $this->values[$key];
				}
			}
			return '';
		}

		/**
		 * Creates FDF file with default Student fields
		 *
		 * @return string
		 */
		public function getFDF() {
			$fdf = '%FDF-1.2
                %����
                1 0 obj
                <<
                /FDF << /Fields 2 0 R /F ()>>
                >>
                endobj
                2 0 obj
                [' . chr(10);

			foreach ($this->values as $key => $value) {
				if ($key != '' && $value != '') $fdf .= '<< /T (' . $key . ')/V (' . $value . ')>>' . chr(10);
			}

			$fdf .= chr(10) . ']
                endobj
                trailer
                <<
                /Root 1 0 R

                >>
                %%EOF ';
			return $fdf;
		}



	}

?>
