<?php

	/**
	 * Creates button for Help Popup
	 *
	 * @copyright Lumen Touch, 2015
	 * @author Alex Kalevich
	 */
	class FFIDEAHelpButton extends FFButton {

		/**
		 * Help html
		 *
		 * @var string
		 */
		private $html;

		/**
		 * Constraction ID
		 *
		 * @var string
		 */
		private $const_id;


		/**
		 * Class Constructor

		 */
		public function __construct($caption) {
			# call parent constructor
			parent::__construct();
			$this->value($caption);
		}


		/**
		 * Sets help html
		 *
		 * @param string $html
		 * @return FFIDEAHelpButton
		 */
		public function setHTML($html = null) {
			$this->html = $html;
			return $this;
		}

		/**
		 * Sets help construction ID
		 *
		 * @param string $id
		 * @return FFIDEAHelpButton
		 */
		public function setHTMLByConstruction($id = null) {
			$this->const_id =  $id;
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
			if (substr(SystemCore::$userUID, 0, 8) == 'gsupport' && SystemCore::$AccessType == 1 || SystemCore::$VndRefID == '1') {
				$this
					->onClick(
						"var win = api.window.open('Help', api.url('" . CoreUtils::getURL('./api/idea_help_main.php') . "'), " .
						"{" .
						"'html' : '" . $this->html . "', " .
						"'constr_id' : '" . $this->const_id . "'" .
						"}" .
						");" .
						"win.resize(950, 600);" .
						"win.center();" .
						"win.show();"
					)
					->leftIcon('help.png')
					->width(80);
				return parent::toHTML();
			}
		}

		/**
		 * Creates an instance of this class
		 *
		 * @static
		 * @param string $caption
		 * @return FFIDEAHelpButton
		 */
		public static function factory($caption = 'Help') {
			return new FFIDEAHelpButton($caption);
		}

	}

?>
