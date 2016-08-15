<?php

	/**
	 * Contains basic student Demo and Sp Ed data
	 *
	 * @copyright Lumen Touch, 2012
	 */
	class IDEAStudentFormXML extends RegularClass {

		/**
		 * Form ID
		 * DB Table: webset.std_forms_xml.sfrefid
		 *
		 * @var int
		 */
		private $id = null;

		/**
		 * Form Student ID
		 * DB Table: webset.std_forms_xml.stdrefid
		 *
		 * @var int
		 */
		private $stdrefid;

		/**
		 * Form IEP Year ID
		 * DB Table: webset.std_forms_xml.iepyear
		 *
		 * @var int
		 */
		private $iepyear = null;

		/**
		 * State ID
		 * DB Table: webset.std_forms_xml.frefid
		 *
		 * @var int
		 */
		private $frefid;

		/**
		 * Values
		 * DB Table: webset.std_forms_xml.values_content
		 *
		 * @var int
		 */
		private $values;

		/**
		 * Archived or not
		 * DB Table: webset.dmg_studentmst.archived
		 *
		 * @var bool
		 */
		protected $archived;

		/**
		 * Last Update
		 * DB Table: webset.dmg_studentmst.lastupdate
		 *
		 * @var string
		 */
		protected $lastupdate;

		/**
		 * Last User
		 * DB Table: webset.dmg_studentmst.lastuser
		 *
		 * @var string
		 */
		protected $lastuser;

		/**
		 * Initializes basic properties
		 *
		 * @param int $tsRefID
		 */
		public function __construct($id = null) {
			if ($id != null) {
				$this->id = $id;
				$this->updateProperties();
			}
		}

		private function updateProperties() {
			$SQL = "
					SELECT stdrefid,
						   iepyear,
						   frefid,
						   values_content,
						   archived,
						   lastupdate,
						   lastuser
					  FROM webset.std_forms_xml
					 WHERE sfrefid = " . $this->id . "
				";
			$form = $this->execSQL($SQL)->assoc();

			$this->stdrefid = $form['stdrefid'];
			$this->iepyear = $form['iepyear'];
			$this->frefid = $form['frefid'];
			$this->values = base64_decode($form['values_content']);
			$this->archived = $form['archived'] == 'Y' ? true : false;
			$this->lastupdate = $form['lastupdate'];
			$this->lastuser = $form['lastuser'];
		}

		/**
		 * Gets Form ID
		 *
		 * @return string
		 */
		public function getFormId() {
			return $this->id;
		}

		/**
		 * Gets Student ID
		 *
		 * @return string
		 */
		public function getStdrefid() {
			return $this->stdrefid;
		}

		/**
		 * Gets IEP Year ID
		 *
		 * @return string
		 */
		public function getIepYear() {
			return $this->iepyear;
		}

		/**
		 * Gets State Form ID
		 *
		 * @return string
		 */
		public function getStateFormId() {
			return $this->frefid;
		}

		/**
		 * Gets Values
		 *
		 * @return string
		 */
		public function getValues() {
			return $this->values;
		}

		/**
		 * Gets Values
		 *
		 * @return string
		 */
		public function getArchived() {
			return $this->archived;
		}

		/**
		 * Gets Last Update
		 *
		 * @return string
		 */
		public function getLastUpdate() {
			return $this->lastupdate;
		}

		/**
		 * Gets Last User
		 *
		 * @return string
		 */
		public function getLastUser() {
			return $this->lastuser;
		}

		/**
		 * Sets Student ID
		 *
		 * @param int $val
		 * @return IDEAStudentFormXML
		 */
		public function setStdrefid($val) {
			$this->stdrefid = $val;
			return $this;
		}

		/**
		 * Sets IEP Year ID
		 *
		 * @param int $val
		 * @return IDEAStudentFormXML
		 */
		public function setIepYear($val) {
			$this->iepyear = $val;
			return $this;
		}

		/**
		 * Sets State Form ID
		 *
		 * @param int $val
		 * @return IDEAStudentFormXML
		 */
		public function setStateFormId($val) {
			$this->frefid = $val;
			return $this;
		}

		/**
		 * Sets Values
		 *
		 * @param string $val
		 * @return IDEAStudentFormXML
		 */
		public function setValues($val) {
			$this->values = $val;
			return $this;
		}

		/**
		 * Sets Values
		 *
		 * @param bool $val
		 * @return IDEAStudentFormXML
		 */
		public function setArchived($val) {
			$this->archived = $val == true ? 'Y' : 'N';
			return $this;
		}

		/**
		 * Saves Form
		 *
		 * @return IDEAStudentFormXML
		 */
		public function saveForm() {
			$this->id = DBImportRecord::factory('webset.std_forms_xml', 'sfrefid', $this->db)
				->key('sfrefid', (int)$this->id)
				->set('stdrefid', $this->stdrefid)
				->set('iepyear', $this->iepyear)
				->set('frefid', $this->frefid)
				->set('values_content', base64_encode($this->values))
				->set('lastuser', db::escape(SystemCore::$userUID))
				->set('lastupdate', 'NOW()', true)
				->import()
				->recordID();
			$this->updateProperties();
			return $this;
		}

		/**
		 * Delete Form
		 *
		 * @return IDEAStudentFormXML
		 */
		public function deleteForm() {
			DBImportRecord::factory('webset.std_forms_xml', 'sfrefid', $this->db)
				->key('sfrefid', (int)$this->id)
				->set('stdrefid', 'NULL', true)
				->set('lastuser', db::escape(SystemCore::$userUID))
				->set('lastupdate', 'NOW()', true)
				->import();
			return $this;
		}

		/**
		 * Restore Form
		 *
		 * @return IDEAStudentFormXML
		 */
		public function restoreForm() {
			DBImportRecord::factory('webset.std_forms_xml', 'sfrefid', $this->db)
				->key('sfrefid', (int)$this->id)
				->set('stdrefid', '(SELECT stdrefid FROM webset.std_iep_year WHERE webset.std_forms_xml.iepyear = siymrefid)', true)
				->set('lastuser', db::escape(SystemCore::$userUID))
				->set('lastupdate', 'NOW()', true)
				->import();
			return $this;
		}

		/**
		 * Searches needed form among saved
		 *
		 * @return IDEAStudentFormXML
		 */
		public function searchForm() {
			$SQL = "
				SELECT sfrefid
				  FROM webset.std_forms_xml
				 WHERE stdrefid = " . $this->stdrefid . "
				   AND frefid = " . $this->frefid . "
				   AND iepyear = " . ($this->iepyear == null ? 'iepyear' : $this->iepyear) . "
				 ORDER BY sfrefid DESC
			";
			$id = $this->execSQL($SQL)->getOne();

			if ($id > 0) {
				$this->id = $id;
				$this->updateProperties();
			}
			return $this;
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param int $id
		 * @return IDEAStudentFormXML
		 */
		public static function factory($id = null) {
			return new IDEAStudentFormXML($id);
		}

	}

?>
