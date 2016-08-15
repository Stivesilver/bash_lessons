<?php

	/**
	 * 504 FB Form
	 *
	 * @author Alex Kalevich
	 * @copyright Lumen Touch, 2015
	 */
	class UIFB504Form extends UIFBDocument {

		/**
		 * Form ID
		 *
		 * @var int
		 */
		private $f_refid = 0;

		/**
		 * Form Template ID
		 *
		 * @var int
		 */
		private $t_refid = 0;

		/**
		 * Class Constructor
		 *
		 * @param int $f_refid
		 * @throws Exception
		 */
		public function __construct($f_refid) {
			parent::__construct();
			$this->f_refid = (int)$f_refid;
			$info = $this->execSQL("
				SELECT f.frefid,
					   s.hisrefid,
					   f.fname,
					   f.fb_content,
					   h.stdrefid
				  FROM webset.std_fif_forms s
                       INNER JOIN webset.disdef_fif_forms f ON (f.frefid = s.frefid)
                       INNER JOIN webset.std_fif_history as h ON (h.hisrefid = s.hisrefid)
				 WHERE sfrefid = " . $this->f_refid . "
			")->assoc();

			$this->t_refid = $info['frefid'];

			if (!$info) {
				throw new Exception('The Submission was not found.');
			}

			if ($info) {
				$doc = FBDocument::factory($info['fb_content']);
				$this->setDocument($doc);
				$doc->setInitialValues(array('stdrefid' => $info['stdrefid']));
				$doc->setSettings(FB504Settings::factory());
				$this->setDocumentTitle($info['fname']);
			}
		}

		/**
		 * Creates and returns instance of this class
		 *
		 * @param int $f_refid Form ID
		 * @return UIFB504Form
		 *
		 */
		public static function factory($f_refid) {
			return new UIFB504Form($f_refid);
		}

		/**
		 * Returns Submission ID
		 *
		 * @return int
		 */
		public function getFormID() {
			return $this->f_refid;
		}

		/**
		 * Loads form values
		 *
		 * @return array|string|null
		 */
		protected function loadValues() {
			return $this->execSQL("
				SELECT values_content
				  FROM webset.std_fif_forms
				 WHERE sfrefid = " . $this->f_refid . "
			")->getOne();
		}

		/**
		 * Saves form values
		 *
		 * @param string $data
		 * @return void
		 */
		protected function saveValues($data) {
			$this->f_refid = DBImportRecord::factory('webset.std_fif_forms', 'sfrefid')
				->key('sfrefid', $this->f_refid)
				->set('values_content', $data)
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', date('Y-m-d H:i:s'))
				->import()
				->recordID();
		}
	}

?>
