<?php

	/**
	 * Construction XML Form Template class
	 * This class provides data of Construction Form template stored in webset.sped_constructions
	 *
	 * @final
	 * @copyright Lumen Touch, 2015
	 */
	final class IDEAFormTemplateConstruction extends RegularClass {

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
		 * @var string
		 */
		private $template;

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
		 * Class Defaults
		 *
		 * @var string
		 */
		private $class_defaults;

		/**
		 * Initializes IDEAFormTemplateConstruction object
		 *
		 * @param integer $id
		 */
		public function __construct($id) {
			if (!($id > 0)) throw new Exception('Setup Form ID number.');
			$this->id = $id;
			$SQL = "
				SELECT xml.cnname,
					   xml.cnbody,
					   xml.class_defaults,
					   xml.lastuser,
					   xml.lastupdate
				  FROM webset.sped_constructions xml
				 WHERE xml.cnrefid = " . $this->id . "
			";
			$form = $this->execSQL($SQL)->assoc();
			$this->title = $form['cnname'];
			$this->template = $form['cnbody'];
			$this->class_defaults = $form['class_defaults'];
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
		 * Gets class defaults
		 *
		 * @return string
		 * @depricate
		 */
		public function getClassDefaults() {
			return $this->class_defaults;
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
		 * @return IDEAFormTemplateConstruction
		 */
		public static function factory($id = 0) {
			return new IDEAFormTemplateConstruction($id);
		}

	}

?>
