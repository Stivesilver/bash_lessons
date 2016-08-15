<?
    if (class_exists('IDEACore')) {
	    if (IDEACore::disParam(99) == 'Y') {
	        return true;
	    } else {
	        return false;
	    }
	} else {
	    if (dis_param(99) != 'Y') $condition = 'No';		
	}
?>