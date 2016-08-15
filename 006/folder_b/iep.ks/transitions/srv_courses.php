<?php

    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $RefID = io::get('RefID');

    if ($RefID == '') {
        $list = new ListClass();

        $list->title = 'Proposed District Defined Courses/Experiences to Reach Post Secondary Goal(s)';

        $list->SQL = "
            SELECT tsnsrefid, 
                   dsydesc, 
                   grade.gl_code, 
                   tsndesc
              FROM webset.std_tsn std
                   INNER JOIN webset.disdef_tsn tsn ON tsn.tsnrefid = std.tsndisdefrefid
                   INNER JOIN c_manager.def_grade_levels grade ON SUBSTRING(gldesc, 1, 2) = SUBSTRING(gl_code, 1, 2)                                 
             WHERE stdrefid = " . $tsRefID . "
               AND grade.vndrefid = VNDREFID
            ORDER BY grade.gl_refid, tsnnum, tsndesc
        ";

        $list->addColumn('School Year');
        $list->addColumn('Grade Level');
        $list->addColumn('Proposed Goal Experience');

        $list->deleteTableName = "webset.std_tsn";
        $list->deleteKeyField = "tsnsrefid";

        $list->addURL = CoreUtils::getURL('srv_courses.php', array('dskey' => $dskey));
        $list->editURL = CoreUtils::getURL('srv_courses.php', array('dskey' => $dskey));

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
    } else {

        $edit = new EditClass("edit1", $RefID);

        $edit->title = 'Proposed District Defined Courses/Experiences to Reach Post Secondary Goal(s)';

        $edit->setSourceTable('webset.std_tsn', 'tsnsrefid');

        $edit->addGroup('General Information');

        $edit->addControl('School Year:', 'select')
            ->sqlField('dsydesc')
            ->value(db::execSQL("
                SELECT dsydesc
                  FROM webset.disdef_schoolyear
                 WHERE vndrefid = VNDREFID
                   AND NOW() BETWEEN dsybgdt AND dsyendt
                 ORDER BY dsybgdt DESC
            ")->getOne())
            ->sql("
                SELECT dsydesc, dsydesc
                  FROM webset.disdef_schoolyear
                 WHERE vndrefid = VNDREFID
                 ORDER BY dsybgdt DESC
            ");

        $edit->addControl('Grade Level')
            ->name('gldesc')
            ->sqlField('gldesc')
            ->size(4)
            ->append(FFSelect::factory()
                ->sql("
                   SELECT gl_code, gl_code
                      FROM c_manager.def_grade_levels
                     WHERE gl_refid in (
                               SELECT grrefid
                                 FROM webset.disdef_tsn_grades t1
                                      INNER JOIN webset.disdef_tsn t2 ON t2.tsnrefid = t1.tsnrefid
                                WHERE vndrefid = VNDREFID
                           )
                       AND vndrefid = VNDREFID
                     ORDER BY gl_numeric_value                             
                ")
                ->onChange('$("#gldesc").val(this.value)')
                ->toHTML()
        );

        $edit->addControl('Proposed Goal Experience', 'select')
            ->sqlField('tsndisdefrefid')
            ->sql("
              (SELECT t2.tsnrefid, tsndesc
                 FROM webset.disdef_tsn_grades t1
                      INNER JOIN webset.disdef_tsn t2 ON t2.tsnrefid =  t1.tsnrefid
                WHERE grrefid in (SELECT gl_refid 
                                    FROM c_manager.def_grade_levels 
                                   WHERE vndrefid = VNDREFID
                                     AND gl_code = 'VALUE_01')
                ORDER BY tsnnum, tsndesc) 
          
                UNION ALL
          
               SELECT NULL, '-----------------' 
               
                UNION ALL
              
              (SELECT tsnrefid, tsndesc
                 FROM webset.disdef_tsn 
                WHERE vndrefid = VNDREFID
                  AND tsnrefid not in (SELECT t2.tsnrefid
                                         FROM webset.disdef_tsn_grades t1
                                              INNER JOIN webset.disdef_tsn t2 ON t2.tsnrefid =  t1.tsnrefid
                                        WHERE grrefid in (SELECT gl_refid 
                                                            FROM c_manager.def_grade_levels 
                                                           WHERE vndrefid = VNDREFID
                                                             AND gl_code = 'VALUE_01'))
                ORDER BY tsnnum, tsndesc)
            ")
            ->tie('gldesc')
            ->req();

        $edit->addControl('Details', 'textarea')
            ->sqlField('tsnnarr')
            ->req();

        $edit->addGroup('Update Information', true);
        $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
        $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
        $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

        $edit->finishURL = CoreUtils::getURL('srv_courses.php', array('dskey' => $dskey));
        $edit->cancelURL = CoreUtils::getURL('srv_courses.php', array('dskey' => $dskey));

        $edit->printEdit();
    }
?>