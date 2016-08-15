<?php

	/**
	 * Creates button for VND Group
	 *
	 * @copyright Lumen Touch, 2015
	 * @author Alex Kalevich
	 */
	class IDEAGroupPopulate extends FFButton {

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
		 * Name field of table
		 * Example: smfcrefid
		 *
		 * @var string
		 */
		private $name_field;

		/**
		 * Content field of table
		 * Example: smfcrefid
		 *
		 * @var string
		 */
		private $cont_field;

		/**
		 * Help html
		 *
		 * @var string
		 */
		private $groups;

		/**
		 * Javascript statement which will set GET 'refids' parameter for Export Popup windows
		 * Example: ListClass.get().getSelectedValues().values.join(',')
		 * Example: '1,2,3'
		 *
		 * @var string
		 */
		private $refids = null;

		/**
		 * Additional Parameters
		 *
		 * @var array
		 */
		private $keys;

		/**
		 * Additional Parameters
		 *
		 * @var string
		 */
		private $where;

		/**
		 * Additional Parameters
		 *
		 * @var string
		 */
		private $join;

		/**
		 * Additional Parameters
		 *
		 * @var string
		 */
		private $search;

		/**
		 * Class Constructor
		 */
		public function __construct($caption) {
			# call parent constructor
			parent::__construct();
			$this->value($caption);
		}

		/**
		 * Add search field to current Item Windows
		 *
		 * @param mized $title
		 * @param null $sqlField
		 * @param string $type
		 * @return IDEAPopulateWindow
		 */
		public function addSearch($title = NULL, $sqlField = NULL, $type = NULL) {
			$this->search[] = array('title' => $title, 'sqlField' => $sqlField, 'type' => $type);
			return $this;
		}

		/**
		 * Sets table property
		 *
		 * @param string $table
		 * @throws Exception
		 * @return IDEAGroupPopulate
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
		 * @return IDEAGroupPopulate
		 */
		public function setKeyField($key_field = '') {
			if ($key_field == '') throw new Exception('Please specify key field name.');
			$this->key_field = $key_field;
			return $this;
		}

		/**
		 * Sets name_field property
		 *
		 * @param string $name_field
		 * @throws Exception
		 * @return IDEAGroupPopulate
		 */
		public function setNameField($name_field = '') {
			if ($name_field == '') throw new Exception('Please specify key field name.');
			$this->name_field = $name_field;
			return $this;
		}

		/**
		 * Sets cont_field property
		 *
		 * @param string $cont_field
		 * @throws Exception
		 * @return IDEAGroupPopulate
		 */
		public function setContField($cont_field = '') {
			if ($cont_field == '') throw new Exception('Please specify key field name.');
			$this->cont_field = $cont_field;
			return $this;
		}

		/**
		 * Sets help html
		 *
		 * @param array $groups
		 * @return IDEAGroupPopulate
		 */
		public function setGroups($groups = null) {
			$this->groups = implode(',', $groups);
			return $this;
		}

		/**
		 * Adds additional paramter
		 *
		 * @param string $val
		 * @return IDEAForm
		 */
		public function addKeys($val) {
			$this->keys[] = $val;
			return $this;
		}

		/**
		 * Adds additional paramter
		 *
		 * @param string $val
		 * @return IDEAForm
		 */
		public function addWhere($val) {
			$this->where .= ' AND ' . $val;
			return $this;
		}

		/**
		 * Adds additional paramter
		 *
		 * @param string $val
		 * @return IDEAForm
		 */
		public function addJoin($val) {
			$this->join .= ' ' . $val;
			return $this;
		}

		/**
		 * Sets form refids property
		 *
		 * @param string $refids
		 * @throws Exception
		 * @return IDEAGroupPopulate
		 */
		public function setFormRefids($refids = null) {
			if ($refids === null) throw new Exception('Please specify Refids javascript statement.');
			$this->refids = $refids;
			return $this;
		}

		/**
		 * Apply List Class mode
		 *
		 * @param string $lcname
		 * @return IDEAGroupPopulate
		 */
		public function applyListClassMode($lcname = '') {
			$this->setFormRefids("ListClass.get(" . ($lcname == "" ? "" : "'" . $lcname . "'") . ").getSelectedValues().values.join(',')");
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

			$key = SystemCore::$Registry->readKey('webset', 'districts_groups', 'xml');

			if ($key != -1) {
				$xml = current($key);
				$dataTree = new SimpleXMLElement($xml);
				foreach ($dataTree->xpath('group/district') as $district) {
					$districts[] = array('name' => (string)$district['name'], 'vndrefid' => (string)$district['vndrefid']);
				}
				$ids = array_map(create_function('$a', 'return $a["vndrefid"];'), $districts);
				if (in_array(SystemCore::$VndRefID, $ids)) {
					$this->setGroups($ids);
					$this
						->onClick(
							"var win = api.window.open('Set to Group', api.url('" . CoreUtils::getURL('./api/groups_list.php') . "'), " .
							"{" .
							"'ids' : '" . $this->groups . "', " .
							"'forms' : " . $this->refids . ", " .
							"'table' : '" . $this->table . "', " .
							"'key_field' : '" . $this->key_field . "', " .
							"'name_field' : '" . $this->name_field . "', " .
							"'keys' : '" . json_encode($this->keys) . "', " .
							"'where' : '" . $this->where . "', " .
							"'join' : '" . $this->join . "', " .
							"'search' : '" . json_encode($this->search) . "', " .
							"'cont_field' : '" . $this->cont_field . "'" .
							"}" .
							");" .
							"win.resize(950, 600);" .
							"win.center();" .
							"win.show();"
						)
						->leftIcon('student_transcript_16.png')
						->width(120);
					return parent::toHTML();
				}
			}
		}

		/**
		 * Creates an instance of this class
		 *
		 * @static
		 * @param string $caption
		 * @return IDEAGroupPopulate
		 */
		public static function factory($caption = 'Set to Group') {
			return new IDEAGroupPopulate($caption);
		}

	}

?>
