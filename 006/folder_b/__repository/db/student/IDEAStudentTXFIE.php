<?php

/**
 * Contains basic student FIE data specific for state of TX
 *
 * @author Nick Ignatushko <nick@lumentouch.com>
 * @copyright Lumen Touch, 2013.
 */
class IDEAStudentTXFIE extends IDEAStudentTX {

	/**
	 * Sp Ed Enrollment Student Lumen ID
	 * DB Table: webset.sys_teacherstudentassignment
	 *
	 * @var array
	 */
	protected $languages;

	/**
	 * Info about student english level
	 *
	 * @var array
	 */
	protected $englishKnowledge;

	/**
	 * Information about vision\health\other factors student. Use in different blocks
	 *
	 * @var array
	 */
	protected $visionHealthFactors;

	/**
	 * Data for table Intellectual Functioning in VIII. Intelligence and Adaptive Behavior block
	 *
	 * @var array
	 */
	protected $intelligence;

	/**
	 * Information about communication disorder, such as stuttering, impaired articulation,
	 * a language impairment, or a voice impairment.
	 *
	 * @var array
	 */
	protected $speechSummary;

	/**
	 * Information about student speech informal
	 *
	 * @var array
	 */
	protected $speechInformal;

	/**
	 * Information about student speech articulation
	 *
	 * @var array
	 */
	protected $speechArticulation;

	/**
	 * Information about student speech fluency
	 *
	 * @var array
	 */
	protected $speechFluency;

	/**
	 * Information about student voice parameters
	 *
	 * @var array
	 */
	protected $speechVoice;

	/**
	 * Student summary of evaluation
	 *
	 * @var array
	 */
	protected $speechSummOfEval;

	/**
	 * Student recommendation
	 *
	 * @var array
	 */
	protected $speechRecommendation;

	/**
	 * Get data for Language/Communicative Status
	 *
	 * @param integer $apptype
	 * @return array
	 */
	public function getSourcesData($apptype) {
		$SQL = "
			SELECT COALESCE(hspdesc,'') || ' ' || COALESCE(s_src,'') AS dataSource,
                   to_char(s_date, 'mm-dd-yyyy') AS dateSource
              FROM webset_tx.std_fie_social std
                   LEFT OUTER JOIN webset.es_scr_disdef_proc asm ON asm.hsprefid = std.hsprefid
             WHERE stdrefid = " . $this->tsrefid . "
               AND iepyear = " . $this->stdiepyear . "
               AND apptype = '$apptype'
             ORDER BY refid desc
        ";

		$result = db::execSQL($SQL)->assocAll();

		return $result;
	}

	/**
	 * Creates an instance of this class
	 *
	 * @param int $tsRefID
	 * @param int $stdiepyear
	 * @return IDEAStudentTXFIE
	 */
	public static function factory($tsRefID, $stdiepyear = 0) {
		return new IDEAStudentTXFIE($tsRefID, $stdiepyear);
	}

	/**
	 * Set student properties about english knowledge.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function englishKnowledge($key) {
		if (!isset($this->englishKnowledge)) {
			$SQL = "
				SELECT dominant,
		               dominant_oth,
		               express_best,
		               express_oth,
		               eng_lep_rec,
		               eng_lep_exp,
		               oth_lep_lng,
		               oth_lep_rec,
		               oth_lep_exp,
		               text_devider,
		               lpac_test,
		               lpac_score,
		               limited_prof,
		               limited_recomm,
		               conducted,
		               conducted_text,
		               findings
		          FROM webset_tx.std_fie_language
		         WHERE stdrefid = " . $this->tsrefid . "
			       AND iepyear = " . $this->stdiepyear . "
	        ";

			$this->englishKnowledge = db::execSQL($SQL)->assoc();
		}

		return $this->englishKnowledge[$key];
	}

	/**
	 * Get student information about physical strength
	 *
	 * @param int $area
	 * @return array
	 */
	public function strength($area) {
		$SQL = "
			SELECT strength,
                   weakness
              FROM webset_tx.std_fie_academic
             WHERE stdrefid = " . $this->tsrefid . "
			       AND iepyear = " . $this->stdiepyear . "
               AND a_refid = $area
             ORDER BY refid";

		return db::execSQL($SQL)->assocAll();
	}

