<?php
	Security::init();
    $dskey = io::get('dskey', true);
    
    $edit = new EditClass('edit1', io::geti('RefID'));
        
    $edit->title = 'Current Desktop Parameters';

    $edit->addGroup('General Information');

    $edit->addControl('Desktop Title')->sqlField('WINDOWTITLE')->name('WINDOWTITLE')->width('400px');
    $edit->addControl('Desktop Type', 'list')
        ->sqlField('DTYPE')
        ->name('DTYPE')
        ->data(array('R'=>'Standard', 'I'=>'Interactive'));
        
    $edit->addControl('Desktop Font', 'list')
        ->sqlField('BGFONTNAME')
        ->name('BGFONTNAME')
        ->data(array(
                  'Arial'=>'Arial', 
                  'Arial Black'=>'Arial Black',
                  'Arial Narrow'=>'Arial Narrow',
                  'Book Antiqua'=>'Book Antiqua',
                  'Bookman'=>'Bookman', 
                  'Comic Sans MS'=>'Comic Sans MS', 
                  'Courier'=>'Courier',
                  'Impact'=>'Impact',
                  'Monospace'=>'Monospace',
                  'Papyrus'=>'Papyrus',                  
                  'sans-serif'=>'sans-serif',
                  'Tahoma'=>'Tahoma',
                  'Times New Roman'=>'Times New Roman',
                  'Verdana'=>'Verdana',
                  'Verdana,sans-serif'=>'Verdana,sans-serif'
            )
        );   
        
    $edit->addControl('Font Color')->sqlField('BGFONTCOLOR')->name('BGFONTCOLOR');
    $edit->addControl('Font Size', 'integer')->sqlField('BGFONTSIZE')->name('BGFONTSIZE');
    $edit->addControl(FFCheckBox::factory('ToolBar Menu')->data(array('on'=>'')))->sqlField('WIN_TOOLBAR')->name('WIN_TOOLBAR');
    $edit->addControl(FFCheckBox::factory('SlideBar Menu')->data(array('on'=>'')))->sqlField('WIN_ICONBAR')->name('WIN_ICONBAR');    
    $edit->addControl('Desktop Color')->sqlField('BGCOLOR')->name('BGCOLOR');
    $edit->addControl('Wallpaper')->sqlField('BGPICTURE')->name('BGPICTURE')->width('400px');
    $edit->addControl('Alignment', 'list')
        ->sqlField('BGPICTUREALIGN')
        ->name('BGPICTUREALIGN')
        ->data(
            array(
                'Center'=>'Desktop Center',
                'Stretch'=>'Whole Desktop (Stretched)',
                'Tile'=>'Whole Desktop (Tile)',
                'Left-Top'=>'Left Top Corner',
                'Left-Bottom'=>'Left Bottom Corner',
                'Right-Top'=>'Right Top Corner',
                'Right-Bottom'=>'Right Bottom Corner'
            )
        );
    
    $edit->addControl('Window Style', 'list')
        ->sqlField('WIN_SCHEME')
        ->name('WIN_SCHEME')
        ->data(
            array(
                '00'=>'Lumen Classic Red',
                '01'=>'Lumen Classic Blue',
                '02'=>'Lumen Classic Olive',
                '03'=>'Lumen Classic Green',
                '04'=>'Lumen Classic Lilac',
                '05'=>'Lumen Classic Gray',
                '10'=>'Aqua Blue',
                '11'=>'Simple Gray',
                '12'=>'Simple Black'
            )
        );
          
    $edit->addControl('Window Content', 'list')
        ->sqlField('CSSFILE')
        ->name('CSSFILE')
        ->data(
            array(
                '/uplinkos/css/style01.css'=>'01: Red & Yellow',
                '/uplinkos/css/style02.css'=>'02: Blue Color',
                '/uplinkos/css/style03.css'=>'03: Green Color',
                '/uplinkos/css/style04.css'=>'04: Gray & Yellow',
                '/uplinkos/css/style05.css'=>'05: Purple Color',
                '/uplinkos/css/style06.css'=>'06: Brown Color',
                '/uplinkos/css/style07.css'=>'07: Fusia Color',
                '/uplinkos/css/style08.css'=>'08: Black & White',
                '/uplinkos/css/style09.css'=>'09: Blue & Green',
                '/uplinkos/css/style10.css'=>'10: Gray & Yellow',
                '/uplinkos/css/style11.css'=>'11: Deep Red & Gray',
                '/uplinkos/css/style12.css'=>'12: Gray & Purple',
                '/uplinkos/css/style13.css'=>'13: Brown & Sandy',
                '/uplinkos/css/style14.css'=>'14: Blue & Purple',
                '/uplinkos/css/style15.css'=>'15: Green & Yellow',
                '/uplinkos/css/style21.css'=>'16: Blue Car Style',
                '/uplinkos/css//style22.css'=>'22: Olive Car Window CSS',
                '/uplinkos/css//style23.css'=>'23: New Blue & Gray'
            )
        );
        
    $edit->addControl('IC Style', 'list')
        ->sqlField('IC_SCHEME')
        ->name('IC_SCHEME')
        ->data(
            array(
                '00'=>'00: Lumen Classic Red', 
                '01'=>'01: Lumen Classic Blue', 
                '02'=>'02: Lumen Classic Green', 
                '03'=>'03: Lumen Classic Olive', 
                '04'=>'04: Lumen Classic Lilac', 
                '05'=>'05: Lumen Classic Gray', 
                '10'=>'10: Aqua Blue', 
                '11'=>'11: Simple Gray Theme'
            )
        );
          
    $edit->addControl('IC Content', 'list')
        ->sqlField('CSSICFILE')
        ->name('CSSICFILE')
        ->data(
            array(
                '/uplinkos/css/icstyle01.css'=>'00: Blue & Gray', 
                '/uplinkos/css/icstyle21.css'=>'01: Blue', 
                '/uplinkos/css/icstyle19.css'=>'02: Green', 
                '/uplinkos/css/icstyle03.css'=>'03: Olive', 
                '/uplinkos/css/icstyle04.css'=>'04: Lilac', 
                '/uplinkos/css/icstyle05.css'=>'05: Black & White'
            )
        );
    
    $edit->addControl('dskey', 'hidden')->name('dskey')->value($dskey);
	
	$edit->getButton(EditClassButton::SAVE_AND_FINISH)->value('Update Desktops');
    $edit->saveAndAdd = false;
    $edit->saveLocal = false;
    $edit->finishURL = CoreUtils::getURL('zdi_update.php');
    $edit->saveURL = CoreUtils::getURL('zdi_update.php');     
    $edit->cancelURL = 'javascript:api.window.destroy();'; 

    $edit->printEdit();
    
