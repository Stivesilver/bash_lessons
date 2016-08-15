<?php
    Security::init();

    $pcrefid = io::get('pcrefid');

    $list = new ListClass();

    $list->showSearchFields = true;

    $list->SQL = "
		   SELECT t1.umrefid,
		          t2.umlastname,
		          t2.umfirstname,
		          t3.vouname
		     FROM webset.sys_casemanagermst AS t1
		          INNER JOIN public.sys_usermst AS t2 ON t2.umrefid = t1.umrefid
		          INNER JOIN public.sys_voumst AS t3 ON t3.vourefid = t2.vourefid
		    WHERE t2.vndrefid = VNDREFID ADD_SEARCH
		      AND NOT EXISTS (
				   SELECT 1
					 FROM webset.sys_proccoordassignment pca
					WHERE pcrefid = $pcrefid
					  AND t1.umrefid = pca.cmrefid
		          )
		    ORDER BY LOWER(t2.umlastname), LOWER(t2.umfirstname)
	";

    $list->addSearchField(FFIDEASchool::factory(true)->sqlField('t2.vourefid'));
    $list->addSearchField(FFUserName::factory());

    $list->addColumn('Last Name');
    $list->addColumn('First Name');
    $list->addColumn('School');

    $list->addRecordsProcess('Assign')
        ->url(CoreUtils::getURL('pc_assigm_process.ajax.php', array('pcrefid' => $pcrefid)))
        ->type(ListClassProcess::DATA_UPDATE)
        ->onProcessDone('assignDone')
        ->progressBar(false);

    $list->printList();
?>
<script type="text/javascript">
    function assignDone() {
        api.window.dispatchEvent('student_assigned');
        api.window.destroy();
    }
</script>
