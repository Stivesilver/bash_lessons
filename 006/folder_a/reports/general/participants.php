<?php

    Security::init();

    $list = new listClass();
    $list->title = 'IEP Participants';
    $list->showSearchFields = true;
    $list->printable = true;

    $state = VNDState::factory()->code;

    switch ($state) {

        case 'TX':
            $inner = IDEAParts::get('iepYearJoin') . "
                INNER JOIN webset.std_iepparticipants parts ON ts.tsrefid = parts.stdrefid AND iep_year = siymrefid AND COALESCE(docarea, 'A') = 'A'
            ";
            break;

        case 'ID':
            $inner = "
                INNER JOIN webset.std_iepparticipants parts ON ts.tsrefid = parts.stdrefid AND COALESCE(docarea, 'I') = 'I'
            ";
            break;

        case 'MO':
            $inner = IDEAParts::get('iepYearJoin') . "
                INNER JOIN webset.std_iepparticipants parts ON parts.iep_year = iepyear.siymrefid
            ";
            break;

        case 'OH':
            $inner = "
                INNER JOIN webset.std_iepparticipants parts ON ts.tsrefid = parts.stdrefid AND COALESCE(docarea, '') = ''
            ";
            break;

        default:
            $inner = "
                INNER JOIN webset.std_iepparticipants parts ON ts.tsrefid = parts.stdrefid
            ";
            break;
    }

    $list->SQL = "
        SELECT tsrefid,
               " . IDEAParts::get('schoolName') . " AS vouname,
               " . IDEAParts::get('stdname') . " AS stdname,
               stddob,
               gl_code,
               ts.stdiepmeetingdt,
               participantname ,
               participantrole ,
               participantatttype,
               CASE WHEN " . IDEAParts::get('stdActive') . " THEN 'Y' ELSE 'N' END as stdstatus,
               CASE WHEN " . IDEAParts::get('spedActive') . " THEN 'Y' ELSE 'N' END as spedstatus
          FROM webset.sys_teacherstudentassignment AS ts
               " . IDEAParts::get('studentJoin') . "
               " . IDEAParts::get('gradeJoin') . "
               " . IDEAParts::get('schoolJoin') . "
               " . $inner . "
         WHERE std.vndrefid = VNDREFID
         ADD_SEARCH
         ORDER BY 2,3, CASE WHEN substring(participantrole,1,1)='*' THEN 1 ELSE 2 END, std_seq_num, participantname
    ";

    $list->addSearchField(FFStudentName::factory());
    $list->addSearchField(FFIDEASchool::factory())->name('vourefid');
    $list->addSearchField("IEP Meeting Date", "stdiepmeetingdt", "date_range");
    $list->addSearchField('Participant Name', 'participantname')->sqlMatchType(FormFieldMatch::SUBSTRING);
    $list->addSearchField('Role', 'participantrole')->sqlMatchType(FormFieldMatch::SUBSTRING);
    $list->addSearchField('Attendance Type', 'participantatttype')->sqlMatchType(FormFieldMatch::SUBSTRING);
    $list->addSearchField(FFIDEAStdStatus::factory());
    $list->addSearchField(FFIDEASpEdStatus::factory());
    $list->addSearchField(FFGradeLevel::factory())->sqlField('std.gl_refid');

    $list->addColumn('School Name', '', 'group')->sqlField('vouname');
    $list->addColumn('Student Name', '15%')->sqlField('stdname');
    $list->addColumn('Grade', '10%')->sqlField('gl_code');
    $list->addColumn('DOB', '10%')->sqlField('stddob')->type('date');
    $list->addColumn('IEP Meeting', '10%')->sqlField('stdiepmeetingdt')->type('date');
    $list->addColumn('Participant', '15%')->sqlField('participantname');
    $list->addColumn('Role', '20%')->sqlField('participantrole');
    $list->addColumn('Attendance Type', '15%')->sqlField('participantatttype');
    $list->addColumn('Std')->hint('Student Status')->type('switch')->sqlField('stdstatus')->printable(false);
    $list->addColumn('Sp Ed')->hint('Sp Ed Status')->type('switch')->sqlField('spedstatus')->printable(false);

    $list->printList();
?>
