<?php
	$bothparents = IDEAFormDefaults::factory($defaults['tsRefID'])
		->getValues('ParentsBoth');
	$fvalues = '
		<values>
			<value name="ParentName">' . $bothparents . '</value>
		</values>
	';
?>