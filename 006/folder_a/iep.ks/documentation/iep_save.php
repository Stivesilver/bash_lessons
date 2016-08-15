<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');

    $filename = SystemCore::$secDisk . '/Iep/iep_' . $tsRefID . '_' . date('mdYhis') . '.pdf';
    rename(SystemCore::$physicalRoot . "/uplinkos/temp/IEP_" . $tsRefID . '.pdf', $filename);

    if (!file_exists($filename)) {
        Security::init();
        io::err('Sorry. IEP was not archived. Please Re-Build IEP and save again.', true);
    } else {
        $pdf_cont = base64_encode(file_get_contents($filename));

        #Add IEP
        DBImportRecord::factory('webset.std_iep', 'siepmrefid')
            ->set('pdf_cont', $pdf_cont)
            ->set('stdrefid', $tsRefID)
            ->set('siepmtrefid', io::geti('IEPType'))
            ->set('rptype', '(SELECT doctype FROM webset.sped_doctype WHERE drefid = ' . io::geti('ReportType') . ')', true)
            ->set('siepmdocdate', io::get('IEPDate'))
            ->set('siepmdocfilenm', basename($filename))
            ->set('iepyear', $stdIEPYear)
            ->set('stdiepmeetingdt', '(SELECT stdiepmeetingdt FROM webset.sys_teacherstudentassignment WHERE tsrefid = ' . $tsRefID . ')', true)
            ->set('stdenrolldt', '(SELECT stdenrolldt FROM webset.sys_teacherstudentassignment WHERE tsrefid = ' . $tsRefID . ')', true)
            ->set('stdcmpltdt', '(SELECT stdcmpltdt FROM webset.sys_teacherstudentassignment WHERE tsrefid = ' . $tsRefID . ')', true)
            ->set('stdevaldt', '(SELECT stdevaldt FROM webset.sys_teacherstudentassignment WHERE tsrefid = ' . $tsRefID . ')', true)
            ->set('stdtriennialdt', '(SELECT stdtriennialdt FROM webset.sys_teacherstudentassignment WHERE tsrefid = ' . $tsRefID . ')', true)
            ->set('lastuser', SystemCore::$userUID)
            ->set('lastupdate', 'NOW()', true)
            ->import();
    }

    IDEAStudentEvent::addEvent($tsRefID, '<=.ArchivedIEPDate.=>', date("Y-m-d H:i:s"));

    Header('Location: ' . CoreUtils::getURL('iep_builder.php', array('dskey' => $dskey)));
?>