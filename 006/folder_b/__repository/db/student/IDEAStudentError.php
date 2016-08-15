<?php
    /**
    * Contains Student Error Check Methods
    *
	* @copyright Lumen Touch, 2012
    */
    abstract class IDEAStudentError {

        /**
        * Registers Student Error
        *
        * @param int $stdrefid
        * @param int $esrefid
        * @return void
        */
        public function registerError($stdrefid, $esrefid) {

                if (!$stdrefid or !$esrefid) throw new Exception('Setup All parameters.');

                DBImportRecord::factory('webset.std_err', 'refid')
                    ->set('stdrefid', $stdrefid)
                    ->set('esrefid', $esrefid)
                    ->set('lastuser', db::escape(SystemCore::$userUID))
                    ->set('lastupdate', 'NOW()', true)
                    ->import();

        }        
        
        /**
        * Clears Student Errors
        *
        * @param int $stdrefid
        * @return void
        */
        public function clearErrors($stdrefid) { 

                if (!$stdrefid) throw new Exception('Setup Student ID number.');

                db::execSQL("
                    DELETE FROM webset.std_err WHERE stdrefid = $stdrefid;
                ");

        }
        
        /**
        * Check Student Errors
        *
        * @param int $stdrefid
        * @return void
        */
        public function checkErrors($stdrefid) { 

                if (!$stdrefid) throw new Exception('Setup Student ID number.');
                
                $student = IDEAStudent::factory($stdrefid);

                self::clearErrors($stdrefid);
                
                $checkers = IDEAFormat::getErrorHandlers();
                
                for ($i = 0; $i < count($checkers); $i++) {
                    if (self::checkerRun($stdrefid, $student->get('stdiepyear'), $checkers[$i]['resol_file_path']) == 1) {
                        self::registerError($stdrefid, $checkers[$i]['srrefid']);
                    }  
                }

        }
        
        /**
        * Runs Error Checking File
        *
        * @param int $tsRefID
        * @param int $stdIEPYear
        * @param string $path
        * @return bool
        */
        private function checkerRun($tsRefID, $stdIEPYear, $path) {
            $fullpath = SystemCore::$physicalRoot . str_replace('/applications/webset', '/apps/idea', $path); 
                if (file_exists($fullpath) && $path && $tsRefID > 0) {
                    $a = include($fullpath);                
                    return $a;
                }            
            return false;
        }
    }
?>
