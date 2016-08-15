<?php

	/**
	 * IDEAStudentIN.php
	 * Include SQL-queries for IN State
	 *
	 * @author Alex Kalevich
	 * Created 23-10-2014
	 */
	class IDEAStudentIN extends IDEAStudent {

		/**
		 * Eligibility
		 *
		 * @var array
		 */
		protected $eligibility;

		/**
		 * LRE Questions
		 *
		 * @var array
		 */
		protected $lreQuestions;

		/**
		 * Post Goals
		 *
		 * @var array
		 */
		protected $postGoals;

		/**
		 * Return data about Eligibility
		 *
		 * @return array
		 */

		/**
		 * Creates an instance of this class
		 *
		 * @param int $tsRefID
		 * @return IDEAStudentIN
		 */
		public static function factory($tsRefID) {
			return new IDEAStudentIN($tsRefID);
		}

		public function getEligibility() {
			$this->eligibility = db::execSQL("
	            SELECT TO_CHAR(edceval, 'MM/DD/YYYY') AS edceval,
	                   TO_CHAR(edccdeval, 'MM/DD/YYYY') AS edccdeval,
	                   TO_CHAR(edneval, 'MM/DD/YYYY') AS edneval,
	                   TO_CHAR(edncdeval, 'MM/DD/YYYY') AS edncdeval,
	                   edesc,
	                   esw,
	                   e504sw,
	                   ecausalrelsw,
					   reevaluation_sw,
					   casual_relation,
					   direct_relation,
					   school_failure
	              FROM webset.std_in_eligibility
	             WHERE stdrefid = " . $this->tsrefid . "
	        ")->assocAll();
			return $this->eligibility;
		}

		/**
		 * Return data about LREQuestions
		 *
		 * @return array
		 */
		public function getLREQuestions() {
			$this->lreQuestions = db::execSQL("
				SELECT t1.silqdesc,
				       CASE t0.silqaanswersw WHEN 'Y' THEN 'Yes' WHEN 'N' THEN 'No' END AS silqaanswersw,
					   qarejectiondesc
              	  FROM webset.std_in_lre_questions_answers AS t0
                  	   INNER JOIN webset.statedef_in_lre_questions AS t1 ON t1.silqrefid = t0.silqrefid
             	  WHERE stdrefid = " . $this->tsrefid . "
               	  	AND (recdeactivationdt IS NULL OR now()< recdeactivationdt)
             	  ORDER BY t1.silqseq
	        ")->assocAll();
			return $this->lreQuestions;
		}

		/**
		 * Return data about LREQuestions
		 *
		 * @return array
		 */
		public function getPostGoals() {
			$this->postGoals = db::execSQL("
				SELECT gdssdesc AS area,
					replace(gspText, 'The student', '" . $this->get('stdfirstname') . "') || ' ' || gdskgaaction || ' ' || gdskgccontent || ' ' || COALESCE(crbasis || ' ', '') || cdesc AS goal,
				       itstrue
				  FROM webset.std_in_postgoals std
				       LEFT OUTER JOIN webset.disdef_bgb_goaldomainscope scope ON scope.gdsrefid = std.scope
				       LEFT OUTER JOIN webset.disdef_bgb_goalsentencepreface preface ON preface.gsfrefid = std.preface
				       LEFT OUTER JOIN webset.disdef_bgb_ksaksgoalactions action ON action.gdskgarefid = std.action
				       LEFT OUTER JOIN webset.disdef_bgb_scpksaksgoalcontent content ON content.gdskgcrefid = std.content
				       LEFT OUTER JOIN webset.disdef_bgb_ksaconditions condition ON condition.crefid = std.condition
				 WHERE stdrefid = " . $this->tsrefid . "
				 ORDER BY gdssdesc, sequence, 2
			")->assocAll();
			return $this->postGoals;
		}
	}
