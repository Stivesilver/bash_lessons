<?php

	/**
	 * Creates Action Button
	 *
	 * @copyright Lumen Touch, 2015
	 * @author Alex Kalevich
	 */
	class FFIDEAActionButton extends FFMenuButton {

		/**
		 * Set Reorder Table
		 * Example: webset.std_bgb_goal
		 *
		 * @var string
		 */
		private $table = null;

		/**
		 * Key field of table
		 * Example: grefid
		 *
		 * @var string
		 */
		private $key_field;

		/**
		 * Set Sequence Number
		 * Example: order_num
		 *
		 * @var string
		 */
		private $seq_field = null;

		/**
		 * Set Nesting Table
		 * Example: smfcrefid
		 *
		 * @var array
		 */
		private $nesting = null;

		/**
		 * Set Sql Condition
		 *
		 * @var string
		 */
		private $condition = null;

		/**
		 * Set dskey
		 *
		 * @var string
		 */
		private $dskey = null;

		/**
		 * Class Constructor

		 */
		public function __construct() {
			parent::__construct();
			$this->value('Action');
			$this->leftIcon('framework_16.png');
			//			$this->leftIcon('cogwheel_16.png');
			$this->iconsSize(32);
		}

		/**
		 * Creates an instance of this class
		 *
		 * @static
		 * @return FFIDEAActionButton
		 */
		public static function factory() {
			return new FFIDEAActionButton();
		}

		/**
		 * Sets seq table data
		 *
		 * @param string $table
		 * @param string $key_field
		 * @param string $seq_field
		 * @return $this
		 * @throws Exception
		 */
		public function setSeqTable($table = '', $key_field = '', $seq_field = '') {
			if ($table == '') throw new Exception('Please specify table name.');
			if ($key_field == '') throw new Exception('Please specify key field.');
			if ($seq_field == '') throw new Exception('Please specify sequence number.');
			$this->table = $table;
			$this->key_field = $key_field;
			$this->seq_field = $seq_field;
			return $this;
		}

		/**
		 * Sets nesting sequence table
		 *
		 * @param string $table
		 * @param string $key_field
		 * @param string $seq_field
		 * @param string $foreign_field
		 * @param string $foreign_table
		 * @param string $foreign_table_key
		 * @return FFIDEAActionButton
		 */
		public function setNestingSeq($table, $key_field, $seq_field, $foreign_field, $foreign_table, $foreign_table_key) {
			$this->nesting[] = array(
				'table' => $table,
				'key_field' => $key_field,
				'seq_field' => $seq_field,
				'foreign_field' => $foreign_field,
				'foreign_table' => $foreign_table,
				'foreign_table_key' => $foreign_table_key
			);

			return $this;
		}

		/**
		 * Return Sql Condition
		 *
		 * @param $field
		 * @param $value
		 * @param $method
		 * @return $this
		 * @throws Exception
		 */
		public function key($field, $value, $method = '=') {
			if ($field == '') throw new Exception('Please specify field name.');
			if ($value == '') throw new Exception('Please specify value.');
			$value = $this->dbUtils->escape($value);
			if ($this->condition) {
				$this->condition .= ' AND ' . $this->table . '.' . $field . ' ' . $method . ' ' . "'$value'";
			} else {
				$this->condition = 'WHERE ' . $this->table . '.' . $field . ' ' . $method . ' ' . "'$value'";
			}
			return $this;
		}

		/**
		 * Returns HTML code of the element
		 *
		 * @param DBConnection $db
		 * @return string
		 */
		public function toHTML($db = null) {
			$this->setDataStorage();
			# change DB driver if needed
			$this->changeDB($db);

			$html = '';

			$html .= $this->js('new FFIDEAActionButton(' . json_encode($this->id) . ', ' . json_encode($this->dskey) . '); FFIDEAActionButton.packPath = ' . json_encode(CoreUtils::getVirtualPath('./')) . ';');

			return $html . parent::toHTML($db);
		}

		/**
		 * Reordering sequence numbers
		 *
		 * @return $this
		 * @throws Exception
		 */
		public function reorderSeq() {
			if ($this->condition == '') throw new Exception('Please use method KEY to specify sql condition.');
			FileUtils::getJSFile('./js/FFIDEAActionButton.js')->append();
			$this->addItem('Reorder', 'FFIDEAActionButton.get(' . json_encode($this->id) . ').reorder()', 'crm_32.png');
			return $this;
		}

		private function setDataStorage() {
			$ds = new DataStorage();
			$ds->set('table', $this->table);
			$ds->set('key_field', $this->key_field);
			$ds->set('seq_field', $this->seq_field);
			$ds->set('condition', $this->condition);
			$ds->set('nesting', $this->nesting);
			$this->dskey = $ds->getKey();

			return $this;
		}

		/**
		 * Initializes Properties from Data Storage

		 */
		private function refreshProperties() {
			$ds = DataStorage::factory($this->dskey);
			$this->table = $ds->get('table');
			$this->key_field = $ds->get('key_field');
			$this->seq_field = $ds->get('seq_field');
			$this->nesting = $ds->get('nesting');
			$this->condition = $ds->get('condition');
		}

		/***
		 * Return dskey
		 *
		 * @return string
		 */
		public function getDsKey() {
			$this->setDataStorage();
			return $this->dskey;
		}

		/**
		 * Set dskey
		 *
		 * @param $dskey
		 * @return $this
		 * @throws Exception
		 */
		public function setDsKey($dskey) {
			if ($dskey == '') throw new Exception('Please specify field name.');
			$this->dskey = $dskey;
			$this->refreshProperties();
			return $this;
		}

		public function reorder() {
			$num = db::execSQL("
				SELECT max(" . $this->seq_field . ")
				  FROM " . $this->table . "
				  " . $this->condition . "
			")->getOne() + 1;

			$ids = db::execSQL("
				SELECT " . $this->key_field . ",
					   $this->seq_field
				  FROM " . $this->table . "
				       " . $this->condition . "
				 ORDER BY " . $this->key_field . "
			")->assocAll();

			foreach ($ids AS $id) {
				if ($id[$this->seq_field] == null) {
					DBImportRecord::factory($this->table, $this->key_field)
						->key($this->key_field, $id[$this->key_field])
						->set($this->seq_field, $num)
						->import(DBImportRecord::UPDATE_ONLY);
					$num++;
				}
				if ($this->nesting && $this->nesting[0]) {
					$this->updateNesting($this->nesting[0], $id[$this->key_field], $this->nesting);
				}
			}
			return null;
		}

		private function updateNesting($nest, $prev_key, $nesting, $i = 1) {
			$nnum = db::execSQL("
				SELECT max(" . $nest['seq_field'] . ")
				  FROM " . $nest['table'] . "
				  WHERE " . $prev_key . " = " . $nest['foreign_field'] . "
			")->getOne() + 1;
			$nids = db::execSQL("
				SELECT " . $nest['key_field'] . ",
					   " . $nest['seq_field'] . "
				  FROM " . $nest['table'] . "
				       WHERE " . $prev_key . " = " . $nest['foreign_field'] . "
				 ORDER BY " . $nest['key_field'] . "
			")->assocAll();
			foreach ($nids as $nid) {
				if ($nid[$nest['seq_field']] == null) {
					DBImportRecord::factory($nest['table'], $nest['key_field'])
						->key($nest['key_field'], $nid[$nest['key_field']])
						->set($nest['seq_field'], $nnum)
						->import(DBImportRecord::UPDATE_ONLY);
					$nnum++;
				}
				if (isset($nesting[$i])) {
					$this->updateNesting($nesting[$i], $nid[$nest['key_field']], $nesting, $i + 1);
				}
			}
			return;
		}

	}

?>
