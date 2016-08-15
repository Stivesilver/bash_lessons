	/**
	 * Form Field Student
	 *
	 * @access public
	 * @extends SystemCore
	 * @param {string} name
	 * @param {string} dskey
	 */
	function FFIDEAActionButton(name, dskey) {

		// remember reference to this instance
		FFIDEAActionButton.instances.push(this);

		/**
		 * Instance name
		 *
		 * @access public
		 * @var {string}
		 */
		this.name = name;

		/**
		 * Adds new item to work area
		 *
		 * @param string protoName
		 * @return void
		 */
		this.reorder = function () {
			var vars = {dskey: dskey};
			PageAPI.singleton().ajax.process(
				UIProcessBoxType.DATA_UPDATE,
				PageAPI.singleton().url(FFIDEAActionButton.packPath + './api/reorder.ajax.php'),
				vars,
				true
			).addEventListener(
				ObjectEvent.COMPLETE,
				function (e) {
					PageAPI.singleton().reload();
				}
			);
		}
	}

	/**
	 * Path to the package
	 *
	 * @param string
	 */
	FFIDEAActionButton.packPath = '';

	/**
	 * List of the created instances
	 *
	 * @static
	 * @access private
	 * @var array.<FFIDEAStudent>
	 */
	FFIDEAActionButton.instances = [];

	/**
	 * Returns an instance of the class FFIDEAStudent by the specified name
	 *
	 * @static
	 * @access public
	 * @param {string} name
	 * @return FFIDEAActionButton
	 */
	FFIDEAActionButton.get = function (name) {
		for (var a = 0; a < FFIDEAActionButton.instances.length; a++) {
			if (FFIDEAActionButton.instances[a].name == name) {
				return FFIDEAActionButton.instances[a];
			}
		}
		return null;
	};
