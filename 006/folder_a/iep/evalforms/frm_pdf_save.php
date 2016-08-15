<?php
    Security::init();

    $strUrlEnd = io::post('strUrlEnd');
    preg_match_all('/&mfcrefid=(\d{1,4})/', $strUrlEnd, $mfc_id_arr, PREG_PATTERN_ORDER);
    if (isset($mfc_id_arr[1][0])) {
        $mfcrefid = $mfc_id_arr[1][0];
    } else {
        $mfcrefid = '';
    }

    if ($mfcrefid == '') {
        preg_match_all('/&mfcrefid=(.+?\.fdf)/', $strUrlEnd, $mfc_id_arr, PREG_PATTERN_ORDER);
        $filename = $mfc_id_arr[1][0];
    }

    $tsRefID = substr($strUrlEnd, strpos($strUrlEnd, '?tsRefID=') + 9, strpos($strUrlEnd, '&mfcrefid=') - strpos($strUrlEnd, '?tsRefID=') - 9);

    //Create new form
    if ($tsRefID > 0) {
        $student = IDEAStudent::factory($tsRefID);
        $stdIEPYear = $student->get('stdiepyear');
        $filename = 'Form_' . $tsRefID . '_' . date('mdhis') . '.fdf';

        if ($tsRefID > 0 and $mfcrefid > 0 and $stdIEPYear > 0) {
            DBImportRecord::factory('webset.std_forms', 'smfcrefid')
                ->set('stdrefid', $tsRefID)
                ->set('mfcrefid', $mfcrefid)
                ->set('smfcfilename', $filename)
                ->set('iepyear', $stdIEPYear)
                ->set('fdf_content', base64_encode(IDEAFormPDF::fdf_prepare($_POST, $filename, $mfcrefid)))
                ->set('lastuser', SystemCore::$userUID)
                ->set('lastupdate', 'NOW()', true)
                ->set('smfcdate', 'NOW()', true)
                ->import();
            IDEAStudentEvent::formEvent($tsRefID, $mfcrefid, 'add');
        }
    } else {
        //Edit old form
        $SQL = "
            SELECT mfcrefid,
                   stdrefid
	          FROM webset.std_forms
	         WHERE smfcfilename = '" . $filename . "'
        ";

        $result = db::execSQL($SQL);
        $mfcrefid = $result->fields['mfcrefid'];
        $tsRefID = $result->fields['stdrefid'];

        DBImportRecord::factory('webset.std_forms', 'smfcrefid')
            ->key('smfcfilename', $filename)
            ->set('fdf_content', base64_encode(IDEAFormPDF::fdf_prepare($_POST, $filename, $mfcrefid)))
            ->set('lastuser', SystemCore::$userUID)
            ->set('lastupdate', 'NOW()', true)
            ->set('smfcdate', 'NOW()', true)
            ->import();

        IDEAStudentEvent::formEvent($tsRefID, $mfcrefid, 'update');
    }
?>
<script type="text/javascript">
    if (opener) {
        opener.go_to_list();
    }
    window.close();
</script>