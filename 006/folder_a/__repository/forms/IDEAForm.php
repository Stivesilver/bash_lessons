<?php

	/**
	 * IDEA Form Class
	 * This class provides PDF Form utilities
	 *
	 * @final
	 * @copyright Lumen Touch, 2012
	 */
	final class IDEAForm {

		/**
		 * Form Data Storage ID
		 *
		 * @var string
		 */
		private $formDSKey;

		/**
		 * Form Title
		 *
		 * @var string
		 */
		private $title;

		/**
		 * XML Template
		 *
		 * @var string
		 */
		private $template;

		/**
		 * PDF Template
		 * This property will be used for Alternate Printing
		 *
		 * @var string
		 */
		private $template_pdf = null;

		/**
		 * XML Values
		 *
		 * @var string
		 */
		private $values;

		/**
		 * Archived Form or Not. If TRUE form is readonly
		 *
		 * @var bool
		 */
		private $archived;

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
		 * Finish Url
		 *
		 * @var string
		 */
		private $url_finish;

		/**
		 * Cancel Url
		 *
		 * @var string
		 */
		private $url_cancel;

		/**
		 * Save Url
		 *
		 * @var string
		 */
		private $url_save;

		/**
		 * Save Function
		 *
		 * @var string
		 */
		private $save_func = 'saveXml';

		/**
		 * Add Option
		 *
		 * @var boolean
		 */
		private $add_option = false;

		/**
		 * Additional Parameters
		 *
		 * @var array
		 */
		private $parameters;

		/**
		 * Add Edit Contorls
		 *
		 * @var array
		 */
		private $controls = array();

		/**
		 * Populate Button
		 *
		 * @var boolean $populateButton
		 */
		private $populateButton;

		/**
		 * Initializes IDEAForm object
		 *
		 * @param string $fds
		 */
		public function __construct($fds = '') {
			if ($fds == '') {
				$this->formDSKey = DataStorage::factory()
					->getKey();
			} else {
				$this->formDSKey = DataStorage::factory($fds)
					->getKey();
				$this->refreshProperties();
			}
		}

		/**
		 * Initializes Properties from Data Storage

		 */
		private function refreshProperties() {
			$ds = DataStorage::factory($this->formDSKey);
			$this->archived = $ds->get('archived');
			$this->lastupdate = $ds->get('lastupdate');
			$this->lastuser = $ds->get('lastuser');
			$this->parameters = unserialize($ds->get('parameters'));
			$this->controls = unserialize($ds->get('controls'));
			$this->template = $ds->get('template');
			$this->template_pdf = $ds->get('template_pdf');
			$this->title = $ds->get('title');
			$this->url_cancel = $ds->get('url_cancel');
			$this->url_finish = $ds->get('url_finish');
			$this->populateButton = $ds->get('populate_button');
			$this->url_save = $ds->get('url_save');
			$this->save_func = $ds->get('save_func');
			$this->add_option = $ds->get('url_save');
			$this->values = $ds->get('values');
			$this->js = $ds->get('values');
		}

		/**
		 * Initializes Data Storage parameters

		 */
		private function refreshDataStorage() {
			$ds = DataStorage::factory($this->formDSKey);
			$ds->set('archived', $this->archived);
			$ds->set('lastupdate', $this->lastupdate);
			$ds->set('lastuser', $this->lastuser);
			$ds->set('parameters', serialize($this->parameters));
			$ds->set('controls', serialize($this->controls));
			$ds->set('template', $this->template);
			$ds->set('template_pdf', $this->template_pdf);
			$ds->set('title', $this->title);
			$ds->set('url_cancel', $this->url_cancel);
			$ds->set('url_finish', $this->url_finish);
			$ds->set('populate_button', $this->populateButton);
			$ds->set('url_save', $this->url_save);
			$ds->set('save_func', $this->save_func);
			$ds->set('add_option', $this->add_option);
			$ds->set('values', $this->values);
			$ds->set('js', $this->js);
		}

		/**
		 * Sets Archived property
		 *
		 * @param bool $val
		 * @return IDEAForm
		 */
		public function setArchived($val) {
			$this->archived = $val;
			$this->refreshDataStorage();
			return $this;
		}

		/**
		 * Sets Lastupdate property
		 *
		 * @param string $val
		 * @return IDEAForm
		 */
		public function setLastUpdate($val) {
			$this->lastupdate = $val;
			$this->refreshDataStorage();
			return $this;
		}

		/**
		 * Sets Lastupdate property
		 *
		 * @param string $val
		 * @return IDEAForm
		 */
		public function setLastUser($val) {
			$this->lastuser = $val;
			$this->refreshDataStorage();
			return $this;
		}

		/**
		 * Sets additional paramter
		 *
		 * @param string $key
		 * @param string $val
		 * @return IDEAForm
		 */
		public function setParameter($key, $val) {
			$this->parameters[$key] = $val;
			$this->refreshDataStorage();
			return $this;
		}

		/**
		 * Sets Edit Controls
		 *
		 * @param $name
		 * @param $id
		 * @param $value
		 * @param $type
		 * @return IDEAForm
		 */
		public function setControls($name = null, $id, $value = null, $type = null) {
			$this->controls[$id] = array(
				'name' => $name,
				'id' => $id,
				'value' => $value,
				'type' => $type
			);
			$this->refreshDataStorage();
			return $this;
		}

		/**
		 * Sets Template property
		 *
		 * @param string $val
		 * @return IDEAForm
		 */
		public function setTemplate($val) {
			$this->template = $val;
			$this->refreshDataStorage();
			return $this;
		}

		/**
		 * Sets PDF Template File property
		 *
		 * @param string $path
		 * @return IDEAForm
		 */
		public function setTemplatePDF($path) {
			$this->template_pdf = $path;
			$this->refreshDataStorage();
			return $this;
		}

		/**
		 * Sets Title property
		 *
		 * @param string $val
		 * @return IDEAForm
		 */
		public function setTitle($val) {
			$this->title = $val;
			$this->refreshDataStorage();
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
		 * Sets Save Function Name
		 *
		 * @param string $val
		 * @return IDEAForm
		 */
		public function setSaveFunction($val) {
			$this->save_func = $val;
			$this->refreshDataStorage();
			return $this;
		}

		/**
		 * Sets add Option
		 *
		 * @param boolean $val
		 * @return IDEAForm
		 */
		public function setAddOption($val) {
			$this->add_option = $val;
			$this->refreshDataStorage();
			return $this;
		}

		public function setPopulateButton($val = false) {
			$this->populateButton = $val;
			$this->refreshDataStorage();
			return $this;
		}

		/**
		 * Sets Value XML
		 *
		 * @param string $val
		 * @return IDEAForm
		 */
		public function setValues($val) {
			$this->values = $val;
			$this->refreshDataStorage();
			return $this;
		}

		/**
		 * Gets Form Key
		 *
		 * @return string
		 */
		public function getFormDSKey() {
			return $this->formDSKey;
		}

		/**
		 * Gets Panel Url
		 *
		 * @return string
		 */
		public function getUrlPanel() {
			return CoreUtils::getURL('./api/form_panel.php', array('fkey' => $this->formDSKey));
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
		 * Gets PDF Template
		 *
		 * @return string
		 */
		public function getTemplatePDF() {
			return $this->template_pdf;
		}

		/**
		 * Gets Title property
		 *
		 * @return string
		 */
		public function getTitle() {
			return $this->title;
		}

		/**
		 * Gets Cancel Url property
		 *
		 * @return string
		 */
		public function getUrlCancel() {
			return $this->url_cancel;
		}

		/**
		 * Gets Finish Url property
		 *
		 * @return string
		 */
		public function getUrlFinish() {
			return $this->url_finish;
		}

		/**
		 * Gets Save Url property
		 *
		 * @return string
		 */
		public function getUrlSave() {
			return $this->url_save;
		}

		/**
		 * Gets Paramater Value
		 *
		 * @param $key
		 * @return string
		 */
		public function getParameter($key) {
			return $this->parameters[$key];
		}

		/**
		 * Gets Edti Controls
		 *
		 * @return array
		 */
		public function getContorls() {
			return $this->controls;
		}

		/**
		 *
		 * Update Edti Control Value
		 *
		 * @param $id
		 * @param $value
		 * @return IDEAForm
		 */
		public function updateContolValue($id, $value) {
			$this->controls[$id]['value'] = $value;
			$this->refreshDataStorage();
			return $this;
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param string $fds
		 * @return IDEAForm
		 */
		public static function factory($fds = '') {
			return new IDEAForm($fds);
		}

		public static function collectValues($post) {
			$values = "<values>" . PHP_EOL;
			foreach ($post as $key => $val) {
				if ($val != '' and substr($key, 0, 5) == 'form_') {
					$values .= '<value name="' . substr($key, 5, strlen($key)) . '">' . htmlspecialchars(stripslashes($val)) . '</value>' . PHP_EOL;
				}
			}
			$values .= "</values>" . PHP_EOL;
			return $values;
		}

		/**
		 * Contains Java Script
		 *
		 * @var string
		 */
		private $js = null;

		/**
		 * Adds Java Script
		 *
		 * @param string $js
		 * @return IDEAForm
		 */
		public function addJavaScript($js) {
			$this->js = $js;
			$this->refreshDataStorage();
			return $this;
		}

		/**
		 * Get File
		 *
		 * @param string $format
		 * @return file
		 * @throws Exception
		 */
		public function getFile($format = 'pdf') {
			$file = IDEADocument::factory()
				->setSource($this->template);
			if ($format == 'pdf') {
				return $file->output(IDEADocumentFormat::PDF);
			} elseif ($format == 'html') {
				return $file->output(IDEADocumentFormat::HTML);
			}
		}
	}

?>
