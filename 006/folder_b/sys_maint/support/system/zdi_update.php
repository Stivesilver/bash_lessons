<?php
    Security::init();

    $ds = DataStorage::factory(io::post('dskey', true));
    $urls = $ds->get('ids');

    foreach ($urls as $url) {
        if ($url != '') {
            $desktop = IDEAZdi::factory(CoreUtils::getPhysicalPath($url))
                ->setParam('WINDOW', 'WIN_SCHEME', io::post('WIN_SCHEME'))
                ->setParam('WINDOW', 'IC_SCHEME', io::post('IC_SCHEME'))
                ->setParam('WINDOW', 'WIN_TOOLBAR', (io::post('WIN_TOOLBAR') == 'on' ? 'on' : 'off'))
                ->setParam('WINDOW', 'WIN_ICONBAR', (io::post('WIN_ICONBAR') == 'on' ? 'on' : 'off'))
                ->setParam('DESKTOP', 'WINDOWTITLE', io::post('WINDOWTITLE'))
                ->setParam('DESKTOP', 'DTYPE', io::post('DTYPE'))
                ->setParam('DESKTOP', 'BGFONTNAME', io::post('BGFONTNAME'))
                ->setParam('DESKTOP', 'BGFONTCOLOR', io::post('BGFONTCOLOR'))
                ->setParam('DESKTOP', 'BGFONTSIZE', io::post('BGFONTSIZE'))
                ->setParam('DESKTOP', 'BGCOLOR', io::post('BGCOLOR'))
                ->setParam('DESKTOP', 'BGPICTURE', io::post('BGPICTURE'))
                ->setParam('DESKTOP', 'BGPICTUREALIGN', io::post('BGPICTUREALIGN'))
                ->setParam('DESKTOP', 'CSSFILE', io::post('CSSFILE'))
                ->setParam('DESKTOP', 'CSSICFILE', io::post('CSSICFILE'))                 
                ->updateFile();
        }
    }
?>
<script type='text/javascript'>
    api.window.dispatchEvent('desktops_updated');
    api.window.destroy();
</script>