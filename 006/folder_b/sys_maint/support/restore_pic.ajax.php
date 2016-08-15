<?php

	Security::init();

	$selValSrt = io::get('selVal');
	$selVal = explode(',', $selValSrt);
	foreach ($selVal AS $val) {
		$sar = array();
		$content = db::execSQL("
				SELECT siepmrefid,
				       xml_cont,
				       iepyear,
				       stdiep.stdrefid
				  FROM webset.std_iep AS stdiep
				 WHERE siepmrefid = $val
			")->assocAll();
		$content = $content[0];

		$xml = simplexml_load_string(base64_decode($content['xml_cont']));

		foreach ($xml->xpath('//tr') as $tr) {
			$img = '';
			$nowtr = simplexml_load_string($tr->asXML());
			foreach ($nowtr->xpath('//img') as $image) {
				$attributes = $image->attributes();
				$img = (string)$attributes['src'];
			}
			if ($img != '') {
				foreach ($nowtr->xpath('//i') as $name) {
					$name = $name->asXML();
					$name = substr($name, 3);
					$name = substr($name, 0, -4);
				}
				$base64png = db::execSQL("
						SELECT signature
			              FROM webset.std_iepparticipants
			             WHERE stdrefid = " . $content['stdrefid'] . "
			               AND participantname = '" . db::escape($name) . "'
			               AND iep_year = " . $content['iepyear'] . "
					")->getOne();

				if ($base64png) {
					$sinaturePNGFile = tempFileName("png");
					file_put_contents($sinaturePNGFile, base64_decode($base64png));
					$sinatureJPGFile = tempFileName("jpg");
					png2jpg($sinaturePNGFile, $sinatureJPGFile, '75');
					$imgbinary = fread(fopen($sinatureJPGFile, "r"), filesize($sinatureJPGFile));
					$jpg_base64 = base64_encode($imgbinary);
					$sar[$img] = $jpg_base64;
				}
			}
		}

		$nxml = base64_decode($content['xml_cont']);
		foreach ($sar as $img => $code) {
			$nxml = str_replace($img, "data:image/jpg;base64," . $code, $nxml);
		}
		$nxml = base64_encode($nxml);

		DBImportRecord::factory('webset.std_iep')
			->key('siepmrefid', $val)
			->set('xml_cont', $nxml)
			->import(DBImportRecord::UPDATE_ONLY);
	}

	function tempFileName($extension = "pdf") {
		return SystemCore::$physicalRoot . "/uplinkos/temp/" . md5(uniqid(rand())) . "." . $extension;
	}

	function png2jpg($originalFile, $outputFile, $quality) {
		$image = imagecreatefrompng($originalFile);

		$image2 = imagecreatetruecolor(imagesx($image), imagesy($image));

		imagealphablending($image, false);
		imagesavealpha($image, true);
		imagefilledrectangle($image2, 0, 0, imagesx($image), imagesy($image), imagecolorallocate($image2, 255, 255, 255));
		imagecopy($image2, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
		imagealphablending($image2, true);
		imagejpeg($image2, $outputFile, $quality);

		imagedestroy($image);
		imagedestroy($image2);
	}

?>
