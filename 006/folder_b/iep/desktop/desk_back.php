<?php
    Security::init();
	$url = CoreUtils::getURL('/applications/webset/iep/wrk_stdmgr_menu_back.php', $_GET);
	io::jsVar('back_url', $url);
?>
<script type="text/javascript">
	api.goto(back_url);
</script>