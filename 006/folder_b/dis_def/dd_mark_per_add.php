<?php
    Security::init();
    
    $RefID = io::geti('RefID');
    
    $edit = new EditClass('edit1', $RefID);

    $edit->title = 'Add/Edit Building Marking Periods';
    
    $edit->setSourceTable('webset.sch_markperiod', 'bmprefid');

    $edit->addTab('General Information');
    
    $edit->addControl('Building:', 'select')
        ->sqlField('vourefid')
        ->name('vourefid')
        ->sql("
            SELECT vourefid, 
                   vouname
              FROM sys_voumst
             WHERE vndrefid = VNDREFID
             ORDER BY vouname
        ");

    if ($RefID == 0) {
        $edit->addControl('School Year:', 'select')
            ->sqlField('dsyrefid')
            ->name('dsyrefid')
            ->sql("
                SELECT dsyrefid, dsydesc
                  FROM webset.disdef_schoolyear
                 WHERE vndrefid = VNDREFID
                 ORDER BY dsybgdt DESC");
    } else {
        $edit->addControl('School Year:', 'protected')
            ->sqlField('dsyrefid')
            ->name('dsyrefid')
            ->css("display", "none")
            ->append(db::execSQL("
                SELECT dsy.dsydesc
                  FROM webset.sch_markperiod mp
                       INNER JOIN webset.disdef_schoolyear dsy ON dsy.dsyrefid = mp.dsyrefid
                 WHERE bmprefid = ".$RefID)->getOne()
            );
        }

    $edit->addTab('Periods 1-4');
    $edit->addControl('1st Marking Period:')->sqlField('bm1')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy1');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt1')->name('bmbgdt1');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt1')->name('bmendt1');
        
    $edit->addControl('2nd Marking Period:')->sqlField('bm2')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy2');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt2')->name('bmbgdt2');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt2')->name('bmendt2');

    $edit->addControl('3rd Marking Period:')->sqlField('bm3')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy3');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt3')->name('bmbgdt3');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt3')->name('bmendt3');

    $edit->addControl('4th Marking Period:')->sqlField('bm4')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy4');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt4')->name('bmbgdt4');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt4')->name('bmendt4');
    
    $edit->addTab('Periods 5-8');
    $edit->addControl('5th Marking Period:')->sqlField('bm5')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy5');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt5')->name('bmbgdt5');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt5')->name('bmendt5');
    
    $edit->addControl('6th Marking Period:')->sqlField('bm6')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy6');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt6')->name('bmbgdt6');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt6')->name('bmendt6');

    $edit->addControl('7th Marking Period:')->sqlField('bm7')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy7');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt7')->name('bmbgdt7');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt7')->name('bmendt7');

    $edit->addControl('8th Marking Period:')->sqlField('bm8')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy8');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt8')->name('bmbgdt8');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt8')->name('bmendt8');

    $edit->addTab('Periods 9-12');
    $edit->addControl('9th Marking Period:')->sqlField('bm9')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy9');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt9')->name('bmbgdt9');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt9')->name('bmendt9');

    $edit->addControl('10th Marking Period:')->sqlField('bm10')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy10');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt10')->name('bmbgdt10');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt10')->name('bmendt10');

    $edit->addControl('11th Marking Period:')->sqlField('bm11')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy11');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt11')->name('bmbgdt11');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt11')->name('bmendt11');

    $edit->addControl('12th Marking Period:')->sqlField('bm12')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy12');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt12')->name('bmbgdt12');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt12')->name('bmendt12');

    $edit->addTab('Periods 13-16');
    $edit->addControl('13th Marking Period:')->sqlField('bm13')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy13');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt13')->name('bmbgdt13');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt13')->name('bmendt13');

    $edit->addControl('14th Marking Period:')->sqlField('bm14')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy14');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt14')->name('bmbgdt14');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt14')->name('bmendt14');

    $edit->addControl('15th Marking Period:')->sqlField('bm15')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy15');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt15')->name('bmbgdt15');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt15')->name('bmendt15');

    $edit->addControl('16th Marking Period:')->sqlField('bm16')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy16');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt16')->name('bmbgdt16');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt16')->name('bmendt16');

    $edit->addTab('Periods 17-20');
    $edit->addControl('17th Marking Period:')->sqlField('bm17')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy17');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt17')->name('bmbgdt17');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt17')->name('bmendt17');

    $edit->addControl('18th Marking Period:')->sqlField('bm18')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy18');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt18')->name('bmbgdt18');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt18')->name('bmendt18');

    $edit->addControl('19th Marking Period:')->sqlField('bm19')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy19');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt19')->name('bmbgdt19');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt19')->name('bmendt19');
            
    $edit->addControl('20th Marking Period:')->sqlField('bm20')->size(40);        
    $edit->addControl(FFSwitchYN::factory('ESY Period'))->sqlField('esy20');
    $edit->addControl('Begining Date:', 'date')->sqlField('bmbgdt20')->name('bmbgdt20');
    $edit->addControl('Ending Date:', 'date')->sqlField('bmendt20')->name('bmendt20');
    
    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');        
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    
    $edit->addSQLConstraint('Marking period with entered Building and School Year already exists', "
        SELECT 1 
          FROM webset.sch_markperiod                    
         WHERE dsyrefid = '[dsyrefid]' 
           AND vourefid = [vourefid]
           AND bmprefid!=AF_REFID
    ");
                
    $edit->addSQLConstraint('Ending Date of Marking Period #1 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt1]' >= '[bmendt1]' AND '[bmbgdt1]'!= '0' AND '[bmendt1]'!= '0'
    ");

    $edit->addSQLConstraint('Ending Date of Marking Period #2 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt2]' >= '[bmendt2]' AND '[bmbgdt2]'!= '0' AND '[bmendt2]'!= '0'
    ");
            
    $edit->addSQLConstraint('Ending Date of Marking Period #3 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt3]' >= '[bmendt3]' AND '[bmbgdt3]'!= '0' AND '[bmendt3]'!= '0'
    ");

    $edit->addSQLConstraint('Ending Date of Marking Period #4 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt4]' >= '[bmendt4]' AND '[bmbgdt4]'!= '0' AND '[bmendt4]'!= '0'"
    );

    $edit->addSQLConstraint('Ending Date of Marking Period #5 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt5]' >= '[bmendt5]' AND '[bmbgdt5]'!= '0' AND '[bmendt5]'!= '0'
    ");

    $edit->addSQLConstraint('Ending Date of Marking Period #6 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt6]' >= '[bmendt6]' AND '[bmbgdt6]'!= '0' AND '[bmendt6]'!= '0'
    ");

    $edit->addSQLConstraint('Ending Date of Marking Period #7 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt7]' >= '[bmendt7]' AND '[bmbgdt7]'!= '0' AND '[bmendt7]'!= '0'
    ");

    $edit->addSQLConstraint('Ending Date of Marking Period #8 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt8]' >= '[bmendt8]' AND '[bmbgdt8]'!= '0' AND '[bmendt8]'!= '0'
    ");

    $edit->addSQLConstraint('Ending Date of Marking Period #9 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt9]' >= '[bmendt9]' AND '[bmbgdt9]'!= '0' AND '[bmendt9]'!= '0'
    ");

    $edit->addSQLConstraint('Ending Date of Marking Period #10 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt10]' >= '[bmendt10]' AND '[bmbgdt10]'!= '0' AND '[bmendt10]'!= '0'
    ");
            
    $edit->addSQLConstraint('Ending Date of Marking Period #11 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt11]' >= '[bmendt11]' AND '[bmbgdt11]'!= '0' AND '[bmendt11]'!= '0'
    ");

    $edit->addSQLConstraint('Ending Date of Marking Period #12 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt12]' >= '[bmendt12]' AND '[bmbgdt12]'!= '0' AND '[bmendt12]'!= '0'
    ");
            
    $edit->addSQLConstraint('Ending Date of Marking Period #13 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt13]' >= '[bmendt13]' AND '[bmbgdt13]'!= '0' AND '[bmendt13]'!= '0'
    ");

    $edit->addSQLConstraint('Ending Date of Marking Period #14 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt14]' >= '[bmendt14]' AND '[bmbgdt14]'!= '0' AND '[bmendt14]'!= '0'
    ");

    $edit->addSQLConstraint('Ending Date of Marking Period #15 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt15]' >= '[bmendt15]' AND '[bmbgdt15]'!= '0' AND '[bmendt15]'!= '0'
    ");

    $edit->addSQLConstraint('Ending Date of Marking Period #16 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt16]' >= '[bmendt16]' AND '[bmbgdt16]'!= '0' AND '[bmendt16]'!= '0'
    ");

    $edit->addSQLConstraint('Ending Date of Marking Period #17 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt17]' >= '[bmendt17]' AND '[bmbgdt17]'!= '0' AND '[bmendt17]'!= '0'
    ");

    $edit->addSQLConstraint('Ending Date of Marking Period #18 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt18]' >= '[bmendt18]' AND '[bmbgdt18]'!= '0' AND '[bmendt18]'!= '0'
    ");

    $edit->addSQLConstraint('Ending Date of Marking Period #19 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt19]' >= '[bmendt19]' AND '[bmbgdt19]'!= '0' AND '[bmendt19]'!= '0'
    ");

    $edit->addSQLConstraint('Ending Date of Marking Period #20 should be greater than Begining Date', 
        "
        SELECT 1 WHERE '[bmbgdt20]' >= '[bmendt20]' AND '[bmbgdt20]'!= '0' AND '[bmendt20]'!= '0'
    ");

    $edit->finishURL = 'dd_mark_per.php';
    $edit->cancelURL = 'dd_mark_per.php';

    $edit->printEdit();  
?>
