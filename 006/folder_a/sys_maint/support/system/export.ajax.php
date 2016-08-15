<?php
	Security::init();

    $ideaData = IDEAData::factory();
    $data['xml_data'] = $ideaData->xmlExport(
	    $_POST['template'],
	    io::post('root_id'),
	    io::post('show_sql')
    );

	if (io::post('show_sql') == 'Y') {
		$data['xml_del'] = $ideaData->getDeleteRows();
		$data['xml_insert'] = $ideaData->getInsertRows();
		$data['xml_update'] = $ideaData->getUpdateRows();
		io::ajax('xml_del', $data['xml_del']);
		io::ajax('xml_insert', $data['xml_insert']);
		io::ajax('xml_update', $data['xml_update']);
	} else {
		io::ajax('xml_del', '');
		io::ajax('xml_insert', '');
		io::ajax('xml_update', '');
	}

    io::ajax('xml_data', $data['xml_data']);

?>
