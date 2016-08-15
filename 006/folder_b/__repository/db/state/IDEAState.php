<?php

	/**
	 * Contains basic state data
	 *
	 * @copyright Lumen Touch, 2013
	 */
	class IDEAState extends RegularClass {

		/**
		 * District ID
		 * DB Table: public.def_statemst
		 *
		 * @var int
		 */
		protected $staterefid;

		/**
		 * Initializes basic properties
		 *
		 * @param int $staterefid
		 */
		public function __construct($staterefid = null) {
			if ($staterefid == null) {
				throw new Exception('State ID has not been specified.');
			}
			$this->staterefid = $staterefid;
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param int $staterefid
		 * @return IDEAState
		 */
		public static function factory($staterefid = null) {
			return new IDEAState($staterefid);
		}

		/**
		 * Return array labels for PRIOR WRITTEN NOTICE (State CT)
		 *
		 * @return array
		 */
		public static function getLabelsPriorNotices() {
			return db::execSQL("
				SELECT scrrefid,
		               scrdesc
		          FROM webset.es_statedef_screeningtype
		         INNER JOIN webset.glb_statemst ON webset.glb_statemst.staterefid = webset.es_statedef_screeningtype.screfid
		         WHERE (1=1)
		           AND webset.es_statedef_screeningtype.screfid = " . VNDState::factory()->id . "
		         ORDER BY state, scrseq, scrdesc
			    ")->assocAll();
		}

	}

?>