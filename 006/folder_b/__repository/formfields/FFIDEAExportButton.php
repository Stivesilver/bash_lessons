<?php

	/**
	 * Creates button for Export Popup
	 *
	 * @copyright Lumen Touch, 2013
	 */
	class FFIDEAExportButton extends FFButton {

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
		 * Set Nesting Table
		 * Example: smfcrefid
		 *
		 * @var array
		 */
		private $nestingTable = null;

		/**
		 * Set Nesting Refid
		 * Example: smfcrefid
		 *
		 * @var array
		 */
		private $nestingRefid = null;

		/**
		 * Set Foreign Field
		 * Example: smfcrefid
		 *
		 * @var array
		 */
		private $foreignField = null;

		/**
		 * Set Foreign Table
		 * Example: smfcrefid
		 *
		 * @var array
		 */
		private $foreignTable = null;

		/**
		 * Set Foreign Table Key
		 * Example: smfcrefid
		 *
		 * @var array
		 */
		private $foreignTableKey = null;

		/**
		 * Set XML Template
		 *
		 * @var string
		 */
		private $xmlTemplate = null;

		/**
		 * Javascript statement which will set GET 'dskey' parameter for Export Popup windows
		 * Data Storage Key is needed to let view actual SQL for ListClass pages
		 *
		 * @var string
		 */
		private $dskey;

		/**
		 * Class Constructor

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
		 * @return FFIDEAExportButton
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
		 * @return FFIDEAExportButton
		 */
		public function setKeyField($key_field = '') {
			if ($key_field == '') throw new Exception('Please specify key field name.');
			$this->key_field = $key_field;
			return $this;
		}

		/**
		 * Sets nesting level
		 *
		 * @param string $table
		 * @param string $refid
		 * @param string $foreignField
		 * @param string $foreignTable
		 * @param string $foreignTableKey
		 * @return FFIDEAExportButton
		 */
		public function setNesting($table, $refid, $foreignField, $foreignTable, $foreignTableKey) {
			$this->nestingTable[] = $table;
			$this->nestingRefid[] = $refid;
			$this->foreignField[] = $foreignField;
			$this->foreignTable[] = $foreignTable;
			$this->foreignTableKey[] = $foreignTableKey;
			return $this;
		}

		/**
		 * Sets Import/Export Template
		 *
		 * @param string $table
		 * @throws Exception
		 * @return FFIDEAExportButton
		 */
		public function setXMLTemplate($xml = '') {
			if ($xml == '') throw new Exception('Please specify correct xml.');
			$this->xmlTemplate = $xml;
			return $this;
		}

		/**
		 * Sets refids property
		 *
		 * @param string $refids
		 * @throws Exception
		 * @return FFIDEAExportButton
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
		 * @return FFIDEAExportButton
		 */
		public function setFormRefids($refids = null) {
			if ($refids === null) throw new Exception('Please specify Refids javascript statement.');
			$this->refids = $refids;
			return $this;
		}

		/**
		 * Sets dskey property
		 *
		 * @param string $dskey
		 * @throws Exception
		 * @return FFIDEAExportButton
		 */
		public function setDsKey($dskey = '') {
			if ($dskey == '') throw new Exception('Please specify Data Storage key.');
			$this->dskey = $dskey;
			return $this;
		}

		/**
		 * Apply List Class mode
		 *
		 * @param string $lcname
		 * @return FFIDEAExportButton
		 */
		public function applyListClassMode($lcname = '') {
			$this->setFormRefids("ListClass.get(" . ($lcname == "" ? "" : "'" . $lcname . "'") . ").getSelectedValues().values.join(',')");
			$this->setDsKey("ListClass.get(" . ($lcname == "" ? "" : "'" . $lcname . "'") . ").dsKey");
			return $this;
		}

		/**
		 * Apply Edit Class mode
		 *
		 * @param string $ecname
		 * @return FFIDEAExportButton
		 */
		public function applyEditClassMode($ecname = 'edit1') {
			$this->setFormRefids("EditClass.get(" . ($ecname == "" ? "" : "'" . $ecname . "'") . ").refid");
			$this->setDsKey("EditClass.get(" . ($ecname == "" ? "" : "'" . $ecname . "'") . ").getFormData().edit_dsKey");
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

				$export_dskey = DataStorage::factory()
					->set('table', $this->table)
					->set('key_field', $this->key_field)
					->set('refids', $this->refids)
					->set('nestingTable', json_encode($this->nestingTable))
					->set('nestingRefid', json_encode($this->nestingRefid))
					->set('foreignField', json_encode($this->foreignField))
					->set('foreignTable', json_encode($this->foreignTable))
					->set('foreignTableKey', json_encode($this->foreignTableKey))
					->set('xmlTemplate', $this->xmlTemplate)
					->getKey();
				$this
					->onClick(
						"var win = api.window.open('Export Data', api.url('" . CoreUtils::getURL('./api/idea_export_main.php') . "', " .
						"{'dskey' : " . $this->dskey . ", 'refids' : " . $this->refids . ", 'export_dskey' : '" . $export_dskey . "'}" .
						"));" .
						"win.resize(950, 600);" .
						"win.center();" .
						"win.show();"
					)
					->leftIcon('export.png')
					->width(80);
				return parent::toHTML();
			}
		}

		/**
		 * Creates an instance of this class
		 *
		 * @static
		 * @param string $caption
		 * @return FFIDEAExportButton
		 */
		public static function factory($caption = 'Export') {
			return new FFIDEAExportButton($caption);
		}

	}

?>
