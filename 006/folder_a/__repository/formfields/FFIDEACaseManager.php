<?php
	/**
	* Select Field with Case Manager
	*
	* @copyright Lumen Touch, 2012
	*/
	class FFIDEACaseManager extends FFSelect {

		/**
		* Class Constructor
		*
		*/
		public function __construct($mode = '') {
			parent::__construct();
			$this->caption = 'Case Manager';			
			$this->sql("
					SELECT t2.umrefid,
                           t2.umlastname || ', ' || t2.umfirstname
                      FROM webset.sys_casemanagermst AS t1
                           INNER JOIN sys_usermst AS t2 ON t2.umrefid = t1.umrefid
                     WHERE t1.vndrefid = VNDREFID
                     ORDER BY LOWER(t2.umlastname), LOWER(t2.umfirstname)
				");
			
			if ($mode == 'pc') {				
				$this->sql("
					SELECT '".SystemCore::$userID."' AS umrefid,
                           '".SystemCore::$userName."' AS umname,
                           LOWER('".$_SESSION["s_userName"]."') AS order_column
                     WHERE EXISTS (SELECT 1 
                                     FROM webset.sys_casemanagermst AS t1 
                                    WHERE t1.umrefid = ".SystemCore::$userID.")
                     UNION
                    SELECT t3.umrefid,
                           t3.umlastname || ', ' || t3.umfirstname AS umname,
                           LOWER(t3.umlastname || ', ' || t3.umfirstname) AS order_column
                      FROM webset.sys_proccoordmst AS t1
                           INNER JOIN webset.sys_proccoordassignment AS t2 ON t2.pcrefid = t1.pcrefid
                           INNER JOIN public.sys_usermst AS t3 ON t3.umrefid = t2.cmrefid
                     WHERE t1.umrefid = ".SystemCore::$userID."
                     ORDER BY order_column
				    ");
			}
			
		}

		/**
		* Creates an instance of this class
		*
		* @static
		* @return FFIDEACaseManager
		*/
		public static function factory($mode = '') {
			return new FFIDEACaseManager($mode);
		}

	}

?>