	/**
	 * Get data about student for blocks: Vision/Hearing, Health, Other Factors etc.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function visionData($key) {
		if (!isset($this->visionHealthFactors)) {
			$SQL = "
			SELECT visionok,
                   vision_glass,
                   hearingok,
                   hearing_aid,
                   health_history,
                   health_history_text,
                   health_condition,
                   health_condition_text,
                   health_adaptive,
                   health_adaptive_text,
                   factors_oth_ch,
                   factors_oth,
                   assur_copy,
                   to_char(assur_copy_dt, 'mm-dd-yyyy') as assur_copy_dt
              FROM webset_tx.std_fie_general
             WHERE stdrefid = " . $this->tsrefid . "
		       AND iepyear = " . $this->stdiepyear . "
            ";

			$this->visionHealthFactors = db::execSQL($SQL)->assoc();
		}

		return $this->visionHealthFactors[$key];
	}

	/**
	 * Get information about cultural, linguistic skills student
	 *
	 * @return array
	 */
	public function cultural() {
		$SQL = "
			SELECT b_name,
	               stdrefid
	          FROM webset_tx.def_fie_bground def
	          LEFT OUTER JOIN webset_tx.std_fie_bground std
	            ON std.b_refid = def.b_refid
	         WHERE stdrefid = " . $this->tsrefid . "
			   AND iepyear = " . $this->stdiepyear . "
	         ORDER BY b_seq, b_name
            ";

		return db::execSQL($SQL)->assocAll();
	}

	/**
	 * Get data for table Intellectual Functioning in VIII. Intelligence and Adaptive Behavior block
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function intelligence($key)	{
		if (!isset($this->intelligence)) {
			$SQL = "
				SELECT *
	              FROM webset_tx.std_fie_adaptive
	             WHERE stdrefid = " . $this->tsrefid . "
				   AND iepyear = " . $this->stdiepyear . "
	            ";

			$this->intelligence = db::execSQL($SQL)->assoc();
		}

		return $this->intelligence[$key];
	}

	/**
	 * Get adaptive scores for table Adaptive Behavior in block VIII. Intelligence and Adaptive Behavior
	 *
	 * @return array
	 */
	public function adaptiveScores() {
		$SQL = "
			SELECT validvalue,
                   score
              FROM webset_tx.std_fie_adaptivescore
                   INNER JOIN webset.glb_validvalues ON area_id = refid
             WHERE stdrefid = " . $this->tsrefid . "
			   AND iepyear = " . $this->stdiepyear . "
             ORDER BY sequence_number
            ";

		return db::execSQL($SQL)->assocAll();
	}

	/**
	 * Return titles for checkboxes in block VIII. Intelligence and Adaptive Behavior
	 *
	 * @return array
	 */
	public static function adaptiveAreas()	{
		$SQL = "
			SELECT refid,
                   validvalue
              FROM webset.glb_validvalues
             WHERE valuename = 'TX_FIE_Adaptives'
               AND (glb_enddate IS NULL or now()< glb_enddate)
             ORDER BY sequence_number, validvalue
            ";

		return db::execSQL($SQL)->assocAll();
	}

	/**
	 * Get titles and values for checkboxes in block Recommendations
	 *
	 * @param int $part
	 * @return array
	 */
	public function recomendations($part)	{
		if ($part == 1) {
			$WHERE = "WHERE r_seq<125";
		} else {
			$WHERE = "WHERE r_seq>125";
		}

		$SQL = "
			SELECT r_name,
                   stdrefid
              FROM webset_tx.def_fie_recommendation def
              LEFT OUTER JOIN webset_tx.std_fie_recommendation std
                     ON std.r_refid = def.r_refid
               AND stdrefid = " . $this->tsrefid . "
			   AND iepyear = " . $this->stdiepyear . "
                   $WHERE
             ORDER BY r_seq, r_name
            ";

		return db::execSQL($SQL)->assocAll();
	}

	/**
	 * Get data for block Assurances
	 *
	 * @return array
	 */
	public function participants()	{
		$SQL = "
			SELECT name,
                   position
			  FROM webset_tx.std_fie_assurance
             WHERE stdrefid = " . $this->tsrefid . "
			   AND iepyear = " . $this->stdiepyear . "
             ORDER BY refid
            ";

		return db::execSQL($SQL)->assocAll();
	}

	/**
	 * Get data for table PROFESSIONAL EVALUATOR in block Speech Impairment
	 *
	 * @return array
	 */
	public function speechHearing()	{
		$SQL = "
			SELECT s_src,
                   to_char(s_date, 'mm-dd-yyyy') AS date,
                   CASE report WHEN 'Y' THEN 'Yes' END AS report_yes,
                   CASE report WHEN 'N' THEN 'No' END AS report_no
                   FROM webset_tx.std_speech_adata
             WHERE stdrefid = " . $this->tsrefid . "
			   AND iepyear = " . $this->stdiepyear . "
             ORDER BY refid
            ";

		return db::execSQL($SQL)->assocAll();
	}

	/**
	 * Return information about communication disorder student by key
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function speechSummary($key)	{
		if (!isset($this->speechSummary)) {
			$SQL = "
				SELECT disorder,
	                   rfr ,
	                   eduhistory,
	                   observations,
	                   oral
	              FROM webset_tx.std_speech_general
	             WHERE stdrefid = " . $this->tsrefid . "
			       AND iepyear = " . $this->stdiepyear . "
	            ";

			$this->speechSummary = db::execSQL($SQL)->assoc();
		}

		return $this->speechSummary[$key];
	}

	/**
	 * Return data about SPEECH/LANGUAGE TEST RESULTS AND INTERPRETATION OF RESULTS
	 *
	 * @return array
	 */
	public function speechLanguageTests() {
		$SQL = "
			SELECT test,
                   score,
                   rank
              FROM webset_tx.std_speech_lang_scores
             WHERE stdrefid = " . $this->tsrefid . "
		       AND iepyear = " . $this->stdiepyear . "
             ORDER BY refid
            ";

		return db::execSQL($SQL)->assocAll();
	}

