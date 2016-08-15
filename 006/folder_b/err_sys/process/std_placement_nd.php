<?php
    $SQL = "
        SELECT COUNT(1)
	      FROM webset.std_placementconsiderations
	     WHERE stdrefid = ".$tsRefID."
    ";
	$considered = db::execSQL($SQL)->getOne();

	$SQL = "
        SELECT COUNT(1)
	      FROM webset.std_placementselecteddecision  std
	           INNER JOIN webset.statedef_placementselectdecisions stt ON stt.sspsdrefid = std.sspsdrefid
	     WHERE stdrefid = ".$tsRefID."
    ";

	if ($considered > 0 && db::execSQL($SQL)->getOne() == 0 && IDEACore::disParam(51) == 'Y') {
	    return true;
    } else {
        return false;
    }
?>