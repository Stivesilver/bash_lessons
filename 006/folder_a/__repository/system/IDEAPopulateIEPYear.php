<?php

	/**
	 * Class IDEAPopulateIEPYear
	 *
	 * @author
	 * @copyright LumenTouch, 2014
	 */
	class IDEAPopulateIEPYear {

		/**
		 * Class Constructor
		 *
		 * @return IDEAPopulateIEPYear
		 */

		/**
		 * @var $dskey
		 */
		private $dskey;

		/**
		 * @var $area
		 */
		private $area;

		/**
		 * @var $path
		 */
		private $path;

		public function __construct($dskey, $area = null, $path) {
			$this->dskey = $dskey;
			$this->area = $area;
			$this->path = $path;
		}

		/**
		 * Creates and returns an instance of this class.
		 *
		 * @param string $dskey
		 * @param string $area
		 * @param $path
		 * @return IDEAPopulateIEPYear
		 */
		public static function factory($dskey, $area = null, $path) {
			return new IDEAPopulateIEPYear($dskey, $area, $path);
		}

		/**
		 * @return mixed
		 */
		public function getPopulateButton() {
			$button = FFMenuButton::factory('Populate')
				->leftIcon('wizard2_16.png')
				->addItem('Previous IEP Year',
					"api.desktop.open(
					'Populate Records',
					api.url(api.virtualRoot + '/apps/idea/__repository/system/api/populate.php', {'path' : '" . $this->path . "', 'dskey': " . json_encode($this->dskey) . ", 'area': " . json_encode($this->area) . "})
					).addEventListener(
						ObjectEvent.COMPLETE,
						function (e) {
							api.reload();
						}
					);",
					'wizard2_16.png');
			return $button;
		}
	}
