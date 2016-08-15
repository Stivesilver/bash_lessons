<?php

	/**
	 * IDEA Student.
	 * The input element with dropdown list of matched student
	 *
	 * @copyright Lumen Touch, 2014
	 * @author Oleg Bychkovski
	 */
	class FFIDEAStudent extends FFInputDropList {

		/**
		 * Class Constructor
		 * The 2nd argument is a constant of the FFIDEAStudent class
		 *
		 * @param int|string $saveType
		 */
		public function __construct($saveType = FFInputDropList::SAVE_ID) {
			parent::__construct('Student', $saveType);

			$this->searchMethod(FormFieldMatch::SUBSTRING);
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param int|string $saveType
		 * @return FFIDEAStudent
		 */
		public static function factory($saveType = FFInputDropList::SAVE_ID) {
			return new FFIDEAStudent($saveType);
		}

		/**
		 * Condition
		 *
		 * @var array
		 */
		protected $condition = '';

		/**
		 * Set condition for SQL.
		 *
		 * @param string $condition
		 * @return FFIDEAStudent
		 */
		public function setCondition($condition) {
			$this->condition = $condition;
			return $this;
		}

		/**
		 * Width search window
		 *
		 * @var int
		 */
		protected $windowWidth = 800;

		/**
		 * Height search window
		 *
		 * @var int
		 */
		protected $windowHeight = 600;

		/**
		 * Sets search window size
		 *
		 * @param int $width
		 * @param int $height
		 * @return FFIDEAStudent
		 */
		public function setWindowSize($width = null, $height = null) {
			if ($width === null) {
				$this->windowWidth = 800;
			} else {
				$this->windowWidth = (int)$width;
				if ($this->windowWidth < 1) $this->windowWidth = 800;
			}

			if ($height === null) {
				$this->windowHeight = 400;
			} else {
				$this->windowHeight = (int)$height;
				if ($this->windowHeight < 1) $this->windowHeight = 400;
			}
			return $this;
		}

		/**
		 * Returns HTML code of the element
		 *
		 * @param DBConnection $db
		 * @return string
		 */
		public function toHTML($db = null) {

			FileUtils::getJSFile('./js/FFIDEAStudent.js')
				->append();

			$this->dropListSQL(IDEAListParts::createSearchSql('admin', 'std.stdrefid', $this->condition));

			$this->append(
				UICustomHTML::factory(
					FFButton::factory('','FFIDEAStudent.get(' . json_encode($this->id) . ').openStudentList()')
						->leftIcon('search_file.png')
						->disabled($this->disabled)
				)
			);

			$this->append(
				UICustomHTML::factory(
					FFButton::factory('', 'FFIDEAStudent.get(' . json_encode($this->id) . ').clear();')
						->leftIcon('clear.png')
						->disabled($this->disabled)
				)
			);
/*
			$this->append(UICustomHTML::factory(FFEmpty::factory('')
				->htmlWrap('')
				->name($this->id . '_name'))
				->css('font-weight', 'bold'));
*/
			$ds = new DataStorage();
			$ds->set('condition', $this->condition);

			$js =
				$this->js('
					FFIDEAStudent.packPath = ' . json_encode(CoreUtils::getVirtualPath('./')) . ';
					with (new FFIDEAStudent(' . json_encode($this->id) . ', ' . json_encode($ds->getKey()) . ')) {
						setWindowSize(' . json_encode($this->windowWidth) . ', ' . json_encode($this->windowHeight) . ');
					}
				');

			return parent::toHTML($db) . $js;
		}


	}