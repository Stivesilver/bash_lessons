<?php

  	Security::init();

  	$list    	= new ListClass();
  	$dskey   	= io::get('dskey');
	$ds 	 	= DataStorage::factory($dskey, true);
	$tsRefId 	= $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$editUrl    = CoreUtils::getURL('srv_srv_edit.php', array('dskey' => $dskey));
	$path = '/apps/idea/iep.id/2008/services/by_year_services_list.php';

  	$list->customSearch     = "yes";
	$list->showSearchFields = "yes";
	$list->title 			= "Services";
	$list->deleteTableName  = 'webset.std_srv_sped';
	$list->deleteKeyField   = 'ssmrefid';
	$list->addURL 			= $list->editURL = $editUrl;
  	$list->SQL 				= "
  		SELECT webset.std_srv_sped.ssmrefid,
               order_num,
               stsdesc,
               sfdesc,
               CASE WHEN round(hours) = hours THEN round(hours) else round(hours,1) END,
               minutes,
               COALESCE(webset.disdef_location.crtdesc, '') || CASE WHEN crtnarrsw = 'Y' THEN ': ' || COALESCE(ssmclasstypenarr, '') ELSE '' END as location,
               ssmteacherother,
               ssmbegdate,
               ssmenddate,
               nasw
          FROM webset.std_srv_sped
	           INNER JOIN webset.statedef_services_sped ON webset.std_srv_sped.stsrefid = webset.statedef_services_sped.stsrefid
	           LEFT JOIN webset.disdef_frequency ON webset.std_srv_sped.ssmfreq = webset.disdef_frequency.sfrefid
	           LEFT JOIN webset.disdef_location ON webset.std_srv_sped.ssmclasstype = webset.disdef_location.crtrefid
          	   LEFT OUTER JOIN public.sys_usermst ON webset.std_srv_sped.umrefid = public.sys_usermst.umrefid
         WHERE stdrefid = " . $tsRefId . "
           AND iepyear = " . $stdIEPYear . "
         ORDER BY order_num, ssmrefid
  		";

  	$list->addColumn("Order #");
    $list->addColumn("Service");
    $list->addColumn("Frequency")->dataCallback('srvList');
    $list->addColumn("Hours")->dataCallback('srvList');
    $list->addColumn("Minutes")->dataCallback('srvList');
    $list->addColumn("Location")->dataCallback('srvList');
    $list->addColumn("Position")->dataCallback('srvList');
    $list->addColumn("Start Date")->dataCallback('srvList')->type('date');
    $list->addColumn("End Date")->dataCallback('srvList')->type('date');

	$list->addRecordsResequence(
		'webset.std_srv_sped',
		'order_num'
	);

	$button = new IDEAPopulateIEPYear($dskey, null, $path);
	$listButton = $button->getPopulateButton();
	$list->addButton($listButton);

    $list->addButton(
      	  FFIDEAExportButton::factory()
          	  ->setTable($list->deleteTableName)
              ->setKeyField($list->deleteKeyField)
              ->applyListClassMode()
    );

    $list->addButton(
        IDEAFormat::getPrintButton(array('dskey' => $dskey))
    );

  	$list->printList();

  	function srvList($data, $col) {
  		if ($data['nasw'] == 'Y') {
			$return = '';
  		} else {
			$return = $data[$col];
  		}

		return $return;
    }


?>
