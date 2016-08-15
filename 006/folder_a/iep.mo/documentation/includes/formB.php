<?php
    $SQL = "
        SELECT CASE WHEN LOWER(scanswer) LIKE '%yes%' THEN 'Yes' ELSE 'No' END
          FROM webset.std_spconsid std
               INNER JOIN webset.statedef_spconsid_answ ans ON ans.scarefid = std.scarefid
               INNER JOIN webset.statedef_spconsid_quest qws ON std.scqrefid = qws.scmrefid
         WHERE std.stdrefid = ".$tsRefID."
           AND std.syrefid = ".$stdIEPYear."
		   AND LOWER(scmquestion) LIKE '%extended school year%'
    ";
    if (db::execSQL($SQL)->getOne()=='Yes') {
        return true;
    }

    if (IDEACore::disParam(53)=="Y") {
        $SQL = "SELECT sesymesydecisionsw
                  FROM webset.std_esy_mst std
                 WHERE std.stdrefid = ".$tsRefID."
                   AND std.iepyear = ".$stdIEPYear."
        ";

	    if (db::execSQL($SQL)->getOne()=='Y') {
            return true;
        }
    }

    return false;
?>