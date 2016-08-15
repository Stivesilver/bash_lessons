<?php

	/**
	 * PDF Form Template class
	 * This class provides data of form template stored in webset.statedef_forms
	 *
	 * @final
	 * @copyright Lumen Touch, 2012
	 */
	final class IDEAFormTemplatePDF extends RegularClass {

		const FORM_REPOSITORY = 'applications/webset/iep/evalforms/docs';

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
		 * PDF Template Path
		 *
		 * @var string
		 */
		private $template_path;

		/**
		 * PDF Template
		 *
		 * @var string
		 */
		private $template;

		/**
		 * Form Purpose ID
		 *
		 * @var int
		 */
		private $purpose_id;

		/**
		 * Form Purpose
		 *
		 * @var string
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
		 * Cross-ref Table to XML form fields
		 *
		 * @var string
		 */
		private $xml_field_links;

		/**
		 * XML Version Form ID 
		 *
		 * @var string
		 */
		private $xmlform_id;

		/**
		 * District Defaults
		 *
		 * @var string
		 */
		private $district_defaults;

		/**
		 * Initializes IDEAFormTemplatePDF object
		 *
		 * @param integer $id
		 */
		public function __construct($id) {
			if (!($id > 0)) throw new Exception('Setup Form ID number.');
			$this->id = $id;
			$SQL = "
				SELECT pdf.mfcdoctitle,
					   pdf.mfcfilename,
					   pdf.file_defaults,
					   pdf.mfcprefid,
					   pdf.xml_field_links,
					   pdf.xmlform_id,
					   pdf.lastuser,
					   pdf.lastupdate,
					   def.values,
					   purp.mfcpdesc
				  FROM webset.statedef_forms AS pdf
					   INNER JOIN webset.def_formpurpose AS purp ON pdf.mfcprefid = purp.mfcprefid
					   LEFT OUTER JOIN webset.disdef_defaults AS def ON form_id = pdf.mfcrefid AND vndrefid = VNDREFID AND area = 'PDF'
				 WHERE pdf.mfcrefid = " . $this->id . "
			";
			$form = $this->execSQL($SQL)->assoc();
			$this->title = $form['mfcdoctitle'];
			$this->template = file_get_contents(SystemCore::$physicalRoot . '/' . self::FORM_REPOSITORY . '/' . $form['mfcfilename']);
			$this->template_path = SystemCore::$physicalRoot . '/' . self::FORM_REPOSITORY . '/' . $form['mfcfilename'];
			$this->file_defaults = $form['file_defaults'];
			$this->xml_field_links = base64_decode($form['xml_field_links']);
			$this->xmlform_id = $form['xmlform_id'];
			$this->district_defaults = $form['values'];
			$this->purpose = $form['mfcpdesc'];
			$this->purpose_id = $form['mfcprefid'];
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
		 * Gets Form Template Path
		 *
		 * @return string
		 */
		public function getTemplatePath() {
			return $this->template_path;
		}

		/**
		 * Gets Cross-Ref XML form table links
		 *
		 * @return string
		 */
		public function getXMLCrossRefLinks() {
			return $this->xml_field_links;
		}

		/**
		 * Gets ID to XML Version form ID
		 *
		 * @return string
		 */
		public function getXMLFormID() {
			return $this->xmlform_id;
		}

		/**
		 * Gets ID to XML Version form adopted template
		 *
		 * @return string
		 */
		public function getXMLFormTemplate() {
			$form_xml = IDEAFormTemplateXML::factory($this->xmlform_id);
			return IDEAFormPDF::replace_id($form_xml->getTemplate(), $this->xml_field_links);
		}

		/**
		 * Gets File Url with defaults
		 *
		 * @return string
		 */
		public function getFileDefaults() {
			return $this->file_defaults;
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
		 * @return IDEAFormTemplatePDF
		 */
		public static function factory($id = 0) {
			return new IDEAFormTemplatePDF($id);
		}

	}

?>
