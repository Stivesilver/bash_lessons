<?php

	/**
	 * Class IDEAFormPopulate
	 *
	 * @author Alex Kalevich
	 * @copyright LumenTouch, 2014
	 */
	class IDEAFormPopulate {

		/**
		 * Class Constructor
		 *
		 * @return IDEAFormPopulate
		 */

		/**
		 * @var $fkey
		 */
		private $ofkey;

		public function __construct($ofkey = null) {
			$this->ofkey = $ofkey;
		}

		/**
		 * Creates and returns an instance of this class.
		 *
		 * @param $ofkey
		 * @return \IDEAFormPopulate
		 */
		public static function factory($ofkey = null) {
			return new IDEAFormPopulate($ofkey);
		}

		/**
		 * @return mixed
		 */
		public function getPopulateButton() {
			$button = FFMenuButton::factory('Populate')
				->leftIcon('wizard2_16.png')
				->addItem('Previous',
					"api.desktop.open(
					'Previous',
					api.url(api.virtualRoot + '/apps/idea/__repository/forms/api/populate.php', {'state_id' : '" . $this->getStateId() . "', 'dskey': " . json_encode($this->getDskey()) . ", 'ofkey': " . json_encode($this->ofkey) . "})
					).addEventListener(
						ObjectEvent.COMPLETE,
						function (e) {
							api.reload();
						}
					);",
					'wizard2_16.png');
			return $button;
		}

		private function getDskey() {
			$form = IDEAForm::factory($this->ofkey);
			return $form->getParameter('dskey');
		}

		private function getStateId() {
			$form = IDEAForm::factory($this->ofkey);
			return $form->getParameter('state_id');
		}
	}
