<?php
    $parts = db::execSQL("
        SELECT spirefid ,
               participantname,
               participantrole,
               participantatttype,
               std_seq_num
          FROM webset.std_iepparticipants
         WHERE stdrefid = " . $defaults['tsRefID'] ."
    ")->assocAll();

	$lea = '';
	$interpret = '';
	$genteacher = '';

    foreach( $parts as $part) {
	    switch ($part['participantrole']) {
		    case '*LEA Representative':
			    $lea 		= $part['participantname'];
			    break;
		    case '*Individual Interpreting Instructional Implications of Evaluation Results':
			    $interpret  = $part['participantname'];
			    break;
		    case '*Regular Classroom Teacher':
			    $genteacher = $part['participantname'];
			    break;
	    }
    }

	$bothparents = IDEAFormDefaults::factory($defaults['tsRefID'])
		->getValues('ParentsBoth');

	$fvalues = '
		<values>
			<value name="LocEdAg">' . $lea . '</value>
			<value name="linterpreterderesult">' . $interpret . '</value>
			<value name="teachername">' . $genteacher . '</value>
			<value name="ParentName">' . $bothparents . '</value>
			<value name="Parents">' . $bothparents . '</value>
			<value name="AgencyRepTrois"></value>
		</values>
	';
?>