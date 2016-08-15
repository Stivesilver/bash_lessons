<?php

	/**
	 * Class IDEAFormDefaultsIDInvToMeetEvl
	 * Add default in 370 - Invitation to Meeting Evaluation

	 */
	class IDEAFormDefaultsIDInvToMeetEvl extends IDEAFormDefaults implements IDEAFormDefaultsInterface {
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
		 * @return IDEAFormDefaultsIDInvToMeetEvl
		 */
		public static function factory($tsRefID) {
			return new IDEAFormDefaultsIDInvToMeetEvl($tsRefID);
		}

		/**
		 * Inits default values
		 *
		 * @param int $tsRefID
		 */
		private function init($tsRefID) {
			$parts = db::execSQL("
				SELECT part_name,
		               part_role
		          FROM webset.es_std_red_part
		         WHERE stdrefid=" . $tsRefID . "
		         ORDER BY seq, refid
			")->assocAll();
			$i = 1;
			foreach ($parts as $part) {
				$this->values['1_' . $i] = $part['part_name'];
				$this->values['1_' . $i+1] = $part['part_role'];
				$i = $i+2;
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
