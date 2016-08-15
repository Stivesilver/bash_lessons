<?PHP
	Security::init(NO_OUTPUT | MODE_WS, 1);

	/**
	 * This params determines what to show()
	 *
	 * @var string
	 */
	$mode = io::get('mode');

	switch ($mode) {

	case 'center':
		// create marking periods arrays
		$SQL = "
        SELECT smp.smp_refid,
               smp.smp_period,
               smp.esy,
               smp_active
          FROM webset.sch_marking_period smp
         WHERE vndrefid = 2
         ORDER BY smp_sequens
		";
		$periods = db::execSQL($SQL)->assocAll();
		$a1 = array();
		$a2 = array();
		$a3 = array();
		$a4 = array();
		$a5 = array();
		foreach ($periods as $key => $value) {
			switch ($value['smp_period']) {
			case '1': $a1[] = $value['smp_refid']; break;
			case '2': $a2[] = $value['smp_refid']; break;
			case '3': $a3[] = $value['smp_refid']; break;
			case '4': $a4[] = $value['smp_refid']; break;
			case 'ESY 1': $a5[] = $value['smp_refid']; break;
			}
		}

		function getNewID($oldid, $a1, $a2, $a3, $a4, $a5) {
			$newid1 = 5;
			$newid2 = 6;
			$newid3 = 7;
			$newid4 = 8;
			$newid5 = 51;
			if (in_array($oldid, $a1)) return $newid1;
			if (in_array($oldid, $a2)) return $newid2;
			if (in_array($oldid, $a3)) return $newid3;
			if (in_array($oldid, $a4)) return $newid4;
			if (in_array($oldid, $a5)) return $newid5;
			die('exception: ' . $oldid . ' was not found in marking periods');
		}

		$SQL = "
	      SELECT t.spr_refid,
	             t.sbg_grefid,
	             t.sbb_brefid,
	             t.spr_period_data
	        FROM webset.std_progress_reporting AS t
	       WHERE LENGTH(spr_period_data) > 2
	       ORDER BY t.spr_refid
		";
		$rows = db::execSQL($SQL)->assocAll();
		print '<pre>';
		foreach ($rows as $key => $value) {
			$block = json_decode($value['spr_period_data'], true);
			$newblock = array();
			if (count($block) > 0) {
				foreach ($block as $period => $data) {
					$newid = getNewID($period, $a1, $a2, $a3, $a4, $a5);
					if (isset($newblock[$newid])) {
						if (strlen(serialize($data)) > strlen(serialize($newblock[$newid]))) {
							$newblock[$newid] = $data;
						}
					} else {
						$newblock[$newid] = $data;
					}
				}
				#print_r($value['spr_period_data']);
				#print '<br/>';
				#print_r(json_encode($newblock));
				#print '<hr/>';
				DBImportRecord::factory('webset.std_progress_reporting', 'spr_refid')
					->key('spr_refid', $value['spr_refid'])
					->set('spr_period_data', json_encode($newblock))
					->import();
			}
		}
		print 'job finished';
		break;
 
	case 'hdm':
		$SQL = "
	      SELECT rnumber
	        FROM hdm.hdmrrequestmst rm
	       WHERE rm.vndrefid = 1	        
	         AND rm.rstrefid = 43
	       ORDER BY rm.rrefid DESC
		";
		$tickets = db::execSQL($SQL)->indexCol(0);
		print implode(PHP_EOL, $tickets);
		break;
 
	case 'hdm_spedex':
		$SQL = "
		  SELECT rnumber,
		         rprrefid,
		         rsubject,
		         outsideuseremail,
		         CO.comdesc,
		         substr(rincidentdtl, 1, 5000) AS body
		    FROM hdm.HDMRRequestMst RM
		         INNER JOIN hdm.HDMRequestAssignmentDtl RAD ON RAD.RRefID = RM.RRefID 
		         LEFT OUTER JOIN hdm.hdmpersonnelmanager PM ON (RM.UIDRFRefID = PM.PMRefID AND pm.vndrefid = 1)
		         LEFT OUTER JOIN hdm.hdmcustorgmst CO ON PM.comrefid = CO.comrefid
		   WHERE RM.VndRefID = 1
		     AND RM.RStRefID = 43
		     AND RAD.RADPersonnelRefID = 34
		   ORDER BY RM.RRefID DESC
		";
		$tickets = db::execSQL($SQL)->assocAll();
		$style = 'top [border: 1px solid black; border-collapse: collapse;]';
		$urgents = 0;
		$tbl = '<table border="0">' . PHP_EOL;
		foreach ($tickets as $key => $value) {
			$prior_beg = '';
			$prior_end = '';
			if ($value['rprrefid'] == '29') {
				$prior_beg = '!';
				$prior_end = '';
				$urgents++;
			}
			$value['body'] = preg_replace('/[0-9a-zA-Z+\/]{76}/is', '', $value['body']);
			$value['body'] = preg_replace('/Content-.*/', '', $value['body']);
			$value['body'] = preg_replace('/[a-f0-9-]{30}/is', '', $value['body']);
			$value['body'] = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $value['body']);
			$tbl .= '<tr>' . PHP_EOL;
			$tbl .= '<td valign="top">';
			$tbl .= $value['rnumber'] . $prior_beg . ' ';
			$tbl .= $value['rsubject'] . ' ';
			$tbl .= ($value['comdesc'] ? '[' . $value['comdesc'] . ']' : '') . '<br/>';
			$tbl .= $value['outsideuseremail'] .  $prior_end . ' <br/>';
			$tbl .= '<pre>' . $value['body'] . '</pre>';
			$tbl .= '</td>';
			$tbl .= '</tr>' . PHP_EOL;
		}
		$tbl .= '</table>' . PHP_EOL;
		$tbl = '
			Total: ' . count($tickets) . ' Urgent: ' . $urgents . $tbl;

		print CryptClass::factory()->encode($tbl);
		break;

	case 'slist':
		$profile = (int)io::geti('profile');
		if ($profile > 0) 
			$where = "AND EXISTS (SELECT 1 FROM lightbulb.ce_profile_servers AS p WHERE p.cep_refid = $profile AND p.srefid = s.srefid)";
		else
			$where = "";
		$SQL = "
			SELECT s.srefid,
			       s.shost,
			       s.sname,
				   s.sdocumentroot,
				   s.sdbname,
				   s.svirtualroot
			  FROM global.gl_servers AS s
			 WHERE COALESCE(is_active, 'Y') = 'Y' $where
			 ORDER BY sname
		";
		$servers = db::execSQL($SQL)->assocAll();
		$lines = "";
		foreach ($servers as $key => $server) {
			$lines .= sprintf( "%s\t%s\t%s\t%s\t%s\t%s\t\n",
				$server['srefid'],
				preg_replace('/http[s]{0,1}:\/\/([a-z0-9.-]*).*/', '\\1', $server['shost']),
				$server['sname'],
				$server['sdocumentroot'],
				$server['sdbname'],
				$server['svirtualroot']
			);
		}
		//print $lines;
		print CryptClass::factory()->encode($lines);
		break;
	case 'rcs':
		$doc = RCDocument::factory();
		$doc->setPageFooter(
			RCLayout::factory()
				->addText('ED620, Revised December  2015', '[width: 230px;]')
				->addText('INDIVIDUALIZED EDUCATION PROGRAM', 'center')
				->addText('[PN]', '[width: 230px;] right')
			, '[font-size: 6px;]'
		);
		$doc->addText('<b>Hello World!</b>');
		$doc->startNewPage(false, null, false, 1);
		$doc->setPageFooter(
			RCLayout::factory()
				->addText('Drugay Est', '[width: 230px;]')
				->addText('INDIVIDUALIZED EDUCATION PROGRAM', 'center')
				->addText('10', '[width: 230px;] right')
			, '[font-size: 6px;]'
		);
		$doc->newline();
		$doc->addText('<b>Hello World 2!</b>');
		$doc->output();
		break;

	case 'check':
		$doc = RCDocument::factory();
		$tbl = RCTable::factory('[height: 3px; width: 10px;]')
			->border(1)
			->cellPadding(0)
			->addCell();

		$tbl_in = RCTable::factory('[height: 3px; width: 6px;]')
			->cellPadding(0)
			->addCell('', '[background: #777; font-size: 5px;]');

		$tbl_out = RCTable::factory('[height: 15px; width: 10px;]')
			->border(1)
			->cellPadding(2)
			->addCell($tbl_in, 'center');

		$doc->addObject($tbl);
		$doc->newline();
		$doc->addObject($tbl_out);

		$doc->output();
		break;

		/**
		 * Show PDF for needed block of MO IEP Builder
		 *
		 */
	case 'red':

		//642 	Parent Notification Regarding Results 
		//641 	Team Conclusions and Decisions 
		//640 	Summary 
		//639 	General Information 
		//638 	Disability/Services 
		$reddoc = new IDEABlockRED();
		$block = IDEADocumentBlock::getBlockByID(642);
		$reddoc->setSelectedBlocks($block);
		$reddoc->setRcDoc();
		$reddoc->setStd(33481);
		$reddoc->addBlocks(false);
		$path = $reddoc->getRCDoc()->output();
		break;

	case 'mo_er':
		$er = new IDEABlockEval();
		$er->setSelectedBlocks(IDEADocumentType::factory(IDEABlockBuilder::ER)->getBlocks());
		$er->setRcDoc();
		$er->setStd(33481);
		$er->addBlocks(false);
		$path = $er->getRCDoc()->output();
		break;

	/**
	 * IDEAInstall Get
	 *
	 */
	case 'install_get': 
		print '<pre>' . IDEAInstall::factory('spedex')
			->setPhRoot(SystemCore::$physicalRoot)
			->addDir('apps/idea')
			->addDir('applications/webset/advancement')
			->setSecRoot(SystemCore::$secDisk)
			->addDirSec('IEP')
			->setDBName(SystemCore::$DBName)
			->setDBUser(SystemCore::$DBLogin)
			->addDBSchema('global')
			->addDBTableStructure('webset.adv_defaultparams')
			->addDBTableData('public.us_schedule')
			->addXMLTask(
				'<template>
					<tables>
						<public.sys_vndmst>
							<c_manager.def_grade_levels parent_id="vndrefid" />
						</public.sys_vndmst>
					</tables>
				</template>', 
				2, 
				'SELECT VNDREFID'
			)
			->addFinalSQL("SELECT * from sys_vndmst WHERE vndrefid=VNDREFID")
			->getScriptGetContent();
		break;

	/**
	 * IDEAInstall Put
	 *
	 */
	case 'install_put': 
		print '<pre>' . IDEAInstall::factory('spedex')
			->setPhRoot(SystemCore::$physicalRoot)
			->setSecRoot(SystemCore::$secDisk)
			->setDBName(SystemCore::$DBName)
			->setDBUser(SystemCore::$DBLogin)
			->setVndrefid(1)
			->getScriptPutContent();
		break;

		/**
		 * Show PDF for needed block of MO IEP Builder
		 *
		 */
	case 'mo_builder':

		require_once(SystemCore::$physicalRoot . "/uplinkos/classes/pdfClass.v2.0.php");
		require_once(SystemCore::$physicalRoot . "/uplinkos/classes/lib_sysparam.php");
		require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");
		require_once(SystemCore::$physicalRoot . "/applications/webset/iep.mo/documentation/builder_core/IEPDates.php");
		require_once(SystemCore::$physicalRoot . "/applications/webset/iep.mo/documentation/builder_core/XMLTemplates.php");

		$backgr_color = "c0c0c0";

		$content = '<doc>';

		$content .= get_block(33, 33481, 1010870, false, array('str'=>''));

		$content .= "</doc>";

		//die($content);

		$doc = new xmldoc();
		$doc->edit_mode = "no";
		$doc->xml_data = $content;

		//xml file create
		$xml_file = 'test.xml';
		file_put_contents(SystemCore::$tempPhysicalRoot . '/'.  $xml_file, $doc->xml_data);

		$file_name = $doc->getpdf();

		$path = $_SERVER['DOCUMENT_ROOT'] . $file_name;
		$pdfcont = file_get_contents($path);

		$len = strlen($pdfcont);
		header("content-type: application/pdf");
		header("content-transfer-encoding: binary");
		header('cache-control: maxage=3600'); //adjust maxage appropriately
		header('pragma: public');
		header("content-length: $len");
		header("content-disposition: inline; filename=" . basename($path));

		print $pdfcont;
		break;

	case 'screenshot':
		$screen_file = SystemCore::$tempPhysicalRoot . '/'.  "snapshot1.png";
		if (file_exists($screen_file)) {
			$im = imagecreatefrompng($screen_file);
			header('Content-Type: image/png');
			header('Cache-Control: no-cache, must-revalidate'); 
			header('Expires: Fri, 31 Mar 2000 17:00:00 GMT'); 
			imagepng($im);
			imagedestroy($im);
		} else {
			die("screenshot file does not exist");
		}
		break;

	case 'blocks':
		$doc = IDEADocumentType::factory(IDEABlockBuilder::ER);
		$blocks = $doc->getBlocks();
		$block = $blocks[0];
		io::trace($blocks);
		$block = IDEADocumentBlock::getBlockByID(1380);
		io::trace($block);
		break;

	case 'tn_builder':
		$ids = io::get('block')? io::get('block') : '1366,1367,1368,1369,1370,1371,1372,1373,1374';
		$block = new IDEABlockTN();
		$block->setSelectedBlocks($ids);
		$block->setRcDoc(RCPageFormat::LANDSCAPE);
		$block->setStd(33481);
		$block->addBlocks(false);
		$path = $block->getRCDoc()->output();

		$PDFcont = file_get_contents($path);
		break;

	case 'ct_builder':
		$er = new IDEABlockCT();
		$er->setSelectedBlocks(IDEADocumentType::factory(IDEABlockBuilder::CT)->getBlocks(1267));
		$er->setRcDoc();
		$er->setStd(33481);
		$er->setRcDoc(RCPageFormat::LANDSCAPE);
		$er->addBlocks(false);
		$path = $er->getRCDoc()->output();
		break;
		break;
	}

?>
