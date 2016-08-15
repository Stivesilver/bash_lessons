<?php

    /**
     * Basic IDEA blocks
     * Contains sql fields, tables, query parts, titles special for logged user's district
     *
     * @copyright Lumen Touch, 2012
     */
    abstract class IDEAPartsID extends IDEAParts {

        /**
         * Goal Field
         * DB Table: webset.std_oth_goals
         *
         * @var string
         */
        protected static $goal_statement = "
			CASE
				WHEN goal_type='O' THEN own_goal
				ELSE UPPER(SUBSTRING(TRIM(COALESCE(cdesc, cond_oth, '')),1,1)) ||
					 SUBSTRING(TRIM(COALESCE(cdesc, cond_oth, '')),2) ||
					 ' ' || COALESCE(stdname, '') ||
					 ' ' || TRIM(COALESCE(gdskgaaction, verb_oth, '')) ||
					 ' ' || TRIM(COALESCE(gdskgccontent, content_oth, '')) ||
					 ' ' || TRIM(COALESCE(mdesc, meas_oth, '')) ||
					 ' ' || COALESCE(timeframe_oth, '')
			END
		";
		
        /**
         * Goal Procedure Field
         * DB Table: webset.std_oth_goals
         *
         * @var string
         */
        protected static $goal_procedure = "
			CASE
				WHEN goal_type='O' THEN own_eval
				ELSE TRIM(UPPER(SUBSTRING(TRIM(COALESCE(proc_oth, '')),1,1)) ||
					 SUBSTRING(TRIM(COALESCE(proc_oth, '')),2) ||
					 ' ' || COALESCE(criteria_oth, '') ||
					 ' ' || COALESCE(grade_eval, '') ||
					 ' ' || TRIM(COALESCE(edesc, sched_oth, '')))
			END
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