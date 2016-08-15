<?php
    /**
    * Contains Student Registry Methods
    *
	* @copyright Lumen Touch, 2012
    */
    abstract class IDEAStudentRegistry {

        /**
        * Returnes Student Reqistry Value
        *
        * @param int $stdrefid
        * @param string $keygroup
        * @param string $keyname
        * @param int $iepyear
        * @return mixed
        */
        public static function readStdKey($stdrefid, $keygroup, $keyname, $iepyear = 0) {

            if (!$stdrefid or !$keygroup or !$keyname) throw new Exception('Setup All parameters.');

            if ($iepyear > 0) {
                $iepyearWhere = "AND iep_year = $iepyear";
            } else {
                $iepyearWhere = "";
            }

            $SQL = "
                SELECT srkeydata
                  FROM webset.std_registry
                 WHERE stdrefid = ".$stdrefid."
                   AND srkeygroup = '".$keygroup."'
                   AND srkeyname = '".$keyname."'
                   ".$iepyearWhere."
                 ORDER BY lastupdate desc
            ";
            return stripslashes(db::execSQL($SQL)->getOne());
        }

        /**
        * Saves Student Reqistry Value
        *
        * @param int $stdrefid
        * @param string $keygroup
        * @param string $keyname
        * @param mixed $keyvalue
        * @param int $iepyear
        * @return string
        */
        public static function saveStdKey($stdrefid, $keygroup, $keyname, $keyvalue, $iepyear = 0) {

			if (!$stdrefid or !$keygroup or !$keyname) throw new Exception('Setup All parameters.');

            $irecord = DBImportRecord::factory('webset.std_registry', 'rrefid')
                ->key('stdrefid', $stdrefid)
                ->key('srkeygroup', db::escape($keygroup))
                ->key('srkeyname', db::escape($keyname))
                ->set('srkeydata', db::escape(trim($keyvalue)))
                ->set('lastuser', db::escape(SystemCore::$userUID))
                ->set('lastupdate', 'NOW()', true);

            if ($iepyear > 0) {
                $irecord->key('iep_year', $iepyear);
            }

            $irecord->import();

            return 'Ok';
        }

	    /**
	     * Returns Registry ID
	     *
	     * @param int $stdrefid
	     * @param string $keygroup
	     * @param string $keyname
	     * @param int $iepyear
	     * @return mixed
	     */
	    public static function getRecordID($stdrefid, $keygroup, $keyname, $iepyear = 0) {

		    if (!$stdrefid or !$keygroup or !$keyname) throw new Exception('Setup All parameters.');

		    if ($iepyear > 0) {
			    $iepyearWhere = "AND iep_year = $iepyear";
		    } else {
			    $iepyearWhere = "";
		    }

		    $SQL = "
                SELECT rrefid
                  FROM webset.std_registry
                 WHERE stdrefid = ".$stdrefid."
                   AND srkeygroup = '".$keygroup."'
                   AND srkeyname = '".$keyname."'
                   ".$iepyearWhere."
                 ORDER BY lastupdate desc
            ";
		    return (int)db::execSQL($SQL)->getOne();
	    }
    }
?>
