<?php

	/**
	 * XML Form Template class
	 * This class provides data of form template stored in webset.statedef_forms_xml
	 *
	 * @final
	 * @copyright Lumen Touch, 2012
	 */
	final class IDEAFormTemplateXML extends RegularClass {

		/**
		 * Form ID
		 *
		 * @var string
		 */
		private $id;

		/**
		 * Form Title
		 *
		 * @var string
		 */
		private $title;

		/**
		 * XML Template
		 *
		 * @var bool
		 */
		private $template;

		/**
		 * Form Purpose ID
		 *
		 * @var bool
		 */
		private $purpose_id;

		/**
		 * Form Purpose
		 *
		 * @var bool
		 */
		private $purpose;

		/**
		 * Last User
		 *
		 * @var string
		 */
		private $lastuser;

		/**
		 * Last Update
		 *
		 * @var string
		 */
		private $lastupdate;

		/**
		 * Defaults File url
		 *
		 * @var string
		 * @depricate
		 */
		private $file_defaults;

		/**
		 * District Defaults
		 *
		 * @var string
		 */
		private $district_defaults;

		/**
		 * Class Defaults
		 *
		 * @var string
		 */
		private $class_defaults;

		/**
		 * Initializes IDEAFormTemplateXML object
		 *
		 * @param integer $id
		 */
		public function __construct($id) {
			if (!($id > 0)) throw new Exception('Setup Form ID number.');
			$this->id = $id;
			$SQL = "
				SELECT form_name,
					   form_xml,
					   file_defaults,
					   class_defaults,
					   values,
					   form_purpose,
					   mfcpdesc,
					   xml.lastuser,
					   xml.lastupdate
				  FROM webset.statedef_forms_xml xml
					   INNER JOIN webset.def_formpurpose purp ON form_purpose = purp.mfcprefid
					   LEFT OUTER JOIN webset.disdef_defaults ON form_id = frefid AND vndrefid = VNDREFID AND area = 'XML'
				 WHERE frefid = " . $this->id . "
			";
			$form = $this->execSQL($SQL)->assoc();
			$this->title = $form['form_name'];
			$this->template = base64_decode($form['form_xml']);
			$this->class_defaults = $form['class_defaults'];
			$this->file_defaults = $form['file_defaults'];
			$this->district_defaults = $form['values'];
			$this->purpose = $form['mfcpdesc'];
			$this->purpose_id = $form['form_purpose'];
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
		 * Gets Form Title
		 *
		 * @return string
		 */
		public function getTitle() {
			return $this->title;
		}

		/**
		 * Gets Form Template
		 *
		 * @return string
		 */
		public function getTemplate() {
			return $this->template;
		}

		/**
		 * Gets File Url with defaults
		 *
		 * @return string
		 * @depricate
		 */
		public function getFileDefaults() {
			return $this->file_defaults;
		}

		/**
		 * Gets class defaults
		 *
		 * @return string
		 * @depricate
		 */
		public function getClassDefaults() {
			return $this->class_defaults;
		}

		/**
		 * Gets District Defaults
		 *
		 * @return string
		 */
		public function getDistrictDefaults() {
			return $this->district_defaults;
		}

		/**
		 * Gets Form Purpose
		 *
		 * @return string
		 */
		public function getPurpose() {
			return $this->purpose;
		}

		/**
		 * Gets Form Purpose ID
		 *
		 * @return string
		 */
		public function getPurposeId() {
			return $this->purpose_id;
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
		 * Creates an instance of this class
		 *
		 * @param integer $id
		 * @return IDEAFormTemplateXML
		 */
		public static function factory($id = 0) {
			return new IDEAFormTemplateXML($id);
		}

	}

?>
