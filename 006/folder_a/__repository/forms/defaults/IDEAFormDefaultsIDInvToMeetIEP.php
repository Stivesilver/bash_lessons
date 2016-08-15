<?php

	/**
	 * Class IDEAFormDefaultsIDInvToMeetIEP
	 * Add default in 370 - Invitation to Meeting IEP

	 */
	class IDEAFormDefaultsIDInvToMeetIEP extends IDEAFormDefaults implements IDEAFormDefaultsInterface {
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
		 * @return IDEAFormDefaultsIDInvToMeetIEP
		 */
		public static function factory($tsRefID) {
			return new IDEAFormDefaultsIDInvToMeetIEP($tsRefID);
		}

		/**
		 * Inits default values
		 *
		 * @param int $tsRefID
		 */
		private function init($tsRefID) {
			$parts = db::execSQL("
				SELECT participantname ,
		               participantrole
		          FROM webset.std_iepparticipants
		         WHERE stdrefid = " . $tsRefID . "
		           AND COALESCE(docarea, 'I') = 'I'
		         ORDER BY CASE WHEN substring(participantrole,1,1)='*' THEN 1 ELSE 2 END, std_seq_num, participantname
			")->assocAll();
			$i = 1;
			foreach ($parts as $part) {
				$this->values['1_' . $i] = $part['participantname'];
				$this->values['1_' . $i + 1] = $part['participantrole'];
				$i = $i + 2;
			}
			$name = db::execSQL("
				SELECT coalesce(trim(gdFNm),'') || ' ' || coalesce(trim(gdLNm),'') || ' and ' || stdfnm || ' ' || stdlnm as allname
			      FROM webset.dmg_guardianmst grd
	                   INNER JOIN webset.def_guardiantype gtype ON grd.gdType = gtype.gtRefID
	                   INNER JOIN webset.sys_teacherstudentassignment ts ON grd.stdrefid = ts.stdrefid
	                   INNER JOIN webset.vw_dmg_studentmst AS std ON ts.stdrefid = std.stdrefid
	             WHERE tsrefid = " . $tsRefID . "
	             ORDER BY seqnumber, gtrank, UPPER(gdLNm), UPPER(gdFNm)
              ")->getOne();
			$this->values['ParentName'] = $name;
		}
	}

?>
