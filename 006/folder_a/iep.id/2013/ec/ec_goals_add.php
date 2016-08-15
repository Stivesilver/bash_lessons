<?php

	Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$outcome = io::geti('outcome', true);
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	$area_id = IDEAAppArea::ID_EC_GOALS;
	
	$previous = db::execSQL("
	    SELECT *
		  FROM webset.std_general std					   
	      WHERE stdrefid = " . $tsRefID . "
		    AND iepyear = " . $stdIEPYear . "
		    AND area_id = " . $area_id . "
		    AND int01 = " . $outcome . "
	 	  ORDER BY 1 DESC 
	")->assoc();

	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit Annual Goals';
	$edit->saveAndEdit = true;

	$edit->setSourceTable('webset.std_general', 'refid');

	$edit->addTab('General Information');
	$edit->addControl("Order #", "integer")
		->sqlField('order_num')
		->value((int) db::execSQL("
				SELECT max(order_num)
				  FROM webset.std_general std					   
				 WHERE stdrefid = " . $tsRefID . "
				   AND iepyear = " . $stdIEPYear . "
				   AND area_id = " . $area_id . "
				   AND int01 = " . $outcome . "
                ")->getOne() + 1
		)
		->size(5);

	$edit->addControl('Describe the child\'s baseline performance', 'textarea')
		->sqlField('txt06')
		->css('width', '95%')
		->css('height', '50px')
		->autoHeight(true)
		->help('Describe the child\'s baseline performance for the annual goal (s) and how participation in pre-academic and non-academic activities and routines is adversely affected.');

	$edit->addControl('General Education Content Standard(s)', 'textarea')
		->sqlField('txt01')
		->css('width', '95%')
		->css('height', '50px')
		->autoHeight(true)
		->help('List all of the Idaho eGuidelines standards that related to the Annual goal(s) of need');

	$edit->addControl('Annual Goal', 'textarea')
		->sqlField('txt02')
		->css('width', '95%')
		->css('height', '50px')
		->autoHeight(true)
		->help('Specific measurable skill(s) and the condition that wouldindicate improved functioningin general education curriculum and setting related to this outcome.')
		->req();

	$edit->addControl('Evaluation Procedure', 'textarea')
		->sqlField('txt03')		
		->css('width', '95%')
		->css('height', '50px')
		->autoHeight(true)
		->help('criteria, procedure, and schedule');

	$edit->addControl('Assistive Technology (if needed)', 'textarea')
		->sqlField('txt04')
		->value($previous['txt04'])
		->css('width', '100%')
		->css('height', '50px')
		->autoHeight(true);

	$edit->addControl('How and When Progress Toward Goal Is Reported', 'textarea')
		->sqlField('txt05')
		->value($previous['txt05'])
		->css('width', '100%')
		->css('height', '50px')
		->autoHeight(true);

	if ($RefID == 0) {
		$edit->addControl('Report Card', 'protected')
			->append(UIMessage::factory('Please save current Outcome record before adding Report Card', UIMessage::NOTE)->toHTML());
	} else {
		$edit->addControl('Report Card', 'protected')
			->append(
				UIAnchor::factory('Report Card')
					->onClick('api.window.open("Report Card", "' . 
						CoreUtils::getURL(
							'/apps/idea/iep/constructions/main.php', 
							array(
								'constr' => '155', 
								'cansel' => 'destroy', 
								'print' => 'no', 
								'top' => 'no', 
								'dskey' => $dskey, 
								'other_id' => $RefID
							)
						) . '")'
					)
			);
	}
	
	
	$edit->addTab('Objectives');
	$edit->addIFrame(CoreUtils::getURL('ec_objectives.php', array('dskey' => $dskey, 'goal' => $RefID)))->height('300');

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
	$edit->addControl('IEP Year ID', 'hidden')->value($stdIEPYear)->sqlField('iepyear');
	$edit->addControl("Area ID", "hidden")->value($area_id)->sqlField('area_id');
	$edit->addControl("Outcome ID", "hidden")->value($outcome)->sqlField('int01');

	$edit->finishURL = CoreUtils::getURL('ec_goals.php', array('dskey' => $dskey, 'outcome' => $outcome));
	$edit->cancelURL = CoreUtils::getURL('ec_goals.php', array('dskey' => $dskey, 'outcome' => $outcome));

	$edit->printEdit();
?>

<script type="text/javascript">

		function editForm(state_id, std_id) {
			url = api.url('el_eligibility_form_edit.ajax.php');
			api.ajax.post(
				url,
				{
					'state_id': state_id,
					'std_id': std_id,
					'dskey': $('#dskey').val()
				},
			function(answer) {
				win = api.window.open(answer.caption, answer.url);
				win.maximize();
				win.addEventListener(WindowEvent.CLOSE, formCompleted);
				win.show();
			}
			);
		}

		function formCompleted() {
			var edit1 = EditClass.get();
			edit1.saveAndEdit()
		}
</script>
