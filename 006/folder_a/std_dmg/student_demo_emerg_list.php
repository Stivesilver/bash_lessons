<?php
	
	Security::init(PHP_NOTICE_ON);
	
	$list = new ListClass('list1');
	
	$list->title = 'Student Emergency Contact Information';
	
	$list->SQL = "select    ec_refid,
							contact_order,
	                        contact_name,
	                        gtdesc,
	                        primary_language,
	                        may_pickup_sw,
	                      	ec_illness_inform_sw
	                      from (  SELECT t_1.ec_refid,
	                              t_1.gtrefid,
	                              t_1.stdrefid,
	                              t_1.ec_lname||', '||t_1.ec_fname as contact_name,
	                              t_1.lastuser,
	                              t_1.lastupdate,
	                              t_2.gtdesc,
	                              t_3.adesc as primary_language,contact_order,
	                      		  may_pickup_sw,
	                      		  ec_illness_inform_sw
	                       FROM c_manager.ec_contact t_1
	                            join webset.def_guardiantype t_2
	           on t_1.gtrefid = t_2.gtrefid
	           left join webset.statedef_prim_lang t_3
	           	on t_1.ec_primary_language_refid = t_3.refid
	           ) as main_list_tabel where  stdrefid = " . $_GET["stdRefID"] . " order by contact_order,contact_name";
	
	$list->addColumn("Con. Ord.");    
	$list->addColumn("Emergency Contact");    
	$list->addColumn("Relationship");    
	$list->addColumn("Prim. Lang.");    
	$list->addColumn("May pickup","","SWITCH");    
	$list->addColumn("Illness","","SWITCH");    
	
	$list->printList();
?>
