<?php
/**
 * IDEAStudentTXRTI.php
 *
 * Contains basic student RTI data specific for state of TX
 *
 * @author Ganchar Danila <dganchar@lumentouch.com>
 * Created 10-02-2014
 */
class IDEAStudentTXRTI extends IDEAStudentTX {

	/**
	 * Student can requesting assistance from the RTI in some area
	 *
	 * @var array
	 */
	protected $assistanceRTI;

	/**
	 * Enrollment History data
	 *
	 * @var array
	 */
	protected $entrollmentHistory;

	/**
	 * Data about prev schools student
	 *
	 * @var array
	 */
	protected $previousSchools;

	/**
	 * Attendance History
	 *
	 * @var array
	 */
	protected $attendanceHistory;

	/**
	 * Information about language student
	 *
	 * @var array
	 */
	protected $language;

	/**
	 * Subjects and Current Grades Student(Academic Information)
	 *
	 * @var array
	 */
	protected $subjectsAndGrades;

	/**
	 * Student can retained. If yes, then he have basis
	 *
	 * @var array
	 */
	protected $basisRetained;

	/**
	 * Formative Assessments(Achievement Test Data)
	 *
	 * @var array
	 */
	protected $formativeAssessments;

	/**
	 * Texas Assessment of Knowledge and Skills (TAKS)
	 *
	 * @var array
	 */
	protected $taks;

	/**
	 * Documentation/Summary of Core Programs
	 *
	 * @var array
	 */
	protected $corePrograms;

	/**
	 * Speech/Language/Communication data
	 *
	 * @var array
	 */
	protected $speech;

	/**
	 * Behavior Program Student
	 *
	 * @var array
	 */
	protected $behaviorProgram;

	/**
	 * Behavioral Observations, Classroom Behavior Management
	 *
	 * @var array
	 */
	protected $behavior;

	protected $summaryRecommendations;

	/**
	 * Return Assistance RTI
	 *
	 * @return array|bool
	 */
	function getAssistanceRTI() {
		if (!isset($this->assistanceRTI)) {
			$SQL = "
				SELECT strength_math,
                       (SELECT gl_code FROM c_manager.def_grade_levels WHERE gl_refid = grade_math),
	                   strength_reading,
	                   (SELECT gl_code FROM c_manager.def_grade_levels WHERE gl_refid = grade_reading),
	                   strength_writing,
	                   (SELECT gl_code FROM c_manager.def_grade_levels WHERE gl_refid = grade_writing),
                       text_other,
                       strength_other,
                       (SELECT gl_code FROM c_manager.def_grade_levels WHERE gl_refid = grade_other),
                       sk_peer,
	                   sk_foll,
	                   sk_stay,
	                   sk_inte,
                       sk_other,
                       sk_othe,
                       success_choises,
                       success_other,
                       strength_science,
	                   (SELECT gl_code FROM c_manager.def_grade_levels WHERE gl_refid = grade_science) as grade_science,
                       strength_social,
	                   (SELECT gl_code FROM c_manager.def_grade_levels WHERE gl_refid = grade_social) as grade_social,
                       sitareas,
                       meetingtype
                  FROM webset_tx.std_sat_strength
                 WHERE stdrefid = " . $this->tsrefid . "
			       AND iepyear = " . $this->stdiepyear . "
			     ";

			$this->assistanceRTI = db::execSQL($SQL)->assoc();
		}

		return $this->assistanceRTI;
	}

	/**
	 * Return data about Enrollment History
	 *
	 * @return array
	 */
	function getEntrollmentHistory() {
		if (!isset($this->entrollmentHistory)) {
			$SQL = "
				SELECT curently,
	                   curently_no,
	                   transfer,
	                   to_char(transfer_yes, 'yyyy-mm-dd') as transfer_yes
	              FROM webset_tx.std_sat_enroll
	             WHERE stdrefid = " . $this->tsrefid . "
			       AND iepyear = " . $this->stdiepyear . "
	            ";
			$this->entrollmentHistory = db::execSQL($SQL)->fields;
		}

		return $this->entrollmentHistory;
	}

