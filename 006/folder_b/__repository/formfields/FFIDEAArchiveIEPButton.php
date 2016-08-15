<?php

	/**
	 * Creates button for Export Popup
	 *
	 * @copyright Lumen Touch, 2014
	 * @author Alex Kalevich
	 */
	class FFIDEAArchiveIEPButton extends FFButton {

		/**
		 * Set User ID
		 * Example: umrefid
		 *
		 * @var array
		 */
		protected $umrefid;

		/**
		 * Set Archive Access
		 * Example: archAccess
		 *
		 * @var array
		 */
		protected $archAccess = true;

		/**
		 * Class Constructor

		 */
		public function __construct($caption) {
			# call parent constructor
			parent::__construct();
			$this->value($caption);
		}

		/**
		 * Check Archive Access
		 */
		public function checkAccess() {
			if(SystemCore::$isAdmin == false) {
				$is_cm = $this->execSQL("
					SELECT 1
					FROM webset.sys_casemanagermst
					WHERE umrefid = UMREFID
					")->getOne();
				if ($is_cm) {
					if (IDEACore::disParam(12) == 'N') {
						$this->archAccess = false;
					}
				}
				$is_pc = $this->execSQL("
					SELECT 1
					FROM webset.sys_proccoordmst
					WHERE umrefid = UMREFID
					")->getOne();
				if ($is_pc) {
					if (IDEACore::disParam(13) == 'N') {
						$this->archAccess = false;
					} else {
						$this->archAccess = true;
					}
				} 
				# If user is not Lumen Admin and 147 param is ON - NO ACCESS
				# This param overwrites previous ones
				if (IDEACore::disParam(147) == 'Y') {
					$this->archAccess = false;
				}
			}
		}

		/**
		 * Returns HTML code of the element
		 *
		 * @param DBConnection $db
		 * @throws Exception
		 * @return string
		 */
		public function toHTML($db = null) {
			$this->width(120)
				->disabled(true)
				->checkAccess();
			if ($this->archAccess == false) {
				$this->onClick("PageAPI.singleton().alert('User Group does not have rights to Archive. Contact District Admin.')", true);
			}
			return parent::toHTML();
		}

		/**
		 * Sets User ID
		 *
		 * @param string $umrefid
		 * @throws Exception
		 * @return FFIDEAArchiveIEPButton
		 */
		public function setUserID($umrefid = '') {
			if ($umrefid == '') throw new Exception('Please set User ID.');
			$this->umrefid = $umrefid;
			return $this;
		}

		/**
		 * Creates an instance of this class
		 *
		 * @static
		 * @param string $caption
		 * @return FFIDEAExportButton
		 */
		public static function factory($caption = 'Archive') {
			return new FFIDEAArchiveIEPButton($caption);
		}

	}

?>
