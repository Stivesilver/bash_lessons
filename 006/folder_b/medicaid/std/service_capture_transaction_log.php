<?php
	Security::init();

	$ds = new DataStorage(io::get('dsKey'));
	$mss_refid = $ds->get('mss_refid');

	$list = new ListClass();

	$list->title = 'The Update Journal of Medicaid Service Record';

	$list->SQL = "
		SELECT msl_refid,
	           msl_caption,
	           lastupdate,
	           msl_type_name,
		       msl_text_value_old,
		       msl_text_value_new,
		       umname,
		       msl_type,
		       mss_refid
		FROM (
	        SELECT msl_refid,
	               msl_caption,
	               log.lastupdate,
	               CASE WHEN msl_type = 'U' THEN 'Update'
	                    WHEN msl_type = 'I' THEN 'Insert'
	                    WHEN msl_type = 'D' THEN 'Delete'
	               END AS msl_type_name,
				   CASE WHEN msl_type != 'I'
				        THEN msl_text_value_old
				        ELSE NULL
			       END AS msl_text_value_old,
	               msl_text_value_new,
	               umlastname || ', ' || umfirstname AS umname,
	               msl_type,
	               mss_refid
			  FROM webset.med_std_services_log AS log
			       INNER JOIN public.sys_usermst ON log.umrefid = public.sys_usermst.umrefid
			) AS tt
	    WHERE mss_refid = $mss_refid ADD_SEARCH
	    ORDER BY 1 DESC


    ";


	$list->addSearchField('Date',  'lastupdate::date', 'date_range');

	$list->addSearchField('Action', 'msl_type', 'select')
		->data(
			array(
				'U' => 'Update',
				'I' => 'Insert'
			)
		);

	$list->addSearchField('User', 'umname')
		->append('&nbsp;[Last], [First]');

	$list->addSearchField('Field', 'msl_caption');

	$list->addColumn('Field');

	$list->addColumn('Date')
		->type('date');

	$list->addColumn('Action');

	$list->addColumn('Old Value');

	$list->addColumn('New Value');

	$list->addColumn('User');

	$list->printList();


?>
