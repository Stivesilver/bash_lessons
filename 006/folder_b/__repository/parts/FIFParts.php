<?php
	/**
	* Basic 504 blocks
	* Contains sql fields, tables, query parts, titles special for logged user's district
	*
	* @copyright Lumen Touch, 2012
	*/
	abstract class FIFParts {

		/**
		* This variable indicates that startup params have been initialized
		*
		* @var bool
		*/
		private static $initialized   = false;

		/**
		* 504 Process Period
		* DB Table: webset.std_fif_history, webset.disdef_fif_status
		*
		* @var string
		*/
		private static $fifPeriod     = "difdesc || COALESCE(TO_CHAR(ts.initdate, '-MM/DD/YYYY'),'') || COALESCE(TO_CHAR(ts.exitdate, '-MM/DD/YYYY'),'')";

        /**
		* 504 Status Check
        * For queries without joined status tables
		* DB Table: webset.std_fif_history, webset.disdef_fif_status
		*
		* @var string
		*/
        private static $fifActive     = "";
        
        /**
        * 504 Status Check
        * For queries with joined status tables
        * DB Table: webset.std_fif_history, webset.disdef_fif_status
        *
        * @var string
        */
        private static $fifActivePlain = "";
        

	    /**
		* Date Entered 504 Process
		* DB Table: webset.std_fif_history
		*
		* @var string
		*/
	    private static $initdate       = "TO_CHAR(initdate, 'MM/DD/YYYY')";

	    /**
		* Date Exited 504 Process
		* DB Table: webset.std_fif_history
		*
		* @var string
		*/
	    private static $exitdate       = "TO_CHAR(exitdate, 'MM/DD/YYYY')";

	    /**
		* District 504 Enrollment JOIN part
		* DB Table: webset.disdef_fif_status
		*
		* @var string
		*/
	    private static $enrollJoin     = "LEFT OUTER JOIN webset.disdef_fif_status status  ON fif.difrefid  = en.difrefid";


		/**
		* Initializes properties
		*
		* @return void
		*/
		public static function init() {

			if (self::$initialized) return;
			self::$initialized = true;

            self::$fifActive  = "
                EXISTS (SELECT 1 
                          FROM webset.std_fif_history fif
                         WHERE std.stdrefid = fif.stdrefid
                           AND COALESCE(initdate, to_date('1000-01-01', 'YYYY-MM-DD')) <= current_date 
                           AND current_date <= COALESCE(exitdate, TO_DATE('3000-01-01', 'YYYY-MM-DD')) 
                           AND COALESCE(fif.difrefid,0) IN (" . self::activeFifCodes() . "))
            ";
            
            self::$fifActivePlain  = "
                COALESCE(initdate, to_date('1000-01-01', 'YYYY-MM-DD')) <= current_date 
                AND current_date <= COALESCE(exitdate, TO_DATE('3000-01-01', 'YYYY-MM-DD')) 
                AND COALESCE(status.difrefid,0) IN (" . self::activeFifCodes() . ")
            ";

		}

		/**
		* Returns specified property value
		*
		* @param mixed $property
		* @return mixed
		*/
		public static function get($property) {
			if (!self::$initialized) self::init();
            return self::$$property;
		}
       
        
        /**
        * Returns ID numbers of Active 504 Enrollment Codes to be used in student sp ed lists
        * @return string
        */
        public static function activeFifCodes() {

            $filepath = SystemCore::$tempPhysicalRoot . '/' . SystemCore::$VndRefID . '_cache_504.txt';

            if (file_exists($filepath)) {
                $active_id = file_get_contents($filepath);
            }  else {
                $SQL = "
                    SELECT difrefid
                      FROM webset.disdef_fif_status district
                           INNER JOIN webset.def_fif_status state ON state.fifrefid = district.statecode_id
                     WHERE vndrefid = VNDREFID
                       AND (state.enddate IS NULL OR NOW() < state.enddate)
                       AND (district.enddate IS NULL OR NOW() < district.enddate)                
                       AND active_sw = 'Y'
                ";
                $enrs = db::execSQL($SQL);
                $active_id = '';
                while (!$enrs->EOF) {
                    $active_id .= $enrs->fields['difrefid'] . ',';
                    $enrs->MoveNext();
                }
                $active_id .= "0";
                file_put_contents($filepath, $active_id);
            }
            return $active_id;
        }
	}
?>