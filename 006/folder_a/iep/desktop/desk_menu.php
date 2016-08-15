<?php
    Security::init();

    $dskey      = io::get('dskey');
    $ds         = DataStorage::factory($dskey);
    $tsRefID    = $ds->safeGet('tsRefID');
    $student    = IDEAStudent::factory($tsRefID);
    $stdIEPYear = $student->get('stdiepyear');
    $apps       = IDEAFormat::getApplications();
    $set_ini    = IDEAFormat::getIniOptions();

    $columns = 4;
    $cols_added = 0;
    $table = UITable::factory();
    for ($i=0; $i<$columns; $i++) {
        $table->addColumn((100/$columns) . '%');
    }
    for ($i=0; $i<count($apps); $i++) {
        #Check whether Student has active IEP year
        $iepYearIsFine = ($stdIEPYear > 0 || $apps[$i]['mdlink'] == $set_ini['iep_year_url']) ? true : false;

		#Remove asterisk in url
		$apps[$i]['mdlink'] = str_replace('*', '', $apps[$i]['mdlink']);

        #Checks link condition file
        $checkData = null;
        $cond_url = str_replace('/applications/webset', '/apps/idea', $apps[$i]['displcondition']);
        if (file_exists(SystemCore::$physicalRoot . $cond_url) && $cond_url != '' ) {
            $checkData = include(SystemCore::$physicalRoot . $cond_url);
            if (isset($checkData['condition']) && !$checkData['condition']) continue;
            if (isset($checkData['link']) && $checkData['link'] != '') {
                $apps[$i]['mdlink'] = $checkData['link'];
            }
            if (isset($checkData['menutext']) && $checkData['menutext'] != '') {
                $apps[$i]['mdmenutext'] = $checkData['menutext'];
            }
        }

        if ($cols_added == 0) $table->addRow('[height: 50px; ]');
        $new_url  = str_replace('/applications/webset', '/apps/idea', $apps[$i]['mdlink']);

        if (!file_exists(SystemCore::$physicalRoot . $apps[$i]['mdicon']) or $apps[$i]['mdicon'] == '') {
            $apps[$i]['mdicon'] = '/applications/webset/icons/mainscreen/additional_information.png';
        }
        $real_url = explode('?', $new_url);
        $real_url = $real_url[0];
        if (file_exists(SystemCore::$physicalRoot . $real_url) && $iepYearIsFine) {
            $cell = UICustomHTML::factory(
                        UILayout::factory()
                            ->newLine('')
                            ->addHTML(FileUtils::getIMGFile(SystemCore::$virtualRoot . $apps[$i]["mdicon"])->toHTML(), '1px [padding: 4px]')
                            ->addHTML($apps[$i]["mdmenutext"], '[padding: 4px]')
                        )

                       ->onClick('api.goto("' . SystemCore::$virtualRoot . $new_url . '", ' . json_encode(array('dskey'=>$dskey)) . ')')
                       ->className('zToolButton')
                       ->css('cursor', 'pointer');
        } else {
            $cell = UICustomHTML::factory(
                        UILayout::factory()
                            ->newLine('')
                            ->addHTML(FileUtils::getIMGFile(SystemCore::$virtualRoot . $apps[$i]["mdicon"])->toHTML(), '1px [padding: 4px]')
                            ->addHTML($apps[$i]["mdmenutext"], '[padding: 4px]')
                        )
                       ->css('color', 'silver')
                       ->css('font-style', 'italic');
        }
        $table->addCell($cell);
        $cols_added++;
        if ($cols_added>=$columns) $cols_added = 0;
    }


    if (!($stdIEPYear > 0)) {
        $message = 'Student has no Active '.$set_ini['iep_year_title'].'. Please create new one.';

        print UIMessage::factory($message, UIMessage::NOTE)
                  ->textAlign('left')
                  ->toHTML();
    }
    print $table->toHTML();
?>
