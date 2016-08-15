<?php
	/**
	* Contains Student Events Methods
	*
	* @copyright Lumen Touch, 2012
	*/
	abstract class IDEAStudentEvent {

		/**
		* Adds new student event
		* 
		* @param int $tsRefID
        * @param string $eventMask
        * @param mixed $eventValue
        * @param mixed $eventValuePrev
		* @return void
		*/
		static public function addEvent($tsRefID, $eventMask, $eventValue, $eventValuePrev) {
        
            if ($eventValue == $eventValuePrev) return;
            
            if (!$tsRefID or !$eventMask) throw new Exception('Setup All parameters.');          
            
            $SQL = "SELECT semdrefid,
                           semddesc
                      FROM webset.statedef_eventdesc
                     WHERE screfid = ".VNDState::factory()->id."
                       AND semddatainsercd = '".$eventMask."'";
            $result = db::execSQL($SQL);            
            $semdrefid = $result->fields[0];
            $semddesc  = $result->fields[1]; 
            if (!$semdrefid) throw new Exception($eventMask  .' is not present for ' . VNDState::factory()->code);          

            if ($eventValuePrev == 'NULL' || $eventValuePrev == '') {
                $message = $semddesc . ' ' . ($eventValue == 'NULL' ? 'blank' : $eventValue) . ' has been added.';
            } else {
                $message = $semddesc . ' ' . $eventValuePrev . ' has been replaced with ' . ($eventValue == 'NULL' ? 'blank' : $eventValue) . '.';
            }
            
            DBImportRecord::factory('webset.std_eventmst', 'semrefid')
                ->set('stdrefid', $tsRefID)
                ->set('semdrefid', $semdrefid)
                ->set('message', db::escape($message))
                ->set('lastuser', db::escape(SystemCore::$userUID))
                ->set('lastupdate', 'NOW()', true)
                ->import();
        }        
        
        /**
        * Adds form event
        * 
        * @param int $tsRefID
        * @param int $formid
        * @param string $eventType   
        * @return void
        */
		static public function formEvent($tsRefID, $formid, $eventType) {
            
            if (!is_numeric($tsRefID)) throw new Exception('Student ID should be Integer. "$tsRefID" provided.');
            if (!is_numeric($formid))  throw new Exception('Form ID should be Integer. "$formid" provided.');
            
            //Search for registered form in Tracking Documents Events. If not exists - leaving procedure
            $SQL = "
                SELECT mfcdoctitle
                  FROM webset.statedef_track_mst mst
                       INNER JOIN webset.statedef_track_signific ts ON mst.signific_id = ts.refid
                       LEFT OUTER JOIN webset.statedef_forms forms ON document_id = forms.mfcrefid
                 WHERE (mst.vndrefid = ".SystemCore::$VndRefID." OR mst.vndrefid IS NULL)
                   AND COALESCE(mst.sourcetype, '') = 'U'
                   AND document_id = ".$formid."
                 ORDER BY mst.seqnum, sdescription
            ";
            $result = db::execSQL($SQL);            
            if ($result->EOF) return;
            //Message text
            $message = 'Form ' . addslashes($result->fields[0]) . ' has been ';
            switch ($eventType) {
              case 'update':
                $message .= 'updated.';
                break;
              case 'delete':
                $message .= 'deleted.';
                break;
              case 'archive':
                $message .= 'archived.';
                break;
              case 'add':
                $message .= 'added.';
                break;
            }
            //Event Type ID detection
            $SQL = "
                SELECT semdrefid
                  FROM webset.statedef_eventdesc
                       INNER JOIN sys_vndmst ON vndrefid = ".SystemCore::$VndRefID."
                       INNER JOIN webset.glb_statemst ON vndstate = state
                 WHERE screfid = staterefid
                   AND semddatainsercd = '<=.Documention Form.=>'
            ";
            $result = db::execSQL($SQL);            
            $semdrefid = $result->fields[0];
            //Event Add
            if ($semdrefid>0) {
                DBImportRecord::factory('webset.std_eventmst', 'semrefid')
                    ->set('stdrefid', $tsRefID)
                    ->set('semdrefid', $semdrefid)
                    ->set('message', db::escape($message))
                    ->set('lastuser', db::escape(SystemCore::$userUID))
                    ->set('lastupdate', 'NOW()', true)
                    ->import();
            }
        }
	}
?>
