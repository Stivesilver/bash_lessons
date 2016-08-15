<?php

	/**
	 * For editing XML forms
	 * Class IDEAFormEditor
	 *
	 * @author Alex Kalevich
	 * @copyright LumenTouch, 2014
	 */
	class IDEAFormEditor {

		/**
		 * Form Data Storage ID
		 *
		 * @var string
		 */
		private $formDSKey;

		/**
		 * Table name
		 * Example: webset.std_forms
		 *
		 * @var string
		 */
		private $table;

		/**
		 * Key Field name
		 * Example: refid
		 *
		 * @var string
		 */
		private $key_field;

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
		 * Primary key
		 *
		 * @var integer
		 */
		private $key;

		/**
		 * base64 Y/N
		 *
		 * @var boolean
		 */
		private $base64 = false;

		/**
		 * Sql Form Category
		 *
		 * @var string
		 */
		private $catSql = null;

		/**
		 * XML
		 *
		 * @var string
		 */
		private $xml;

		/**
		 * XML
		 *
		 * @var name
		 */
		private $name;

		/**
		 * Finish Url
		 *
		 * @var string
		 */
		private $url_finish = 'javascript:api.window.destroy();';

		/**
		 * Cancel Url
		 *
		 * @var string
		 */
		private $url_cancel = 'javascript:api.window.destroy();';

		/**
		 * Save Url
		 *
		 * @var string
		 */
		private $url_save = 'javascript:api.window.destroy();';

		/**
		 * Class Constructor
		 *
		 * @param null $formDSKey
		 * @param $table
		 * @param $key_field
		 * @param $name_field
		 * @param $xml_field
		 * @param $key
		 * @param bool $base64
		 * @return IDEAFormEditor
		 */
		public function __construct($table, $key_field, $name_field, $xml_field, $key, $base64 = false, $formDSKey = null) {
			if ($formDSKey == null) {
				$this->formDSKey = DataStorage::factory()
					->getKey();
			} else {
				$this->formDSKey = DataStorage::factory($formDSKey)
					->getKey();
				$this->refreshProperties();
			}
			$this->table = $table;
			$this->key_field = $key_field;
			$this->name_field = $name_field;
			$this->xml_field = $xml_field;
			$this->key = $key;
			$this->base64 = $base64;
			$this->f = $base64;
		}

		/**
		 * Creates and returns an instance of this class.
		 *
		 * @param $table
		 * @param $key_field
		 * @param $name_field
		 * @param $xml_field
		 * @param $key
		 * @param bool $base64
		 * @param null $formDSKey
		 * @return IDEAFormEditor
		 */
		public static function factory($table, $key_field, $name_field, $xml_field, $key, $base64 = false, $formDSKey = null) {
			return new IDEAFormEditor($table, $key_field, $name_field, $xml_field, $key, $base64, $formDSKey);
		}

		/**
		 * Initializes Properties from Data Storage
		 *
		 */
		private function refreshProperties() {
			$ds = DataStorage::factory($this->formDSKey);
			$this->key = $ds->get('refid');
			$this->name = $ds->get('name');
			$this->xml = $ds->get('xml');
			$this->table = $ds->get('table');
			$this->key_field = $ds->get('key_field');
			$this->name_field = $ds->get('name_field');
			$this->xml_field = $ds->get('xml_field');
			$this->url_cancel = $ds->get('url_cancel');
			$this->url_finish = $ds->get('url_finish');
			$this->url_save = $ds->get('url_save');
		}

		/**
		 * Initializes Data Storage parameters
		 *
		 */
		private function refreshDataStorage() {
			$ds = DataStorage::factory($this->formDSKey);
			$ds->set('refid', $this->key);
			$ds->set('name', $this->name);
			$ds->set('xml', $this->xml);
			$ds->set('table', $this->table);
			$ds->set('key_field', $this->key_field);
			$ds->set('name_field', $this->name_field);
			$ds->set('xml_field', $this->xml_field);
			$ds->set('url_cancel', $this->url_cancel);
			$ds->set('url_finish', $this->url_finish);
			$ds->set('url_save', $this->url_save);
		}

		/**
		 * Sets Forms Category Sql
		 *
		 * @param string $sql
		 * @throws Exception
		 * @return $this
		 */
		public function setCatSql($sql = '') {
			if ($sql == '') throw new Exception('Please specify form category Sql.');
			$this->catSql = $sql;
			return $this;
		}

		/**
		 * Sets Cancel Url property
		 *
		 * @param string $val
		 * @return IDEAForm
		 */
		public function setUrlCancel($val) {
			$this->url_cancel = $val;
			$this->refreshDataStorage();
			return $this;
		}

		/**
		 * Sets Finish Url property
		 *
		 * @param string $val
		 * @return IDEAForm
		 */
		public function setUrlFinish($val) {
			$this->url_finish = $val;
			$this->refreshDataStorage();
			return $this;
		}

		/**
		 * Sets Save Url property
		 *
		 * @param string $val
		 * @return IDEAForm
		 */
		public function setUrlSave($val) {
			$this->url_save = $val;
			$this->refreshDataStorage();
			return $this;
		}

		/**
		 * Get Forms Data
		 *
		 * @return array
		 */
		private function getData() {
			$res = db::execSQL("
				SELECT " . $this->key_field . ",
					   " . $this->xml_field . " AS xml,
				       " . $this->name_field . " AS name
				  FROM " . $this->table . "
				 WHERE " . $this->key_field . " = " . $this->key . "
			")->assoc();
			if ($this->base64 == true) {
				$res['xml'] = base64_decode($res['xml']);
			}
			$this->name = $res['name'];
			$this->xml = $res['xml'];

			$this->refreshDataStorage();
		}

		/**
		 * @return string
		 */
		public function getUrlPanel() {
			$this->getData();
			return CoreUtils::getURL('./api/form_editor.php', array('fkey' => $this->formDSKey)) ;
		}
	}
