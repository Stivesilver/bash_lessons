<?php
    $SQL = "
        SELECT scanswer
	      FROM webset.std_spconsid std
	           INNER JOIN webset.statedef_spconsid_answ ans ON ans.scarefid = std.scarefid
               INNER JOIN webset.statedef_spconsid_quest qws ON std.scqrefid = qws.scmrefid
	     WHERE std.stdrefid = ".$tsRefID."
           AND std.syrefid = ".$stdIEPYear."
	       AND LOWER(scmquestion) LIKE '%visually impaired%'
    ";

	if (db::execSQL($SQL)->getOne()=='Yes') {
        return true;
    } else {
        return false;
    }
?>