<?php

	/**
	 * Class IDEAScreenType
	 *
	 * @author Alex Kalevich
	 * @copyright LumenTouch, 2014
	 */
	class IDEAScreenType extends RegularClass {

		/**
		 * Menu Type
		 *
		 * @var string
		 */
		private $type;

		/**
		 * Class Constructor
		 *
		 * @param $type
		 * @return IDEAScreenType
		 */
		public function __construct($type) {
			parent::__construct();
			$this->type = $type;
		}

		/**
		 * Creates and returns an instance of this class.
		 *
		 * @param $type
		 * @return IDEAScreenType
		 */
		public static function factory($type) {
			if (!$type) {
				$type = db::execSQL("
					SELECT scr_codeword
					  FROM webset.sped_screen
					WHERE scr_default_sw = 'Y'
				")->getOne();
			}
			return new IDEAScreenType($type);
		}

		public function getUrl() {
			$url = $this->execSQL("
				SELECT scr_url
				  FROM webset.sped_screen
				WHERE scr_codeword = '" . $this->type . "'
			")->getOne();
			return $url;
		}

		public function getName() {
			$name = $this->execSQL("
				SELECT scr_name
				  FROM webset.sped_screen
				WHERE scr_codeword = '" . $this->type . "'
			")->getOne();
			return $name;
		}

		public function getID() {
			$id = $this->execSQL("
				SELECT scr_refid
				  FROM webset.sped_screen
				WHERE scr_codeword = '" . $this->type . "'
			")->getOne();
			return $id;
		}

	}
