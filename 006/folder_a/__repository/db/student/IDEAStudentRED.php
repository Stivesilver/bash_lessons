<?php

	/**
	 * Include SQL-queries for RED
	 *
	 * @author Alex Kalevich
	 * Created 19-03-2015
	 */
	class IDEAStudentRED extends IDEAStudentEval {

		/**
		 * General Information
		 *
		 * @var array
		 */
		protected $gen_info;

		/**
		 * Summary
		 *
		 * @var array
		 */
		protected $summary;

		/**
		 * Participants
		 *
		 * @var array
		 */
		protected $participants;

		/**
		 * Conclusions
		 *
		 * @var array
		 */
		protected $conclusions;

		public function __construct($tsRefID = 0, $evalproc_id = 0) {
			parent::__construct($tsRefID, $evalproc_id);
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param int $tsRefID
		 * @return IDEAStudentRED
		 */
		public static function factory($tsRefID, $evalproc_id = 0) {
			return new IDEAStudentRED($tsRefID, $evalproc_id);
		}

		public function getREDGenInfo() {
			$this->gen_info = $this->execSQL("
				SELECT currdisability,
				 	   referraldt,
				 	   finalized_date,
				 	   red_data_review,
				 	   red_data_review_o,
				 	   red_teammet,
				 	   red_teammet_dt,
				 	   stdgrade,
				 	   stdage
				  FROM webset.es_std_common AS gi
				 WHERE gi.evalproc_id = $this->evalproc_id
			")->fields;
			if ($this->gen_info['stdage'] == '') $this->gen_info['stdage'] = $this->get('stdage');
			if ($this->gen_info['stdgrade'] == '') $this->gen_info['stdgrade'] = $this->get('grdlevel');
			return $this->gen_info;
		}

		public function getREDSummary() {
			$areas = $this->execSQL("
				SELECT screen.scrrefid as screening_id,
				       red.redrefid,
				       screen.scrdesc,
				       red.red_desc,
				       red.red_text,
				       red.red_asstext,
				       screen.scrlongdesc,
				       red.red_assneed,
				       red.red_asstext,
				       red.plafp,
				       red.skill
				  FROM webset.es_std_red red
				       INNER JOIN webset.es_statedef_screeningtype screen ON red.screening_id = screen.scrrefid 
				 WHERE red.evalproc_id = $this->evalproc_id
				 ORDER BY screen.scrseq, red.redrefid
			")->assocAll();

			$others_id = db::execSQL("
					SELECT refid
					  FROM webset.es_statedef_red_ds
					 WHERE LOWER(datasource) LIKE '%other%'
			")->indexCol();

			foreach ($areas as $key => $value) {
				$areas[$key]['ds'] = $this->execSQL("
					SELECT dsrefid,
					       ds_other,
					       datasource,
					       webset.es_statedef_red_ds.refid
					  FROM webset.es_statedef_red_ds
					       LEFT OUTER JOIN webset.es_std_redds ON webset.es_std_redds.dsrefid = webset.es_statedef_red_ds.refid AND redrefid = " . ($value['redrefid'] == '' ? '0' : $value['redrefid'] ) . "
					 WHERE screening_id = " . $value["screening_id"] . "
					 ORDER BY seq
				")->assocAll();
				foreach($areas[$key]['ds'] as $k => $v) {
					if (!in_array($v['refid'] , $others_id)) {
						$areas[$key]['ds'][$k]['ds_other'] = '';
					}
				}
 			}
			$this->summary = $areas;
			return $this->summary;
		}

		public function getREDConclusions() {
			$this->conclusions = $this->execSQL("
				SELECT yes_data_evi,
					   yes_data_evr,
					   base_no_data,
					   base_is_data,
					   base_adm_data,
					   base_ind,
					   add_data_obt,
					   to_char(add_data_deter, 'mm/dd/yyyy') as add_data_deter,
					   no_data_evi,
					   no_data_evr,
					   no_data_cur,
					   no_data_curtext,
					   no_data_noevi,
					   no_data_change,
					   no_data_change_from,
					   no_data_change_to,
					   no_data_contin,
					   no_data_change_contin_t1,
					   no_data_change_contin_t2,
					   comments
				  FROM webset.es_std_red_concl
				 WHERE evalproc_id = $this->evalproc_id
			")->fields;
			return $this->conclusions;
		}

		public function getREDParticipants() {
			$this->participants = $this->execSQL("
				SELECT std.refid,
				       part_name,
				       CASE role = 'Other'
				       WHEN TRUE THEN COALESCE(other, '')
				       ELSE role
				       END AS role,
				       std.lastupdate,
				       std.lastuser,
				       stdrefid
				  FROM webset.es_std_red_part AS std
				       INNER JOIN webset.es_statedef_red_part AS state ON state.role = std.part_role
				 WHERE evalproc_id = $this->evalproc_id
				   AND stdrefid = " . $this->tsrefid . "
				 ORDER BY state.seq
			")->assocAll();
			return $this->participants;
		}

	}
