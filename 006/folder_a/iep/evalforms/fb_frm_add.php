<?php

	Security::init();

	$dskey = io::get('dskey');

	$list = new ListClass('list1');

	$list->title = 'FB Documentation';

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT *
          FROM (SELECT 1 || '_' || dfrefid,
			   		   mfcpdesc,
		      		   title,
		      		   fprp.mfcprefid
			  	  FROM webset.disdef_forms AS df
			     	   INNER JOIN webset.def_formpurpose AS fprp ON (fprp.mfcprefid  = df.mfcprefid)
			 	 WHERE df.vndrefid = " . SystemCore::$VndRefID . "
				  UNION ALL
				 SELECT 2 || '_' ||stf.mfcrefid,
				        mfcpdesc,
				        mfcdoctitle AS title,
				        fprp.mfcprefid
				   FROM webset.statedef_forms AS stf
				        INNER JOIN webset.def_formpurpose AS fprp ON fprp.mfcprefid  = stf.mfcprefid
				  WHERE stf.screfid = " . VNDState::factory()->id . "
				    AND fb_type = 1) AS t
				    WHERE ADD_SEARCH
		 ORDER BY 2,3
	";

	$list->addSearchField('Form Title')->sqlField('title');
	$list->addSearchField(FFSelect::factory('Form Purpose'))
			->sql("
			SELECT mfcprefid, mfcpdesc
			  FROM webset.def_formpurpose
	         ORDER BY mfcpdesc
		")
			->sqlField('mfcprefid');

	$list->addColumn('Form Purpose')
			->sqlField('mfcpdesc')
			->type('group');

	$list->addColumn('Title')->sqlField('title');

	$list->editURL = 'javascript: openSubmission(0, "AF_REFID");';

	$list->printList();

	io::jsVar('dskey', $dskey);

?>
<script>

	function openSubmission(f_refid, t_refid) {
		if (!t_refid) {
			t_refid = 0;
		}
		var wnd = api.window.open(
			'Form Submission',
			api.url('./fb_form_view.php', {'f_refid': f_refid, 't_refid': t_refid, 'dskey': dskey})
		);
		wnd.addEventListener(
			ObjectEvent.CLOSE,
			function (e) {
				api.window.destroy();
			},
			this
		);
		wnd.maximize();
		wnd.show();
	}

</script>
