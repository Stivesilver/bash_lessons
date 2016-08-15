<?php

	/**
	 * Contains function for calculaiting information for chart bar and graphs
	 *
	 * @author Rogov Michael
	 */
	class IDEAStudentBenchmarkAssessment extends RegularClass {

		/**
		 * Assessment ID
		 *
		 * @var int
		 */
		protected $b_refid;

		/**
		 * Assessment Ref ID
		 *
		 * @var int
		 */
		protected $as_refid;

		/**
		 * Description of Assessment
		 *
		 * @var string
		 */
		protected $assessment_desc;

		/**
		 * Number of Trial in the Assessment
		 *
		 * @var int
		 */
		protected $num_trial;

		/**
		 * Unit of Measure (P - %; F - Fixed Amount)
		 *
		 * @var string
		 */
		protected $unit_of_measure;

		/**
		 * Quantity Achived
		 *
		 * @var int
		 */
		protected $quantity_achived;

		/**
		 * Number of Consecutive Data Collections
		 *
		 * @var int
		 */
		protected $num_consec_data;

		/**
		 * Measure Type
		 *
		 * @var string
		 */
		protected $measure_type;

		/**
		 * Measurement Basis
		 *
		 * @var int
		 */
		protected $measurement_basis;

		/**
		 * Class Constructor
		 *
		 * @param int $as_refid
		 */
		public function __construct($b_refid) {
			CoreUtils::checkArguments('int');
			if ($b_refid == null) {
				throw new Exception('Benchmark is empty!');
			}
			$this->b_refid = $b_refid;

			$res = db::execSQL("
				SELECT as_refid,
					   assessment_desc,
					   unit_of_measure,
				       quantity_achived,
				       num_consec_data,
				       measure_type,
				       measurement_basis
				  FROM webset.std_bgb_assessment
				 WHERE brefid = " . $this->b_refid . "
				   AND vndrefid = VNDREFID
			")
				->assoc();

			$this->as_refid = $res['as_refid'];
			$this->assessment_desc = $res['assessment_desc'];
			$this->unit_of_measure = $res['unit_of_measure']; //P - % or F - fixed amount
			$this->quantity_achived = $res['quantity_achived']; //1 or 25
			$this->num_consec_data = $res['num_consec_data']; //10
			$this->measure_type = $res['measure_type']; //Average Maintain
			$this->measurement_basis = $res['measurement_basis']; // 1, 2, 3, 4 if 4 -> num_consec_data
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param int $as_refid
		 * @return IDEAStudentBenchmarkAssessment
		 */
		public static function factory($b_refid) {
			return new IDEAStudentBenchmarkAssessment($b_refid);
		}

		/**
		 * Gets Title of assessment
		 *
		 * @return string
		 */
		public function getTitle() {
			return $this->assessment_desc;
		}

		/**
		 * Checks is Assessment Exist
		 *
		 * @return bool
		 */
		public function isAssessmentExists() {
			if ($this->as_refid) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Calculates Data for Graphs
		 *
		 * @return array
		 */
		public function calculate() {
			if (!$this->as_refid) {
				throw new Exception('Assessment is not created!');
			}
			$res = db::execSQL("
				SELECT ae.trial_num,
					   ind.met_mastery,
					   ae.trial_date
				  FROM webset.std_bgb_measurement_benchmark AS mb
					   INNER JOIN webset.std_bgb_measurement_indicator AS mi ON mi.mi_refid = mb.mi_refid
				       INNER JOIN webset.std_bgb_indicator AS ind ON ind.ind_refid = mi.ind_refid
				       INNER JOIN webset.std_bgb_measurement AS m ON m.m_refid = mi.m_refid
		               INNER JOIN webset.std_bgb_assessment_entry AS ae ON ae.en_refid = mb.en_refid
				 WHERE ae.as_refid = " . $this->as_refid . "
				   AND ind.vndrefid = VNDREFID
				   AND m.type_measure = 'Measurable'
				 ORDER BY 1 ASC
		    ")
				->assocAll();

			$trial_res = array();
			foreach ($res as $key => $value) {
				if (!array_key_exists($value['trial_num'], $trial_res)) {
					$trial_res[$value['trial_num']]['com_num'] = 1;
					$trial_res[$value['trial_num']]['date'] = $value['trial_date'];
					if ($value['met_mastery'] == 'Y') {
						$trial_res[$value['trial_num']]['met_num'] = 1;
					} elseif ($value['met_mastery'] == 'N') {
						$trial_res[$value['trial_num']]['met_num'] = 0;
					}
				} else {
					$trial_res[$value['trial_num']]['com_num'] += 1;
					$trial_res[$value['trial_num']]['date'] = $value['trial_date'];
					if ($value['met_mastery'] == 'Y') {
						$trial_res[$value['trial_num']]['met_num'] += 1;
					}
				}
			}

			$graph_res = array();
			if ($this->unit_of_measure == 'P') {
				foreach ($trial_res as $key => $value) {
					$graph_res[$key]['result'] = round(($value['met_num'] / $value['com_num']) * 100);
					$graph_res[$key]['date'] = $value['date'];
				}
			} else {
				foreach ($trial_res as $key => $value) {
					$graph_res[$key]['result'] = $value['met_num'];
					$graph_res[$key]['date'] = $value['date'];
				}
			}

			return $graph_res;
		}

		/**
		 * Gets Vertical value for Graph
		 *
		 * @return array
		 */
		public function getVerticalItems() {
			$vertical_items = array();
			if ($this->unit_of_measure == 'P') {
				for ($i = 0; $i <= 100; $i += 10) {
					$vertical_items[] = $i;
				}
			} else {
				for ($i = 0; $i <= $this->quantity_achived; $i += 0.5) {
					$vertical_items[] = $i;
				}
			}

			return $vertical_items;
		}

		/**
		 * Analyzes Benchmark Goal
		 *
		 * @return IDEAStudentBenchmarkAssessment
		 */
		public function analyzeBenchmarkGoal() {
			$res = $this->calculate();

			$is_met = 0;
			$met = false;
			$count_trials = 0;
			foreach ($res as $key => $value) {
				$count_trials++;
				if ($value['result'] >= $this->quantity_achived) {
					$is_met++;
					DBImportRecord::factory('webset.std_bgb_assessment_entry', 'en_refid')
						->key('as_refid', $this->as_refid)
						->key('trial_num', $key)
						->set('is_met', 'Y')
						->set('lastuser', SystemCore::$userUID)
						->set('lastupdate', 'now()', true)
						->import(DBImportRecord::UPDATE_ONLY);
				} else {
					$is_met = 0;
					DBImportRecord::factory('webset.std_bgb_assessment_entry', 'en_refid')
						->key('as_refid', $this->as_refid)
						->key('trial_num', $key)
						->set('is_met', 'N')
						->set('lastuser', SystemCore::$userUID)
						->set('lastupdate', 'now()', true)
						->import(DBImportRecord::UPDATE_ONLY);
				}
				if ($is_met >= $this->num_consec_data) {
					$met = true;
				}
			}

			$number_data_collection_req_left = $this->num_consec_data - $count_trials;
			if ($number_data_collection_req_left < 0) {
				$number_data_collection_req_left = 0;
			}

			$number_data_collection_req_success = 0;
			if (!$met) {
				$number_data_collection_req_success = $this->num_consec_data - $is_met;
			}

			$last_data_collection = db::execSQL("
				SELECT trial_date
				  FROM webset.std_bgb_assessment_entry
				 WHERE as_refid = " . $this->as_refid . "
				   AND trial_num = " . $count_trials)
				->getOne();

			DBImportRecord::factory('webset.std_bgb_current_status', 'cs_refid')
				->key('brefid', $this->b_refid)
				->set('last_analysis', 'now()', true)
				->set('last_data_collection', $last_data_collection)
				->set('goal_status', $met ? 'M' : 'N')
				->set('number_data_collection', $count_trials)
				->set('number_data_collection_req_left', $number_data_collection_req_left)
				->set('number_data_collection_req_success', $number_data_collection_req_success)
				->set('vndrefid', SystemCore::$VndRefID)
				->set('lastuser', SystemCore::$userUID)
				->set('lastupdate', 'now()', true)
				->import(DBImportRecord::UPDATE_OR_INSERT);

			return $this;
		}

		public function getTrialGraph($type = null) {
			$trials = array();
			$tests = db::execSQL("
				SELECT mtrefid,
					   ct.name || ': ' || COALESCE(ts.name, it.name) AS name,
					   ts.max_points
				  FROM webset.std_bgb_measure_test AS ts
				  	   LEFT JOIN webset.disdef_bgb_measure_items AS it ON (ts.templ_id = it.mirefid)
				  	   LEFT JOIN webset.disdef_bgb_measure_cat AS ct ON (it.cat_id = ct.mcrefid)
				 WHERE bench_id = " . $this->b_refid . "
			")->assocAll();
			$j = 0;
			foreach ($tests as $test) {
				$level = 10;
				if ($test['max_points']) {
					$level = $test['max_points'];
				}
				$rows = db::execSQL("
					SELECT mdrefid, mdate, mname, percent_tag
					  FROM webset.std_bgb_measure_data AS dt
					 WHERE test_id = " . $test['mtrefid'] . "
					 ORDER BY mdate ASC
				")->assocAll();
				$pg = new UIProgressGraph();
				$pg->setSize(600, 150);
				$pg->indent(0);

				# create vertical scale
				for ($i = 0; $i <= $level/2; $i++) {
					$val = $i * 2;
					$pg->addVerticalItem((string)$val, $val);
				}

				# create graph
				foreach ($rows as $row) {
					$pg->addHorizontalItem($this->prepareDateType($row['mdate']), $row['percent_tag']);
				}
				if ($type == 2) {
					$path = SystemCore::$tempPhysicalRoot . '/benchmark_' . SystemCore::$userID . '_' . $test['mtrefid'] . '.jpg';
					SystemCore::$FS->rename($pg->getImagePath(), $path);
					$trials[$j]['img'] = $path;
				}
				$trials[$j]['name'] = $test['name'];
				$trials[$j]['html'] = $pg->toHTML();
				$j++;

			}
			return $trials;
		}

		/**
		 * Prepares data for column with type DATE
		 *
		 * @param string $data
		 * @return string
		 */
		private function prepareDateType($data) {
			$m = array();
			if (preg_match('@(\d{4})[-./](\d{1,2})[-./](\d{1,2})@', $data, $m) > 0) {
				# pattern: yyyy-mm-dd
				$date = substr('00' . $m[2], -2) . '/' . substr('00' . $m[3], -2) . '/' . $m[1];
			} else if (preg_match('@(\d{1,2})[./-](\d{1,2})[./-](\d{4})@', $data, $m) > 0) {
				# pattern: mm-dd-yyyy
				$date = substr('00' . $m[1], -2) . '/' . substr('00' . $m[2], -2) . '/' . $m[3];
			} else if (preg_match('@^(\d{7,15})$@', $data, $m) > 0) {
				# timestamp
				$date = date('m/d/Y', $data);
			} else {
				$date = $data;
			}
			return $date;
		}
	}

?>