	/**
	 * Return information about prev schools
	 *
	 * @return array
	 */
	function getPreviousSchools() {
		if (!isset($this->previousSchools)) {
			$SQL = "
				SELECT refid,
	                   school,
	                   district,
	                   dates,
	                   grades,
	                   seqnum
	              FROM webset_tx.std_sat_schools  std
	             WHERE stdrefid = " . $this->tsrefid . "
	             ORDER BY seqnum, refid
	            ";

			$this->previousSchools = db::execSQL($SQL)->assocAll();
		}

		return $this->previousSchools;
	}

	/**
	 * Return info about Attendance History
	 *
	 * @return array
	 */
	public function getAttendanceHistory() {
		if (!isset($this->attendanceHistory)) {
			$SQL = "
				SELECT absent_days,
	                   school_days,
	                   abs_good ,
	                   abs_bad  ,
	                   reason_abs,
	                   ill_days,
	                   ill_recs,
	                   skiping_classes,
	                   truancy,
	                   to_char(truancy_yes, 'yyyy-mm-dd') as truancy_yes,
	                   attlack,
	                   attproblem
	             FROM webset_tx.std_sat_enroll
	            WHERE stdrefid = " . $this->tsrefid . "
			      AND iepyear = " . $this->stdiepyear . "
	            ";

			$this->attendanceHistory = db::execSQL($SQL)->fields;
		}

		return $this->attendanceHistory;
	}

	/**
	 * @return array
	 */
	public function getLanguage() {
		if (!isset($this->language)) {
			$SQL = "
				SELECT to_char(survey, 'yyyy-mm-dd') as survey,
	                   resulats,
	                   secondlang,
	                   cultural
	              FROM webset_tx.std_sat_language
	             WHERE stdrefid = " . $this->tsrefid . "
				   AND iepyear = " . $this->stdiepyear . "
	            ";

				$this->language = db::execSQL($SQL)->assoc();
			}

		return $this->language;
	}

	/**
	 * Return subjects and grades
	 *
	 * @return array
	 */
	function getSubjectsAndGrades() {
		if (!isset($this->subjectsAndGrades)) {
			$SQL = "
				SELECT subject,
                       score
		          FROM webset_tx.std_sat_aisubjects
                 WHERE stdrefid = " . $this->tsrefid . "
				   AND iepyear = " . $this->stdiepyear . "
                ";

			$this->subjectsAndGrades = db::execSQL($SQL)->assocAll();
		}

		return $this->subjectsAndGrades;
	}

	/**
	 * Get basis retained.
	 *
	 * @return array
	 */
	function getBasisRetained() {
		if (!isset($this->basisRetained)) {
			$SQL = "
				SELECT retained,
				       basis
	              FROM webset_tx.std_sat_airetained
	             WHERE stdrefid = " . $this->tsrefid . "
				   AND iepyear = " . $this->stdiepyear . "
	            ";

			$this->basisRetained = db::execSQL($SQL)->fields;
		}

		return $this->basisRetained;
	}

	/**
	 * Return Formative Assessments data
	 *
	 * @return array
	 */
	function getFormativeAssessments() {
		if (!isset($this->formativeAssessments)) {
			$SQL = "
				SELECT to_char(asdate, 'yyyy-mm-dd') AS date,
	                   testname,
	                   subjarea,
	                   score
	              FROM webset_tx.std_sat_aidata
	             WHERE stdrefid = " . $this->tsrefid . "
				   AND iepyear = " . $this->stdiepyear . "
	            ";

			$this->formativeAssessments = db::execSQL($SQL)->assocAll();
		}

		return $this->formativeAssessments;
	}

	/**
	 * Return TAKS
	 *
	 * @return array
	 */
	public function getTaks() {
		if (!isset($this->taks)) {
			$SQL = "
				SELECT to_char(tdate, 'yyyy-mm-dd') AS date,
	                   subject,
	                   CASE mastery WHEN 'Y' THEN 'Yes' WHEN 'N' THEN 'NO' END,
	                   score
	              FROM webset_tx.std_sat_aitaks
	             WHERE stdrefid = " . $this->tsrefid . "
				   AND iepyear = " . $this->stdiepyear . "
	            ";

			$this->taks = db::execSQL($SQL)->assocAll();
		}

		return $this->taks;
	}