?>
<script type='text/javascript'>
    function changeZDI() {
    	ids = ListClass.get().getSelectedValues().values;
		if (ids == '') {
			api.alert('Please select at least one record');
			return ;
		}
		api.ajax.post(
            'zdi_edit.ajax.php',
            {'ids': ids},
            function(answer) {                
                var wnd = api.window.open('Set Parameters for Select Desktops', api.url('zdi_edit.php', {'dskey' : answer.dskey}));
		        wnd.resize(950, 600);
		        wnd.center();		        
		        wnd.show();
            }
        )
        alert(ids);
    }
    zDesktop = parent.zDesktop;
    
    $('#WINDOWTITLE').val(zDesktop.windowTitle);
    $('#DTYPE').val(zDesktop.dType);
    $('#BGFONTNAME').val(zDesktop.bgFontName);
    $('#BGFONTCOLOR').val(zDesktop.bgFontColor);
    $('#BGFONTSIZE').val(zDesktop.bgFontSize);    
    $('#WIN_TOOLBAR').val(zDesktop.win_toolbar);
    $('#WIN_TOOLBAR').attr('checked', zDesktop.win_toolbar == 'on' ? 'checked' : null);
    $('#WIN_ICONBAR').attr('checked', zDesktop.win_iconbar == 'on' ? 'checked' : null);    
    $('#BGCOLOR').val(zDesktop.bgColor);
    $('#BGPICTURE').val(zDesktop.bgImage);
    $('#BGPICTUREALIGN').val(zDesktop.bgImageAlign);
    $('#WIN_SCHEME').val(zDesktop.win_scheme);
    $('#CSSFILE').val(zDesktop.cssFile);
    $('#IC_SCHEME').val(zDesktop.ic_scheme);
    $('#CSSICFILE').val(zDesktop.cssICFile);
    
</script>