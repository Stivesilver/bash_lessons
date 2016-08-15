<?php

	/**
	 * Class IDEAStudentChecker
	 * Call methods return associative array with following keys:
	 * condition - hide item (Y/N)
	 * link - change item link (string)
	 * item - rename item (string)
	 * disable - disabled item and add description (string(description))
	 *
	 * @author Alex Kalevich
	 * @copyright LumenTouch, 2014
	 */
	class IDEAStudentChecker extends RegularClass {

		/**
		 * Student Id
		 *
		 * @var $tsRefID
		 */
		private $tsRefID;

		/**
		 * Year student
		 *
		 * @var int
		 */
		protected $stdIEPYear;

		/**
		 * Class Constructor
		 *
		 * @param int $tsRefID
		 * @param int $stdIEPYear
		 * @return IDEAStudentChecker
		 */
		public function __construct($tsRefID = null, $stdIEPYear = null) {
			parent::__construct();
			$this->tsRefID = $tsRefID;
			$this->stdIEPYear = $stdIEPYear;
		}

		/**
		 * Creates and returns an instance of this class.
		 *
		 * @param int $tsRefID
		 * @param int $stdIEPYear
		 * @return IDEAStudentChecker
		 */
		public static function factory($tsRefID = null, $stdIEPYear = null) {
			return new IDEAStudentChecker($tsRefID, $stdIEPYear);
		}

		/**
		 * Show/Hide Menu by District Parameter
		 *
		 * @param $dis
		 * @param $default
		 * @return array
		 */
		public function showByDistrictParameter($dis, $default) {
			if (IDEACore::disParam($dis) == "N" || IDEACore::disParam($dis) == "Y") {
				return array('condition' => IDEACore::disParam($dis));
			} else {
				return array('condition' => $default);
			}
		}

		/**
		 * Show/Hide Menu by District Parameter for Admins Only
		 *
		 * @param $dis
		 * @param $default
		 * @return array
		 */
		public function showByDistrictParameterAdminOnly($dis, $default) {
			if (SystemCore::$AccessType == "1" and (IDEACore::disParam($dis) == 'N' || IDEACore::disParam($dis) == 'Y')) {
				return array('condition' => IDEACore::disParam($dis));
			} else {
				return array('condition' => 'N');
			}
		}

		/**
		 * Hide Menu
		 *
		 * @return array
		 */
		public function hide() {
			return array('condition' => 'N');
		}

		/**
		 * Replace Link by District Parameter
		 *
		 * @param int $dis
		 * @param string $default
		 * @param string $url
		 * @param string $item
		 * @return array|null
		 */
		public function linkByDistrictParameter($dis, $default, $url = null, $item = null) {
			if (IDEACore::disParam($dis) == "Y" || IDEACore::disParam($dis) != "N" && $default == 'Y') {
				if ($item) {
					return array('link' => $url, 'item' => $item);
				} else {
					return array('link' => $url);
				}
			} else return null;

		}

		/**
		 * Replace Link by Registry
		 *
		 * @param $regapp
		 * @param $regkey
		 * @param $regname
		 * @param null $url
		 * @param null $item
		 * @return array|null
		 */
		public function linkByRegistry($regapp, $regkey, $regname, $url  = null, $item = null) {
			if ($this->registry->getOne($regapp, $regkey, $regname) == 'Yes') {
				if ($item) {
					return array('link' => $url, 'item' => $item);
				} else {
					return array('link' => $url);
				}
			} else return null;
		}


		/**
		 * Replace Name by Student EC Status
		 *
		 * @return array|null
		 */
		public function nameByStudentECStatus() {
			//EC STATUS
			$SQL = "SELECT stdearlychildhoodsw
  		      FROM webset.sys_teacherstudentassignment
			 WHERE tsrefid = " . $this->tsRefID . "";
			$ecresult = $this->execSQL($SQL);

			$ecstatus = $ecresult->fields[0];

			if ($ecstatus == "Y") {
				return array('item' => "Educational Environment");
			} else return null;
		}

		/**
		 * Add Archive Status to Builder
		 *
		 * @return array
		 */
		public function nameByStudentIEPArchiveStatus() {
			$archived = false;
			$student = new IDEAStudent($this->tsRefID);

			$date_start = $student->getDate('stdiepyearbgdt');
			$date_end = $student->getDate('stdiepyearendt');

			if ($student->getDate('stdiepmeetingdt')) $date_start = $student->getDate('stdiepmeetingdt');
			if ($student->getDate('stdcmpltdt')) $date_end = $student->getDate('stdcmpltdt');

			if ($date_start == '') return;

			$year = substr($date_start, 0, 4);
			$month = substr($date_start, 5, 2);
			$day = substr($date_start, 8, 2);
			$startd = mktime(0, 0, 0, $month, $day, $year);
			$year = substr($date_end, 0, 4);
			$month = substr($date_end, 5, 2);
			$day = substr($date_end, 8, 2);
			$endd = mktime(0, 0, 0, $month, $day, $year);
			$where = "AND siepmdocdate >= '$date_start'";
			$years = substr($date_start, 0, 4) . "-" . substr($date_end, 0, 4) . " ";

			$SQL = "
		        SELECT stdiepmeetingdt
			      FROM webset.std_iep
			     WHERE stdrefid = " . $this->tsRefID . "
			       AND COALESCE(iep_status, 'A') != 'I'
		    ";
			$result = $this->execSQL($SQL);

			while (!$result->EOF) {
				$from_iep = $result->fields[0];
				$year = substr($from_iep, 0, 4);
				$month = substr($from_iep, 5, 2);
				$day = substr($from_iep, 8, 2);
				$iepdate = mktime(0, 0, 0, $month, $day, $year);

				if ($startd <= $iepdate and $iepdate <= $endd) {
					$archived = true;
					break;
				}
				$result->MoveNext();
			}

			if (!$archived) {
				$SQL = "
            SELECT 1
	          FROM webset.std_iep
	         WHERE stdrefid = " . $this->tsRefID . "
	           AND COALESCE(iep_status, 'A') != 'I'
	         " . $where . "
        ";
				$result = db::execSQL($SQL);
				if ($result->fields[0] == 1) {
					$archived = true;
				}
			}

			if ($archived) {
				return array('item' => 'IEP Builder (' . $years . 'IEP archived)');
			} else {
				return array('item' => 'IEP Builder (' . $years . 'IEP not archived)');
			}
		}

		/**
		 * Disable Menu by Student Age
		 *
		 * @return array|null
		 */
		public function activeByStudentAge() {
			$description = "Student is not yet 14 years";

			$SQL = "
				SELECT t1.stdtransitioneligibilitysw
		          FROM webset.sys_teacherstudentassignment AS t1
	             WHERE t1.tsrefid = " . $this->tsRefID;

			$result = $this->execSQL($SQL);
			if ($result->fields[0] == 'Y') {
				return array('disable' => $description);
			} else return null;

		}

		/**
		 * Disable Menu by Student Manifest
		 *
		 * @return array|null
		 */
		public function activeByStudentManifest() {
			$description = "Manifestation Determination in not Purpose of the Conference";

			$SQL = "
				SELECT sicprefid
	              FROM webset.std_in_iepconfpurpose AS t0
                       INNER JOIN webset.statedef_iepconfpurpose AS t1 ON t1.siepcprefid = t0.siepcprefid
	             WHERE siepcpdesc LIKE '%Manifestation Determination%'
	               AND stdrefid = " . $this->tsRefID;

			$result = $this->execSQL($SQL);
			if ($result->fields[0] > 0) {
				return array('disable' => $description);
			} else return null;
		}

		/**
		 * Disable Menu
		 *
		 * @param stirng $description
		 * @return array
		 */
		public function deactivate($description) {
			return array('disable' => $description);
		}

		/**
		 * Disable Menu by Student Behavior
		 *
		 * @return array|null
		 */
		public function activeByStudentBehavior() {
			$description = "Functional Behavioral Assessment and Plan are not necessary";

			$SQL = "
				SELECT snaprefid
                  FROM webset.std_nfb_assess_plan
                 WHERE naprefid IN (1,2)
                   AND stdrefid = " . $this->tsRefID;

			$result = $this->execSQL($SQL);
			if ($result->fields[0] > 0) {
				return array('disable' => $description);
			} else return null;
		}

		/**
		 * Disable Menu by Student ransition Services
		 *
		 * @param string $name
		 * @return array|null
		 */
		public function activeByStudentTransitionServices($name) {
			$description = "Transition services not required.";

			$SQL = "
				SELECT 1
	          	  FROM webset.statedef_spconsid_quest  quest
	                   LEFT OUTER JOIN webset.std_spconsid std ON std.scqrefid = quest.scmrefid
                        AND std.stdrefid = " . $this->tsRefID . "
                        AND std.syrefid = " . $this->stdIEPYear . "
	                   LEFT OUTER JOIN webset.statedef_spconsid_answ ans ON ans.scarefid = std.scarefid
	        	  WHERE screfid = 17
	          		AND ans.scarefid=380
	         		AND std.scarefid=380
	          ";

			$result = $this->execSQL($SQL);
			if ($result->fields[0] > 0) {
				return array('disable' => $description, 'item' => $name . ' (Not Required)');
			} else return null;
		}

		/**
		 * Hides Blocks in Builder
		 *
		 * @param string $text
		 * @return array
		 */
		public function showByStudentForm($text) {
			$SQL = "
		        SELECT scanswer
			      FROM webset.std_spconsid std
			           INNER JOIN webset.statedef_spconsid_answ ans ON ans.scarefid = std.scarefid
		               INNER JOIN webset.statedef_spconsid_quest qws ON std.scqrefid = qws.scmrefid
			     WHERE std.stdrefid = " . $this->tsRefID . "
		           AND std.syrefid = " . $this->stdIEPYear . "
			       AND LOWER(scmquestion) LIKE '%$text%'
            ";
			if ($text == 'extended school year') {
				if ($this->execSQL($SQL)->getOne() == 'Yes') {
					return array('condition' => 'Y');
				}

				if (IDEACore::disParam(53) == "Y") {
					$SQL = "
						SELECT sesymesydecisionsw
		                  FROM webset.std_esy_mst std
		                 WHERE std.stdrefid = " . $this->tsRefID . "
		                   AND std.iepyear = " . $this->stdIEPYear . "
			        ";

					if ($this->execSQL($SQL)->getOne() == 'Y') {
						return array('condition' => 'Y');
					}
				}
				return array('condition' => 'N');
			} else {
				if ($this->execSQL($SQL)->getOne() == 'Yes') {
					return array('condition' => 'Y');
				} else {
					return array('condition' => 'N');
				}
			}
		}

		/**
		 * Disable Menu by Early Childhood
		 *
		 * @return array|null
		 */
		public function activeByEarlyChildhood() {
			if (IDEACore::disParam(141) == 'Y') {
				$description = "Only for K-12 students";

				$SQL = "
				SELECT stdrefid
		          FROM webset.sys_teacherstudentassignment AS t0
		         WHERE stdearlychildhoodsw = 'Y'
		           AND tsrefid = " . $this->tsRefID;

				$result = $this->execSQL($SQL);
				if ($result->fields[0] > 0) {
					return array('disable' => $description);
				} else return null;

			}
		}
	}
