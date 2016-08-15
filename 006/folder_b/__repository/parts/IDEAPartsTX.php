<?php

	/**
	 * Basic IDEA blocks
	 * Contains sql fields, tables, query parts, titles special for logged user's district
	 *
	 * @copyright Lumen Touch, 2012
	 */
	abstract class IDEAPartsTX extends IDEAParts {

		/**
		 * Goal Field
		 * DB Table: webset_tx.std_sb_goals
		 *
		 * @var string
		 */
		protected static $goal_statement = "
			TRIM(CASE gv.validvalue
					  WHEN 'Other' THEN COALESCE(g.timeframe_oth,'')
					  WHEN 'By' THEN 'By ' || COALESCE(to_char(g.timeframe_dt, 'mm-dd-yyyy'),'')
					  ELSE gv.validvalue
				  END)													||

			 COALESCE(' ' || g.stdname || ' ', ' stdfirstname will ') 	||

			 TRIM(CASE ga.action
					  WHEN 'Other' THEN COALESCE(g.action_oth,'')
					  ELSE COALESCE(ga.action, '')
				  END)													|| ' ' ||

			 TRIM(COALESCE(g.behavior, ''))								|| ' ' ||

			 TRIM(COALESCE(g.condition, ''))							|| ' ' ||

			 TRIM(CASE gc.criteria
					  WHEN 'Other' THEN COALESCE(g.criteria_oth,'')
					  ELSE COALESCE(gc.criteria, '')
				  END) ||
			 COALESCE ('. ' || g.ainfo, '')
		";

		/**
		 * Objective Field
		 * DB Table: webset_tx.std_sb_objectives
		 *
		 * @var string
		 */
		protected static $objective_statement = "
			TRIM(CASE ov.validvalue
					  WHEN 'Other' THEN COALESCE(o.timeframe_oth,'')
					  WHEN 'By' THEN 'By ' || COALESCE(to_char(o.timeframe_dt, 'mm-dd-yyyy'),'')
					  ELSE ov.validvalue
				  END)												    ||

			 COALESCE(' ' || o.stdname || ' ', ' stdfirstname will ') 	||

			 TRIM(CASE oa.action
					  WHEN 'Other' THEN COALESCE(o.action_oth,'')
					  ELSE COALESCE(oa.action, '')
				  END)													|| ' ' ||

			 TRIM(COALESCE(o.behavior, ''))								|| ' ' ||

			 TRIM(COALESCE(o.condition, ''))							|| ' ' ||

			 TRIM(CASE oc.criteria
					  WHEN 'Other' THEN COALESCE(o.criteria_oth,'')
					  ELSE COALESCE(oc.criteria, '')
				  END) ||
			 COALESCE ('. ' || o.ainfo, '')
		";

		/**
		 * Returns specified property value
		 *
		 * @param mixed $property
		 * @return mixed
		 */
		public static function get($property) {
			parent::init();
			return self::$$property;
		}

	}

?>