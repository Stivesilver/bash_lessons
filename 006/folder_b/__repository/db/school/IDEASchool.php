<?php

	/**
	 * Contains basic school data
	 *
	 * @copyright Lumen Touch, 2012
	 */
	class IDEASchool extends RegularClass {

		/**
		 * District ID
		 * DB Table: public.sys_vndmst
		 *
		 * @var int
		 */
		protected $vourefid;

		/**
		 * Initializes basic properties
		 *
		 * @param int $tsRefID
		 */
		public function __construct($vourefid = null) {
			if ($vourefid == null) {
				throw new Exception('School ID has not been specified.');
			}
			$this->vourefid = $vourefid;
		}

		/**
		 * Returns Marking Periods
		 *
		 * @return array
		 */
		public function getMarkingPeriods($begdate = '1900-01-01', $enddate = '3000-01-01', $esy = 'N') {

			$mperiods = $this->execSQL("
				SELECT mrk.*, dsydesc
				  FROM webset.sch_markperiod mrk
				       INNER JOIN webset.disdef_schoolyear dsy ON dsy.dsyrefid = mrk.dsyrefid
				 WHERE vourefid = " . $this->vourefid . "
				 ORDER BY dsybgdt, bmbgdt1
			")->assocAll();

			$periods = array();

			$p = 1;
			for ($i = 0; $i < count($mperiods); $i++) {
				$I = 1;
				while ($I < 21) {
					if ($mperiods[$i]["esy" . $I] == $esy && !(($mperiods[$i]["bmbgdt" . $I] < $begdate && $mperiods[$i]["bmendt" . $I] < $begdate) || ($mperiods[$i]["bmbgdt" . $I] > $enddate && $mperiods[$i]["bmendt" . $I] > $enddate))) {
						$periods[$p]["bm"] = $mperiods[$i]["bm" . $I];
						$periods[$p]["bmnum"] = $I;
						$bgdt = $mperiods[$i]["bmbgdt" . $I];
						$endt = $mperiods[$i]["bmendt" . $I];
						$periods[$p]["bmbgdt"] = substr($bgdt, 5, 2) . "/" . substr($bgdt, 8, 2) . "/" . substr($bgdt, 0, 4);
						$periods[$p]["bmendt"] = substr($endt, 5, 2) . "/" . substr($endt, 8, 2) . "/" . substr($endt, 0, 4);
						$periods[$p]["dsyrefid"] = $mperiods[$i]["dsyrefid"];
						$periods[$p]["dsydesc"] = $mperiods[$i]["dsydesc"];
						$p++;
					}
					$I++;
					if ($I == 21 or $mperiods[$i]["bm" . $I] == "") {
						break;
					}
				}
			}

			return $periods;
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param int $tsRefID
		 * @return IDEASchool
		 */
		public static function factory($vourefid = null) {
			return new IDEASchool($vourefid);
		}

	}

?>
