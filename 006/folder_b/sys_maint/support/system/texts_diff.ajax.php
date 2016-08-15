<?php

	Security::init();

	$fTextArr1 = array_unique(str_word_count(io::get('text1'), 1));
	$fTextArr2 = array_unique(str_word_count(io::get('text2'), 1));
	$add = implode(', ', array_diff($fTextArr2, $fTextArr1));
	$del = implode(', ', array_diff($fTextArr1, $fTextArr2));

	$result = '<b>Added:</b> ' . $add . '<br/><br/>' . '<b>Deleted:</b> ' . $del;

	io::ajax('res', $result);
