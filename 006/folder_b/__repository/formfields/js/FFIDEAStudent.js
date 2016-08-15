/**
 * Form Field Student
 *
 * @access public
 * @extends SystemCore
 * @param {string} name
 * @param {string} dskey
 */
	function FFIDEAStudent(name, dskey) {

		// remember reference to this instance
		FFIDEAStudent.instances.push(this);

		/**
		 * Inheriting base class
		 */
		SystemCore.call(this);

		/**
		 * Instance name
		 *
		 * @access public
		 * @var {string}
		 */
		this.name = name;

		/**
		 * Opens student list
		 *
		 * @returns {void}
		 */
		this.openStudentList = function() {
			var wnd = PageAPI.singleton().window.open(
				'Student',
				PageAPI.singleton().url(
					FFIDEAStudent.packPath + '/api/ff_idea_student_list.php',
					{'dskey' : dskey})
			);

			wnd.resize(width, height);
			var inst = this;
			wnd.addEventListener(
				ObjectEvent.SELECT,
				function(e) {
					var selector = $('#' + name);
					selector.val(e.param.stdrefid);
					EventAPI.fireEvent(selector[0], 'change');
					wnd.destroy();
				}
			);
		};

		/**
		 * Changes student name and value in list
		 *
		 * @return void
		 */
		this.clear = function() {
			var selector = $('#' + name);
			selector.val('');
			EventAPI.fireEvent(selector[0], 'change');
		};

		/**
		 * Width window
		 * @param int
		 */
		var width;

		/**
		 * Height window
		 * @param int
		 */
		var height;

		/**
		 * Sets window size
		 *
		 * @param {int} dWidth
		 * @param {int} dHeight
		 * @return void
		 */
		this.setWindowSize = function(dWidth, dHeight) {
			width = dWidth;
			height = dHeight;
		};
	}

	/**
	 * Path to the package
	 *
	 * @param string
	 */
	FFIDEAStudent.packPath = '';

	/**
	 * List of the created instances
	 *
	 * @static
	 * @access private
	 * @var array.<FFIDEAStudent>
	 */
	FFIDEAStudent.instances = [];

	/**
	 * Returns an instance of the class FFIDEAStudent by the specified name
	 *
	 * @static
	 * @access public
	 * @param {string} name
	 * @return FFIDEAStudent
	 */
	FFIDEAStudent.get = function(name) {
		for (var a = 0; a < FFIDEAStudent.instances.length; a++) {
			if (FFIDEAStudent.instances[a].name == name) {
				return FFIDEAStudent.instances[a];
			}
		}
		return null;
	};