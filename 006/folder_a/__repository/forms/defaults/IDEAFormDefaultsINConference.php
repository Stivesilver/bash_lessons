<?php

	/**
	 * Class IDEAFormDefaultsINConference
	 * Add default in IN CASE CONFERENCE NOTIFICATION LETTER

	 */
	class IDEAFormDefaultsINConference extends IDEAFormDefaults implements IDEAFormDefaultsInterface {
		/**
		 * Constructor
		 *
		 * @param int $tsRefID
		 */
		public function __construct($tsRefID) {
			parent::__construct($tsRefID);
			$this->init($tsRefID);
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param int $tsRefID
		 * @return IDEAFormDefaultsINConference
		 */
		public static function factory($tsRefID) {
			return new IDEAFormDefaultsINConference($tsRefID);
		}

		/**
		 * Inits default values
		 *
		 * @param int $tsRefID
		 */
		private function init($tsRefID) {
			$dvalues = '';
			$values = db::execSQL("
				SELECT t0.sicprefid, t1.siepcpdesc AS field1,
	                   CASE trim(lower(t1.siepcpdesc))
	                   WHEN trim(lower('Transition Planning')) THEN 'chk_8'
	                   WHEN trim(lower('Annual Case Review')) THEN 'chk_4'
	                   WHEN trim(lower('Review/Revise IEP')) THEN 'chk_3'
	                   WHEN trim(lower('Discuss Educational and/or Speech/Language Evaluation')) THEN 'chk_5'
	                   WHEN trim(lower('Develop Behavior Plan')) THEN 'chk_7'
	                   WHEN trim(lower('Manifestation Determination')) THEN 'chk_2'
	                   WHEN trim(lower('Exit Interview/Graduation')) THEN 'chk_10'
	                   WHEN trim(lower('Other')) THEN 'chk_9'
	                   WHEN trim(lower('Declassification/Reclassification')) THEN 'chk_6'
	                   WHEN trim(lower('Initial Case Conference')) THEN 'chk_1'
	                   END AS field2,
	                   t0.sicpnarrative AS field3
	              FROM webset.std_in_iepconfpurpose AS t0
	                   INNER JOIN webset.statedef_iepconfpurpose AS t1 ON t1.siepcprefid = t0.siepcprefid
	             WHERE t0.stdrefid = " . $tsRefID . "
	             ORDER BY siep_seq, t0.sicprefid
			")->assocAll();
			foreach ($values as $value) {
				$dvalues .= "<value name=\"" . $value['field2'] . "\">on</value>\n";
				if ($value['field3'] != '') {
					$dvalues .= "<value name=\"d4\">" . trim($value['field3']) . "</value>\n";
				}
			}
			$items = db::execSQL("
				SELECT participantname ,
					   participantrole
			      FROM webset.std_iepparticipants
			     WHERE stdRefID = " . $tsRefID . "
                 ORDER BY std_seq_num, participantname"
			)->assocAll();

			$i = 1;
			foreach ($items as $item) {
				$part = trim($item['participantname'] . ", " . $item['participantrole']);
				$dvalues .= "<value name=\"1_$i\">" . $part . "</value>\n";
				$i = $i + 1;
			}
		}
	}

?>
