<?php

	/**
	* Class Choose Yes/No
	*
	* @copyright 2015, LumenTouch
	* @author Alex Kalevich
	*/
	class FFIDEASwitchYN extends FFCheckBoxList {

		/**
		* Class Constructor
		*
		* @param string $caption
		*/
		public function __construct($caption = '') {

			parent::__construct($caption);
			$this->displaySelectAllButton(false);
			$this->triggerChange(true);
			$this->onChange('
			var vals = $(this).val().split(",");
			if (vals[0] === "" || vals.length == 1) {
				this.__lastValue = vals[0];
				return;
			}
			if (this.__lastValue === undefined) {
				// first call
				vals.splice(1);
			} else if (this.__lastValue == vals[0]) {
				vals.splice(0, 1);
			} else {
				vals.splice(1);
			}
			this.__lastValue = vals[0];
			$(this).val(this.__lastValue).change();
		');

			# fill the data
			$this->data(
				array(
					array('Y', 'Yes'),
					array('N', 'No')
				)
			);
		}

		/**
		 * Set Data
		 *
		 * @param $array
		 * @throws Exception
		 */
		public function setData($array) {
			$this->data($array);
		}

		/**
		* Creates an instance of this class
		*
		* @static
		* @param string $caption
		* @return FFIDEASwitchYN
		*/
		public static function factory($caption = '') {
			return new FFIDEASwitchYN($caption);
		}

	}
?>
