<?php
	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey);
	$tsRefID    = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$RefID      = io::get('RefID');

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit State Accommodations for WIDA-ACCESS';

	$edit->setSourceTable('webset.std_form_d_wida', 'refid');

	$edit->addGroup('General Information');
    $SQL = $RefID > 0 ? "
                        SELECT accrefid,
                               catname || ': ' || accname
                          FROM webset.statedef_aa_wida_acc acc
                               INNER JOIN webset.statedef_aa_wida_cat cat ON catrefid = acccat
                         WHERE accrefid IN (SELECT std.accrefid
                                              FROM webset.std_form_d_wida std
                                             WHERE refid=".$RefID.")
                     " : "
                        SELECT accrefid,
                               catname || ': ' || accname
                          FROM webset.statedef_aa_wida_acc acc
                               INNER JOIN webset.statedef_aa_wida_cat cat ON catrefid = acccat
                         WHERE (cat.enddate IS NULL or now()< cat.enddate)
                           AND (acc.enddate IS NULL or now()< acc.enddate)
                           AND accrefid NOT in (SELECT std.accrefid
                                                  FROM webset.std_form_d_wida std
                                                 WHERE stdrefid=".$tsRefID."
                                                   AND syrefid=".$stdIEPYear.")
                         ORDER BY cat.seqnum, cat.catname, acc.seqnum, acc.accname
	";

	$edit->addControl('Accommodation', 'select')
		->name('accrefid')
		->sqlField('accrefid')
		->sql($SQL)
		->req();

	$edit->addControl('Assessment Domains', 'select_check')
		->sqlField('domains')
		->sql("
            SELECT drefid,
                   domain || CASE WHEN EXISTS (SELECT 1 
                                                 FROM webset.statedef_aa_wida_acc
                                                WHERE invaliddomains LIKE '%' || drefid::varchar || '%'
                                                  AND accrefid = VALUE_01) THEN ' (See Note 1)' ELSE '' END
              FROM webset.statedef_aa_wida_domain
             WHERE (enddate IS NULL or now()< enddate)
               AND EXISTS (SELECT 1 
                             FROM webset.statedef_aa_wida_acc
                            WHERE alloweddomains LIKE '%' || drefid::varchar || '%'
                              AND accrefid = VALUE_01)
             ORDER BY seqnum, domain
        ")
		->tie('accrefid')
		->breakRow()
		->req();

	$edit->addGroup('Update Information', true);
	$edit->addControl("Last User", "protected")->value($_SESSION["s_userUID"])->sqlField('lastuser');        
	$edit->addControl("Last Update", "protected")->value(date("m-d-Y H:i:s"))->sqlField('lastupdate');        
	$edit->addControl("Student ID", "hidden")->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl("IEP Year", "hidden")->value($stdIEPYear)->sqlField('syrefid');
	$edit->addControl('Sp Considerations ID', 'hidden')->value(io::geti('spconsid'))->name('spconsid');

	$edit->finishURL = CoreUtils::getURL('part3.php', array('dskey'=>$dskey)); 
	$edit->cancelURL = CoreUtils::getURL('part3.php', array('dskey'=>$dskey));

	$edit->saveAndAdd = (db::execSQL($SQL.' OFFSET 1 ')->getOne()!='');

	$edit->printEdit();    

	include("notes3.php");
	include("notes0.php");
?>
<script type="text/javascript">   
	var edit1 = EditClass.get();
	edit1.onSaveDoneFunc(
		function(refid) {
			if ($('input[name="RefID"]').val() == 0) {
				api.reload()
			}            
		}
	)
</script>