	/**
	 * Return speech Informal about Student by value
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function speechInformal($key) {
		if(!isset($this->speechInformal)) {
			$SQL = "
				SELECT informal,
	                   syn_cognitive,
	                   syn_relative,
	                   syn_relative_txt,
	                   syn_significant,
	                   syn_significant_txt,
	                   sem_cognitive,
	                   sem_relative,
	                   sem_relative_txt,
	                   sem_significant,
	                   sem_significant_txt,
	                   prag_cognitive,
	                   prag_relative,
	                   prag_relative_txt,
	                   prag_significant,
	                   prag_significant_txt,
	                   met_cognitive,
	                   met_relative,
	                   met_relative_txt,
	                   met_significant,
	                   met_significant_txt,
	                   comments
	              FROM webset_tx.std_speech_informal
	             WHERE stdrefid = " . $this->tsrefid . "
			       AND iepyear = " . $this->stdiepyear . "
	            ";

			$this->speechInformal = db::execSQL($SQL)->assoc();
		}

		return $this->speechInformal[$key];
	}

	/**
	 * Return speech articulation about Student by value
	 *
	 * @param string $key
	 */
	public function speechArticulation($key) {
		if (!isset($this->speechArticulation)) {
			$SQL = "
				SELECT formal,
	                   percentile,
	                   informal,
	                   phonemes,
	                   phonological,
	                   stimulable,
	                   stim_cognitive,
	                   stim_relative,
	                   stim_relative_txt,
	                   stim_significant,
	                   stim_significant_txt,
	                   comments
	              FROM webset_tx.std_speech_articulation
	             WHERE stdrefid = " . $this->tsrefid . "
			       AND iepyear = " . $this->stdiepyear . "
				";

			$this->speechArticulation = db::execSQL($SQL)->assoc();
		}

		return $this->speechArticulation[$key];
	}

	/**
	 * Return speech fluency about Student by value
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function speechFluency($key) {
		if (!isset($this->speechFluency)) {
			$SQL = "
				SELECT formal,
                       informal,
                       stim_cognitive,
                       stim_relative,
                       stim_relative_txt,
                       stim_significant,
                       stim_significant_txt,
                       comments
                  FROM webset_tx.std_speech_fluency
                 WHERE stdrefid = " . $this->tsrefid . "
			       AND iepyear = " . $this->stdiepyear . "
				";

			$this->speechFluency = db::execSQL($SQL)->assoc();
		}

		return $this->speechFluency[$key];
	}

	/**
	 * Return voice parameter about Student by value
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function speechVoice($key) {
		if (!isset($this->speechVoice)) {
			$SQL = "
				SELECT informal,
	                   stim_cognitive,
	                   stim_relative,
	                   stim_relative_txt,
	                   stim_significant,
	                   stim_significant_txt,
	                   comments
	              FROM webset_tx.std_speech_voice
	             WHERE stdrefid = " . $this->tsrefid . "
			       AND iepyear = " . $this->stdiepyear . "
				";

			$this->speechVoice = db::execSQL($SQL)->assoc();
		}

		return $this->speechVoice[$key];
	}

	/**
	 * Get value by key about Summary of Evaluation Student
	 *
	 * @param string $key
	 */
	public function speechSummOfEval($key) {
		if (!isset($this->speechSummOfEval)) {
			$SQL = "
				SELECT disorder_sw,
	                   disorder_txt,
	                   adverse_sw,
	                   adverse_txt,
	                   pathology_sw,
	                   pathology_txt,
	                   criteria_sw,
	                   criteria_txt,
	                   eval_summary
	              FROM webset_tx.std_speech_summary
	             WHERE stdrefid = " . $this->tsrefid . "
			       AND iepyear = " . $this->stdiepyear . "
				";

			$this->speechSummOfEval = db::execSQL($SQL)->assoc();
		}

		return $this->speechSummOfEval[$key];
	}

	/**
	 * Return recommendation Student by key
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function speechRecommend($key) {
		if (!isset($this->speechRecommendation)) {
			$SQL = "
				SELECT therapy,
	                   continue,
	                   remain,
	                   dis_miss,
	                   comments,
	                   CASE length(comments)>0 WHEN TRUE THEN 'Y' ELSE 'N' END as comment_sw,
	                   signame,
	                   sigelator,
	                   signature,
	                   position
	              FROM webset_tx.std_speech_recommend
	             WHERE stdrefid = " . $this->tsrefid . "
			       AND iepyear = " . $this->stdiepyear . "
				";

			$this->speechRecommendation = db::execSQL($SQL)->assoc();
		}

		return $this->speechRecommendation[$key];
	}

}