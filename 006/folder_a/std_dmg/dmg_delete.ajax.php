<?php
	Security::init();

    function update_deleted_record($stdrefid) {

        $SQL = "
            SELECT a.attname
	          FROM pg_class c, pg_attribute a,pg_type t
	         WHERE relkind = 'r' AND c.relname='dmg_studentmst'
	           AND a.attnum > 0
	           AND a.atttypid = t.oid
	           AND a.attrelid = c.oid
	           AND a.attname !='stdrefid'
	           AND EXISTS (SELECT 1 
	                         FROM pg_class tc, pg_attribute ta, pg_type tt
	                        WHERE tc.relkind = 'r' AND tc.relname='dmg_studentmst_deleted'
			                  AND ta.attnum > 0
					          AND ta.atttypid = tt.oid
					          AND ta.attrelid = tc.oid
					          AND a.attname = ta.attname)
	         ORDER BY a.attnum
        ";
        $result = db::execSQL($SQL);

        while(!$result->EOF){
            $SQL = "
                UPDATE webset.dmg_studentmst_deleted
                   SET " . $result->fields[0] . " = webset.dmg_studentmst." . $result->fields[0] . "
                  FROM webset.dmg_studentmst
                 WHERE webset.dmg_studentmst.stdrefid = webset.dmg_studentmst_deleted.stdrefid
                   AND webset.dmg_studentmst_deleted.stdrefid = $stdrefid
            ";
            $rs = db::execSQL($SQL);
			$result->moveNext();
		}

        $SQL = "
            UPDATE webset.dmg_studentmst_deleted
               SET lastuser = 'UMUID',
                   lastupdate = now()
             WHERE stdrefid = $stdrefid
        ";
        $result = db::execSQL($SQL);        
    }    
    	
	$RefIDs = explode(',', io::post('RefID'));  
	for ($i=0; $i < sizeOf($RefIDs); $i++) {
		if($RefIDs[$i]>0) { 
            $SQL = "
                INSERT INTO webset.dmg_studentmst_deleted (stdrefid)
                SELECT stdrefid FROM webset.dmg_studentmst WHERE stdrefid = " . $RefIDs[$i]. "
            ";
            try { 
            	db::execSQL($SQL);
			} catch (Exception $e) {}
            
            update_deleted_record($RefIDs[$i]);

            $SQL = "
                DELETE FROM webset.dmg_studentmst WHERE stdrefid = " . $RefIDs[$i] . "
            ";          
            db::execSQL($SQL);
		}
    }
?>