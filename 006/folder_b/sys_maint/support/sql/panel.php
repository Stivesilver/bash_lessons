<?php
	Security::init();

    $password = db::execSQL("
        SELECT umpassword
          FROM sys_usermst
         WHERE umuid = 'gsupport'
         ORDER BY umrefid
         LIMIT 1
    ")->getOne();
    
    if (!(io::post('login') == 'gsupport' && (io::post('pass') == $password || md5(io::post('pass')) == $password))) {
        Header("Location: login.php");
    } else {
        setcookie("sql_run_php", $_POST["pass"], time()+(60*60*24*365));
    }
        
    print UIFrameSet::factory('100%', '5%, 30%, 65%')
        ->addFrame(
            UIFrame::factory()
                ->addObject(
                    UILayout::factory()
                        ->newLine()
                        ->addObject(
                            FFSelect::factory()
                                ->name('dbid')
                                ->sql("
                                    SELECT dbrefid,
                                           dbname || ' (' || dbsqlname || ')' || ' - ' || dbip
                                      FROM dd_databasemst
                                     ORDER BY CASE dbname WHEN 'job_scheduling' THEN 2 ELSE 1 END,  2
                                ")
                                ->hint('Database'),
                            '1% left'    
                        )
                        ->addObject(
                            FFSelect::factory()
                                ->name('vid')
                                ->data(
                                    array(
                                        'table'=>'table',
                                        'insert'=>'insert',
                                        'update'=>'update',
                                        'csv'=>'csv',
                                        'base64'=>'base64'
                                    )
                                )
                                ->hint('Data Format'),
                            '1% left'    
                        )
                        ->addObject(
                            FFInput::factory(1)->name('num')->value(3)->hint('Number of Rows'),
                            '1% left'    
                        )
                        ->addObject(
                            FFCheckBox::factory()->name('dwn')->hint('dwn as File'),
                            '1% left'    
                        )
                        ->addObject(
                            FFButton::factory()
                                ->name('run_button')
                                ->value('Run!!!')
                                ->width('150px')
                                ->onClick("
                                    api.ajax.post(
                                        'panel.ajax.php', 
                                        {'dbid': $('#dbid').val(),                                         
                                         'vid': $('#vid').val(),
                                         'num': $('#num').val(),
                                         'dwn': $('#dwn').val(),
                                         'sql': $('#sql').val()},
                                        function(answer) {                                        
                                           $('#results_frame').html(answer.data)
                                           $('#total').html(answer.total)
                                        }
                                    )
                                "),
                            '1% left'    
                        )        
                        ->addHTML('Total:','90% right')
                        ->addObject(UICustomHTML::factory(0)->id('total'),'1% left')
                )
        )
        ->addFrame(
            UIFrame::factory()
                ->addObject(
                    FFTextArea::factory()
                        ->name('sql')
                        ->width('100%')
                        ->css('height', '210px')
                        ->css('font-size', '12px')
                        ->css('background-color', 'white')
                        ->css('font-family', 'Courier')
                        ->css('font-size', '13px')
                )
        )
        ->addFrame(
            UIFrame::factory()                
                ->addObject(UICustomHTML::factory()
                    ->id('results_frame')
                    ->css('display', '')
                )
                ->scrollable(true, true)
                ->css('background-color', 'white')
                ->indent(2)
        )
        ->toHTML();
	
?>
<script>
    var isCtrl = false;
    document.onkeyup=function(e){
    	if(e.which == 17) isCtrl=false;
    }

    document.onkeydown=function(e){
    	if(e.which == 17) isCtrl=true;
        if(e.which == 119) $('#run_button').click();
        if(e.which == 192 && isCtrl == true) makeItSelect();     
    }

    function setTotal(number) {
        $('#total').html(number);
    }

    function makeItSelect() {
        if (getSelText()!="") $('#sql').val(getSelText());
        $('#sql').val("SELECT * \r\nFROM " + $('#sql').val() + "\r\nWHERE 1=1\r\nORDER BY 1 desc;");
    }
    
    function getSelText(){
        txtarea = $('#sql')[0];
        return (txtarea.value).substring(txtarea.selectionStart,txtarea.selectionEnd);

	}
    
    $('#sql')[0].focus();
</script>