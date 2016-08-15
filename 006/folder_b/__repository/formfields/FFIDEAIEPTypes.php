<?php

	/**
	 * Select IEP Types
	 *
	 * @author Alex Kalevich
	 * @copyright LumenTouch, 2014
	 */
	class FFIDEAIEPTypes extends FFSelect {

		/**
		 * Class Constructor
		 *
		 * @param string $caption
		 */
		public function __construct($caption) {
			parent::__construct();
			$this->caption = $caption;
			$data = db::execSQL("
				SELECT siepmtrefid, siepmtdesc, defaultoption
				  FROM webset.statedef_ieptypes
                 WHERE screfid = " . VNDState::factory()->id . "
				   AND (enddate IS NULL or now()< enddate)
			")->indexAll();
			$value = '';
			foreach ($data as $item) {
				if ($item[2] == 'Y') {
					$value = $item[0];
				}
			}
			$this->data($data);
			$this->value($value);
		}

		/**
		 * Creates an instance of this class
		 *
		 * @static
		 * @param string $caption
		 * @return FFIDEAIEPTypes
		 */
		public static function factory($caption = 'IEP Types') {
			return new FFIDEAIEPTypes($caption);
		}

	}

?>