	/**
	 * Return Core Programs
	 *
	 * @param string $subject
	 * @return array
	 */
	function getCoreProgramsBySubject($subject) {
		if (!isset($this->corePrograms[$subject])) {
			$SQL = "
				SELECT aaadesc,
	                   category_name,
	                   CASE WHEN lower(item_name)='other' THEN item_other ELSE item_name END,
	                   to_char(program_date, 'mm/dd/yyyy') || ' - ' || to_char(program_end, 'mm/dd/yyyy') AS date
	              FROM webset_tx.std_sat_coreprog  std
	                   INNER JOIN webset_tx.def_sat_program_item items  ON item_id = items.refid
	                   INNER JOIN webset_tx.def_sat_program_cat cat ON category_id = cat.refid
	                   INNER JOIN webset.statedef_assess_acc ON  subject_id = aaarefid
	             WHERE stdrefid = " . $this->tsrefid . "
				   AND iepyear = " . $this->stdiepyear . "
	               AND aaadesc  = '$subject'
	             ORDER BY items.enddate desc, aaadesc, cat.seqnum, items.seqnum, item_name
	            ";

			$this->corePrograms[$subject] = db::execSQL($SQL)->assocAll();
		}

		return $this->corePrograms[$subject];
	}

	/**
	 * Get Speech/Language/Communication
	 */
	public function getSpeech() {
		if (!isset($this->speech)) {
			$SQL = "
				SELECT concerns,
	                   articulation_sw,
	                   articulation,
	                   fluency_sw,
	                   fluency,
	                   language_sw,
	                   language,
	                   voice_sw,
	                   voice
	              FROM webset_tx.std_sat_speech
	             WHERE stdrefid = " . $this->tsrefid . "
				   AND iepyear = " . $this->stdiepyear . "
	            ";

			$this->speech = db::execSQL($SQL)->assoc();
		}

		return $this->speech;
	}

	/**
	 * Return program by area
	 *
	 * @param int $area
	 * @return array
	 */
	function getBehaviorProgByArea($area) {
		if (!isset($this->behaviorProgram[$area])) {
			$SQL = "
				SELECT validvalue || COALESCE(' ' || item_other, '') as program,
	                   item_desc,
	                   prule,
	                   responce_pos,
	                   responce_cor,
	                   CASE weekly WHEN 'Y' THEN 'Yes' WHEN 'N' THEN 'No' END as weekly,
	                   CASE roleplay WHEN 'Y' THEN 'Yes' WHEN 'N' THEN 'No' END as roleplay,
	                   documentation,
	                   to_char(date_beg, 'mm/dd/yyyy') || ' - ' || to_char(date_end, 'mm/dd/yyyy') as dates
	              FROM webset_tx.std_sat_beh_prog  std
	                   LEFT OUTER JOIN webset.glb_validvalues items  ON item_id = items.refid
	             WHERE stdrefid = " . $this->tsrefid . "
				   AND iepyear = " . $this->stdiepyear . "
	               AND area = '" . $area . "'
	             ORDER BY items.glb_enddate desc, items.sequence_number, validvalue, brefid
	            ";

			$this->behaviorProgram[$area] = db::execSQL($SQL)->assocAll();
		}

		return $this->behaviorProgram[$area];
	}

	/**
	 * Return behaviors
	 *
	 * @return array|bool
	 */
	function getBehavior() {
		if (!isset($this->behavior)) {
			$SQL = "
				SELECT *
	              FROM webset_tx.std_sat_behavior
	             WHERE stdrefid = " . $this->tsrefid . "
				   AND iepyear = " . $this->stdiepyear . "
	            ";

			$this->behavior = db::execSQL($SQL)->fields;
		}

		return $this->behavior;
	}

} 