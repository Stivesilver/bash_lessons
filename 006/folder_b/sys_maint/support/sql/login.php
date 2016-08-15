<?php
    Security::init();
          
    if (isset($_COOKIE['sql_run_php'])) {
        $umuid      = 'gsupport';
        $umpassword = $_COOKIE['sql_run_php'];
    } else {
        $umuid      = '';
        $umpassword = '';
    }
    
    print UILayout::factory()
        ->newLine()
        ->addObject(FFInput::factory()->name('login')->value($umuid)->caption('Login Name'))
        ->newLine()
        ->addObject(FFInput::factory(FFInput::PASSWORD)->name('pass')->value($umpassword)->caption('Password'))
        ->newLine()
        ->addObject(FFButton::factory()->name('login_button')->caption('Login')
            ->onClick("            
                api.goto(
                    'panel.php',
                    {}, 
                    {'login' : $('#login').val(), 'pass' : $('#pass').val()}
                )
            ")
        )
        ->toHTML();
?>
<script type="text/javascript">
    if ($('#login').val() && $('#pass').val()) {
        $('#login_button').click();
    }
</script>
