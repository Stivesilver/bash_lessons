<?php

	/**
	 * Class
	 *
	 * @author Alex Kalevich
	 * @copyright LumenTouch, 2014
	 */
	class IDEAFormChecker extends FFButton {

		/**
		 * Table name
		 * Example: webset.std_forms
		 *
		 * @var string
		 */
		private $table;

		/**
		 * Key field of table
		 * Example: smfcrefid
		 *
		 * @var string
		 */
		private $key_field;

		/**
		 * Javascript statement which will set GET 'refids' parameter for Export Popup windows
		 * Example: ListClass.get().getSelectedValues().values.join(',')
		 * Example: '1,2,3'
		 *
		 * @var string
		 */
		private $refids = null;

		/**
		 * Name Field name
		 * Example: xml_name
		 *
		 * @var string
		 */
		private $name_field;

		/**
		 * XML Field name
		 * Example: xml_forms
		 *
		 * @var string
		 */
		private $xml_field;

		/**
		 * XML Field Encoded base64
		 * true/false
		 *
		 * @var bool
		 */
		private $xml_field_encoded = false;

		/**
		 * XML
		 *
		 * @var string
		 */
		private $xml;

		/**
		 * Class Constructor
		 *
		 * @param $caption
		 * @return IDEAFormChecker
		 */
		public function __construct($caption) {
			# call parent constructor
			parent::__construct();
			$this->value($caption);
		}

		/**
		 * Sets table property
		 *
		 * @param string $table
		 * @throws Exception
		 * @return IDEAFormChecker
		 */
		public function setTable($table = '') {
			if ($table == '') throw new Exception('Please specify table name.');
			$this->table = $table;
			return $this;
		}

		/**
		 * Sets key_field property
		 *
		 * @param string $key_field
		 * @throws Exception
		 * @return IDEAFormChecker
		 */
		public function setKeyField($key_field = '') {
			if ($key_field == '') throw new Exception('Please specify key field name.');
			$this->key_field = $key_field;
			return $this;
		}

		/**
		 * Sets refids property
		 *
		 * @param string $refids
		 * @throws Exception
		 * @return IDEAFormChecker
		 */
		public function setRefids($refids = null) {
			if ($refids === null) throw new Exception('Please specify Refids javascript statement.');
			$this->refids = json_encode($refids);
			return $this;
		}

		/**
		 * Sets form refids property
		 *
		 * @param string $refids
		 * @throws Exception
		 * @return IDEAFormChecker
		 */
		public function setFormRefids($refids = null) {
			if ($refids === null) throw new Exception('Please specify Refids javascript statement.');
			$this->refids = $refids;
			return $this;
		}

		/**
		 * Sets form name field
		 *
		 * @param null $name_field
		 * @throws Exception
		 * @return IDEAFormChecker
		 */
		public function setNameField($name_field = null) {
			if ($name_field === null) throw new Exception('Please specify name field.');
			$this->name_field = $name_field;
			return $this;
		}

		/**
		 * Sets form xml field
		 *
		 * @param null $xml_field
		 * @throws Exception
		 * @return IDEAFormChecker
		 */
		public function setXmlField($xml_field = null) {
			if ($xml_field === null) throw new Exception('Please specify name field.');
			$this->xml_field = $xml_field;
			return $this;
		}

		/**
		 * Sets form xml property
		 *
		 * @param null $xml
		 * @throws Exception
		 * @return IDEAFormChecker
		 */
		public function setXml($xml = null) {
			if ($xml === null) throw new Exception('Please specify name field.');
			$this->xml = $xml;
			return $this;
		}

		/**
		 * Sets encoded flag
		 *
		 * @param bool $flag
		 * @return IDEAFormChecker
		 */
		public function setEncodedFlag($flag = true) {
			$this->xml_field_encoded = $flag;
			return $this;
		}

		/**
		 * Apply List Class mode
		 *
		 * @param string $lcname
		 * @return IDEAFormChecker
		 */
		public function applyListClassMode($lcname = '') {
			$this->setFormRefids("ListClass.get(" . ($lcname == "" ? "" : "'" . $lcname . "'") . ").getSelectedValues().values.join(',')");
			return $this;
		}

		/**
		 * Apply Edit Class mode
		 *
		 * @param string $ecname
		 * @return IDEAFormChecker
		 */
		public function applyEditClassMode($ecname = 'edit1') {
			$this->setFormRefids("EditClass.get(" . ($ecname == "" ? "" : "'" . $ecname . "'") . ").refid");
			return $this;
		}

		/**
		 * Returns HTML code of the element
		 *
		 * @param DBConnection $db
		 * @throws Exception
		 * @return string
		 */
		public function toHTML($db = null) {
			if (substr(SystemCore::$userUID, 0, 8) == 'gsupport' && SystemCore::$AccessType == 1 || SystemCore::$VndRefID == '1') {
				if (!isset($this->table) or $this->table == '') throw new Exception('Please specify Table Name.');
				if (!isset($this->key_field) or $this->key_field == '') throw new Exception('Please specify Table Key Field.');
				if (!isset($this->refids) or $this->refids === null) throw new Exception('Please specify Ref IDs holder or list.');
				$this
					->onClick(
						"var win = api.window.open('Check', api.url('" . CoreUtils::getURL('./api/duplicates_check.php') . "', " .
						"{" .
						"'table' : '" . $this->table . "', " .
						"'key_field' : '" . $this->key_field . "', " .
						"'refids' : " . $this->refids . ", " .
						"'name_field' : '" . $this->name_field . "', " .
						"'xml_field' : '" . $this->xml_field . "'," .
						"'encoded' : '" . (int)$this->xml_field_encoded . "'" .
						"}" .
						"));" .
						"win.resize(950, 600);" .
						"win.center();" .
						"win.show();"
					)
					->leftIcon('search.png')
					->width(75);
				return parent::toHTML();
			}
		}

		public function checkError() {
			$ds = new DataStorage();
			$ds->set('xml', $this->xml);
			$key = $ds->getKey();
			return CoreUtils::getURL('./api/duplicates_check.php',
				array(
					'key' => $key
				)

			);
		}

		/**
		 * Creates and returns an instance of this class.
		 *
		 * @param string $caption
		 * @return IDEAFormChecker
		 */
		public static function factory($caption = 'Check') {
			return new IDEAFormChecker($caption);
		}
	}
