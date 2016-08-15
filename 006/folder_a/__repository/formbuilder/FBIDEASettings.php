<?php

	/**
	 * Test Form Environment
	 *
	 * @author Alex Kalevich
	 * @copyright Lumen Touch, 2015
	 */
	class FBIDEASettings extends FBSettings{

		/**
		 * Class Constructor
		 *
		 * @return FBIDEASettings
		 */
		public function __construct() {
			parent::__construct();
			$this->highlightInputFields = true;
			$this->addEntity(new FBIDEAGeneralEntity());
			$this->addEntity(new FBIDEAStudentEntity());
			$this->addEntity(new FBIDEAParentEntity());
		}

		/**
		 * Creates and returns instance of this class
		 *
		 * @return FBIDEASettings
		 */
		public static function factory() {
			return new FBIDEASettings();
		}
	}
