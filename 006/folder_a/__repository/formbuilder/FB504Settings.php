<?php

	/**
	 * Test Form Environment
	 *
	 * @author Alex Kalevich
	 * @copyright Lumen Touch, 2015
	 */
	class FB504Settings extends FBSettings{

		/**
		 * Class Constructor
		 *
		 * @return FB504Settings
		 */
		public function __construct() {
			parent::__construct();
			$this->highlightInputFields = true;
			$this->addEntity(new FBIDEAGeneralEntity());
			$this->addEntity(new FBStudentEntity());
			$this->addEntity(new FBParentEntity());

		}

		/**
		 * Creates and returns instance of this class
		 *
		 * @return FB504Settings
		 */
		public static function factory() {
			return new FB504Settings();
		}
	}
