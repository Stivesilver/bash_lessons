<?php

	/**
	 * Include SQL-queries for TN State
	 *
	 * @author Alex Kalevich
	 * Created 17-02-2015
	 */
	class IDEAStudentTN extends IDEAStudent {

		/**
		 * Cover Page Data
		 *
		 * @var array
		 */
		protected $coverPage;

		/**
		 * Team Members
		 *
		 * @var array
		 */
		protected $teamMembers;

		/**
		 * Present Levels of Development
		 *
		 * @var array
		 */
		protected $pload;

		/**
		 * Outcomes
		 *
		 * @var array
		 */
		protected $outcomes;

		/**
		 * Review Changes
		 *
		 * @var array
		 */
		protected $reviewChanges;

		/**
		 * Services
		 *
		 * @var array
		 */
		protected $services;

		/**
		 * Justification For Provision
		 *
		 * @var array
		 */
		protected $justification;

		/**
		 * Transition
		 *
		 * @var array
		 */
		protected $transition;

		/**
		 * Conference Notes
		 *
		 * @var array
		 */
		protected $confnotes;

		/**
		 * Outcome Summary
		 *
		 * @var array
		 */
		protected $outcomesumm;

		public function __construct($tsRefID = 0, $stdiepyear = 0) {
			parent::__construct($tsRefID, $stdiepyear);
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param int $tsRefID
		 * @param int $stdiepyear
		 * @return IDEAStudentTN
		 */
		public static function factory($tsRefID, $stdiepyear = 0) {
			return new IDEAStudentTN($tsRefID, $stdiepyear);
		}

		/**
		 * Return Cover Page
		 *
		 * @return array
		 */
		public function getCoverPage() {
			$this->coverPage = $this->execSQL("
				SELECT siepmtdesc,
					   txt02 AS serv_coord,
					   txt03 AS phone_coord,
					   TO_CHAR(dat01, 'MM-DD-YYYY') AS review_due,
					   TO_CHAR(dat02, 'MM-DD-YYYY') AS review_end,
					   TO_CHAR(dat03, 'MM-DD-YYYY') AS annual_due,
					   TO_CHAR(dat04, 'MM-DD-YYYY') AS annual_end,
					   txt04 AS add_review,
					   TO_CHAR(dat05, 'MM-DD-YYYY') AS lea_nottif_due,
					   TO_CHAR(dat06, 'MM-DD-YYYY') AS lea_nottif_end,
					   TO_CHAR(dat07, 'MM-DD-YYYY') AS lea_plann_due,
					   TO_CHAR(dat08, 'MM-DD-YYYY') AS lea_plann_end,
					   TO_CHAR(dat09, 'MM-DD-YYYY') AS lea_trans_due,
					   TO_CHAR(dat10, 'MM-DD-YYYY') AS lea_trans_end,
					   txt05 AS nat_enviroment,
					   txt06 AS follow_place
			  	  FROM webset.std_general AS std
			  	  	   LEFT JOIN webset.statedef_ieptypes AS iept ON(iept.siepmtrefid = std.int01)
				 WHERE area_id = 177
				   AND stdrefid = " . $this->tsrefid . "
			")->fields;

			return $this->coverPage;
		}

		/**
		 * Return team members
		 *
		 * @param bool $withIEP
		 * @return array
		 */
		public function getTeamMembers($withIEP = true) {
			$iep = "";
			if ($withIEP === true) $iep = "AND iep_year = " . $this->stdiepyear . "";

			$this->teamMembers = $this->execSQL("
					SELECT participantname ,
		                   participantrole ,
		                   participantatttype,
		                   TO_CHAR(participantdate, 'MM-DD-YYYY') AS participantdate,
		                   participantagree,
		                   participantcomment,
		                   std_seq_num
		              FROM webset.std_iepparticipants
		             WHERE stdrefid = " . $this->tsrefid . "
		                   $iep
		             ORDER BY std_seq_num, participantname
	            ")->assocAll();

			return $this->teamMembers;
		}

		/**
		 * Return Present Levels of Development
		 *
		 * @return array
		 */
		public function getPLOAD() {
			$this->pload = $this->execSQL("
					SELECT tsn.tsndesc,
						   pglp.strengths,
						   pglp.concerns,
						   pglp.pglpnarrative,
						   TO_CHAR(pglp.pgdate, 'MM-DD-YYYY') AS pgdate
					  FROM webset.std_in_pglp AS pglp
					       LEFT OUTER JOIN webset.disdef_tsn AS tsn ON tsn.tsnrefid = pglp.tsnrefid
					 WHERE pglp.stdrefid = " . $this->tsrefid . "
					   AND pglp.iepyear = " . $this->stdiepyear . "
					 ORDER BY pglp.pglpseq, tsn.tsnnum
	            ")->assocAll();

			return $this->pload;
		}

		/**
		 * Return Review Changes
		 *
		 * @return array
		 */
		public function getReviewChanges() {
				$this->reviewChanges = $this->execSQL("
					SELECT gval.validvalueid AS rew_stat_key,
						   txt02 AS rew_comment,
						   TO_CHAR(dat01, 'MM-DD-YYYY') AS rew_date
				      FROM webset.std_general AS std
				           LEFT JOIN webset.glb_validvalues AS gval ON(std.int01 = gval.refid)
					 WHERE area_id = " . IDEAAppArea::TN_IFSP_OUTCOME_ACTION . "
					   AND stdrefid = " . $this->tsrefid . "
	            ")->assocAll();

			return $this->reviewChanges;
		}

		/**
		 * Return Services
		 *
		 * @return array
		 */
		public function getServices() {

			$this->services = $this->execSQL("
				SELECT stn.stn_refid,
			           CASE SUBSTRING(lower(dn.nsdesc) FROM 'other')
			           WHEN 'other' THEN stn.stn_other
			           ELSE dn.nsdesc || COALESCE('. ' || stn.stn_other, '')
			           END AS nsdesc,

				       stn.stn_provider,
			           TO_CHAR(stn.stn_begdate, 'MM-DD-YYYY') AS stn_begdate,
			           TO_CHAR(stn.stn_enddate, 'MM-DD-YYYY') AS stn_enddate,
				       stn.stn_required_sw,
				       stn.stn_payor,
				       stn.stn_revdate,
				       glb.validvalue AS status,

				       crt.crtdesc,
				       sf.sfdesc,
				       int.validvalue,
		               array_to_string(ARRAY(SELECT bl.order_num || '.' || gl.order_num
									     	   FROM webset.std_tn_ns_goal AS tngl
										    	    INNER JOIN webset.std_bgb_goal AS gl ON (tngl.grefid = gl.grefid)
											 	    INNER JOIN webset.std_bgb_baseline bl ON (gl.blrefid = bl.blrefid)
										  	  WHERE stn_refid = stn.stn_refid), ', ') AS goals
			      FROM webset.std_tn_ns AS stn
			           INNER JOIN webset.disdef_oh_ns AS dn ON dn.refid = stn.serv_refid
			           LEFT JOIN webset.disdef_location AS crt ON crt.crtrefid = stn.crtrefid
			           LEFT JOIN webset.disdef_frequency AS sf ON sf.sfrefid = stn.sfrefid
			           LEFT JOIN webset.disdef_validvalues AS int ON int.refid = stn.int_refid
			           LEFT JOIN webset.glb_validvalues AS glb ON glb.refid = stn.revs_refid
			     WHERE stn.stdrefid = " . $this->tsrefid . "
			       AND stn.iepyear = " . $this->stdiepyear . "
            ")->assocAllKeyed();

			return $this->services;
		}

		/**
		 * Return Justification For Provision
		 *
		 * @return array
		 */
		public function getJustificationForProvision() {
				$this->justification = $this->execSQL("
					SELECT std.refid,
						   std.txt01,
						   std.txt02,
						   std.txt03
					  FROM webset.std_general AS std
					 WHERE std.stdrefid = " . $this->tsrefid . "
					   AND std.area_id = " . IDEAAppArea::TN_IFSP_SEVICES . "
					 ORDER BY std.txt01
	            ")->assocAllKeyed();

			return $this->justification;
		}

		/**
		 * Return Transition
		 *
		 * @return array
		 */
		public function getTransition() {
			$this->transition = $this->execSQL("
					SELECT refid,
						   txt01,
						   txt02,
						   txt03,
						   TO_CHAR(dat01, 'MM-DD-YYYY') AS dat01
					  FROM webset.std_general
					 WHERE stdrefid = " . $this->tsrefid . "
					   AND iepyear = " .$this->stdiepyear  . "
				       AND area_id = " . IDEAAppArea::TN_IFSP_TRANSITION_FORMC . "
					 ORDER BY txt01
	            ")->assocAll();

			return $this->transition;
		}

		/**
		 * Return Conference Notes
		 *
		 * @return array
		 */
		public function getConferenceNotes() {
			$this->confnotes = $this->execSQL("
					SELECT cntext
					  FROM webset.std_casenotes std
					 WHERE stdrefid = " . $this->tsrefid . "
					 ORDER BY eventdt, cnsdesc
	            ")->assocAll();

			return $this->confnotes;
		}

		/**
		 * Return Outcome Summary
		 *
		 * @return array
		 */
		public function getOutcomeSummary() {

			$goals = $this->getBgbGoals();

			$this->outcomesumm = array();
			foreach ($goals as $goal) {
				$this->outcomesumm[$goal['grefid']] = array(
					'grefid' => $goal['grefid'],
					'gsentance' => $goal['g_num'] . ' ' . $goal['gsentance']
				);
			}

			return $this->outcomesumm;
		}

	}
