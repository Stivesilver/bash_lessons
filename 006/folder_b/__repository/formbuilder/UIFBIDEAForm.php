<?php

	/**
	 * IDEA FB Form
	 *
	 * @author Alex Kalevich
	 * @copyright Lumen Touch, 2015
	 */
	class UIFBIDEAForm extends UIFBDocument {

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
		 * @param int $type
		 * @throws Exception
		 */
		public function __construct($f_refid, $type) {
			parent::__construct();
			$this->f_refid = (int)$f_refid;

			$info = $this->execSQL("
				SELECT CASE WHEN df.dfrefid IS NOT NULL THEN df.dfrefid ELSE sf.mfcrefid END AS tprefid,
					   stdrefid,
					   title,
					   CASE WHEN df.dfrefid IS NOT NULL THEN df.fb_content ELSE sf.fb_content END AS fb_content
				  FROM webset.std_forms AS std
				       LEFT JOIN webset.disdef_forms AS df ON (std.dfrefid = df.dfrefid)
				       LEFT JOIN webset.statedef_forms AS sf ON (std.mfcrefid = sf.mfcrefid)
				 WHERE smfcrefid = " . $this->f_refid . "
			")->assoc();

			$this->t_refid = $info['tprefid'];

			if (!$info) {
				throw new Exception('The Submission was not found.');
			}

			if ($info) {
				$doc = FBDocument::factory($info['fb_content']);
				$this->setDocument($doc);
				$doc->setInitialValues(array('stdrefid' => $info['stdrefid']));
				$doc->setSettings(FBIDEASettings::factory());
				$this->setDocumentTitle($info['title']);
			}
		}

		/**
		 * Creates and returns instance of this class
		 *
		 * @param int $f_refid Form ID
		 * @param int $type Form
		 * @return UIFBIDEAForm
		 */
		public static function factory($f_refid, $type = 1) {
			return new UIFBIDEAForm($f_refid, $type);
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
				SELECT fb_content
				  FROM webset.std_forms
				 WHERE smfcrefid = " . $this->f_refid . "
			")->getOne();
		}

		/**
		 * Saves form values
		 *
		 * @param string $data
		 * @return void
		 */
		protected function saveValues($data) {
			$this->f_refid = DBImportRecord::factory('webset.std_forms', 'smfcrefid')
				->key('smfcrefid', $this->f_refid)
				->set('fb_content', $data)
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', date('Y-m-d H:i:s'))
				->import()
				->recordID();
		}
	}

?>
