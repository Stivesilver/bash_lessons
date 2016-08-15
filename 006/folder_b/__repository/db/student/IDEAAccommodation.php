<?php
/**
 * IDEAAccomodation.php
 * Helper for accomodations and assessments. Use for db-queries, check bindings between progmods
 * (assessments and modifications), add relations  etc.
 *
 * Created 22-11-2013. Updated 26-11-2013
 * @author Ganchar Danila <dganchar@lumentouch.com>
 */

class IDEAAccommodation {

	/**
	 * Key student. Use for db-queries
	 *
	 * @var integer
	 */
	protected $tsRefID;

	/**
	 * Accommodations with parametres(ids, keys, titles etc)
	 *
	 * @var array
	 */
	protected $accommodations;

	/**
	 * Sum accommodations
	 *
	 * @var integer
	 */
	protected $countAccommodations;

	/**
	 * Modifications with parametres(ids, keys, titles etc)
	 *
	 * @var array
	 */
	protected $modifications;

	/**
	 * Sum modifications
	 *
	 * @var integer
	 */
	protected $countModifications;

	/**
	 * Accommodations without modifications
	 *
	 * @var array
	 */
	protected $notBinding;

	/**
	 * Sum not binding accommodations
	 *
	 * @var integer
	 */
	protected $countNotBinding;

	/**
	 * Set value to attr obj
	 *
	 * @param string $name
	 * @param $value
	 */
	public function setAttr($name, $value) {
		$this->$name = $value;
	}

	/**
	 * Return value attribute
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function getAttr($name) {
		return $this->$name;
	}

	/**
	 * Get assessments from db
	 *
	 * @return void
	 */
	public function getAccommodations() {
		$SQL = "
			SELECT progmod.ids_assessments,
				   progmod.stsrefid,
				   pcat.macrefid,
				   progmod.macrefid,
				   acat.aacrefid,
				   accomod.aacrefid,
				   accomod.stsrefid,
	               COALESCE(acat.aacdesc || ': ',  CAST(accomod.macrefid AS VARCHAR)) || accomod.stsdesc AS title,
	               COALESCE(pcat.macdesc || ': ', '') || progmod.stsdesc AS title2
	          FROM webset.statedef_mod_acc progmod
	               LEFT OUTER JOIN webset.statedef_mod_acc_cat pcat ON pcat.macrefid = progmod.macrefid
	               LEFT OUTER JOIN webset.statedef_mod_acc accomod ON CAST(progmod.ids_assessments AS INTEGER) = accomod.stsrefid
	               LEFT OUTER JOIN webset.statedef_assess_acc_cat acat ON acat.aacrefid = accomod.aacrefid
	         WHERE progmod.screfid = " . VNDState::factory()->id . "
	           AND progmod.modaccommodationsw = 'Y'
	           AND progmod.ids_assessments IS NOT NULL
	           AND (progmod.recdeactivationdt IS NULL or now()< progmod.recdeactivationdt)
	           AND (pcat.enddate IS NULL or now()< pcat.enddate)
	           AND progmod.stsrefid IN (
	                   SELECT std.stsrefid
				         FROM webset.std_srv_progmod std
				              LEFT OUTER JOIN webset.statedef_mod_acc_loc loc ON std.malrefid = loc.malrefid
				              INNER JOIN webset.statedef_mod_acc acc ON std.stsrefid = acc.stsrefid
				              INNER JOIN webset.statedef_mod_acc_cat cat ON acc.macrefid = cat.macrefid
				              INNER JOIN webset.def_modfreq frq ON std.ssmfreq = frq.sfrefid
				              LEFT OUTER JOIN public.sys_usermst usr ON std.umrefid = usr.umrefid
				        WHERE stdrefid = " . $this->tsRefID . "
               )
	         ORDER BY progmod.ids_assessments
		";

		$result = db::execSQL($SQL)->assocAll();

		$this->accommodations = $result;

	}

	/**
	 *  Set sum accommodations
	 */
	public function sumAccomodations() {
		$this->countAccommodations = count($this->accommodations);
	}

	/**
	 *  Set sum modifications
	 */
	public function sumModifications() {
		$this->countModifications = count($this->modifications);
	}

	/**
	 *  Convert array accommodations with parametres to array id's
	 *
	 *  @return array
	 */
	public function convertAccommodationsID() {
		for ($i = 0; $i < $this->countAccommodations; $i++) {
			$assess[] = $this->accommodations[$i]['ids_assessments'];
		}

		return $assess;
	}

	/**
	 *  Convert array modifications with parametres to array id's
	 *
	 *  @return array
	 */
	public function convertModificationsID() {
		for ($i = 0; $i < $this->countModifications; $i++) {
			$assess[] = $this->modifications[$i]['marefid'];
		}

		return $assess;
	}

	/**
	 * Select modifications from db by IDs accommodations
	 *
	 * @return void
	 */
	public function getModificationsByAccommodations() {
		if ($this->countAccommodations > 0) {
			$accommodationsID = $this->convertAccommodationsID();
			$stringID         = implode(',', $accommodationsID);
		} else {
			$stringID = 0;
		}

		$SQL = "
			SELECT std.marefid,
			       COALESCE(pcat.macdesc || ': ', '') || progmod.stsdesc AS title,
			       progmod.stsrefid
			  FROM webset.std_assess_acc std
			       INNER JOIN webset.statedef_mod_acc accomod ON accomod.stsrefid = std.marefid
			       INNER JOIN webset.statedef_assess_acc_cat acat ON acat.aacrefid = accomod.aacrefid
			       LEFT OUTER JOIN webset.statedef_mod_acc progmod ON progmod.ids_assessments::int = accomod.stsrefid AND progmod.screfid = " . VNDState::factory()->id . "
			       LEFT OUTER JOIN webset.statedef_mod_acc_cat pcat ON pcat.macrefid = progmod.macrefid
             WHERE std.stdrefid = " . $this->tsRefID . "
               AND progmod.stsrefid IS NOT NULL
               AND std.marefid NOT IN ($stringID)
             ORDER BY progmod.stsseq, progmod.stsdesc
        ";

		$result = db::execSQL($SQL)->assocAll();

		$this->modifications = $result;
	}

	/**
	 * Select modifications from db
	 *
	 * @return void
	 */
	public function getModifications() {
		#REPLACE('<b>' || COALESCE(aacdesc, '') || ':</b> ' || macc.stsdesc, '<b>:</b>', '')
		$SQL = "
			SELECT std.saarefid,
                   REPLACE(COALESCE(aacdesc, '') || ': ' || macc.stsdesc, '', '') AS title,
                   sacc.aaadesc,
                   std.marefid
              FROM webset.std_assess_acc std
                   INNER JOIN webset.statedef_assess_acc sacc ON std.aaarefid = sacc.aaarefid
                   INNER JOIN webset.statedef_mod_acc macc ON macc.stsrefid = std.marefid
                   LEFT OUTER JOIN webset.statedef_assess_acc_cat cat ON cat.aacrefid = macc.aacrefid
             WHERE std.stdrefid = " . $this->tsRefID . "
        ";

		$result = db::execSQL($SQL)->assocAll();

		$this->modifications = $result;
	}

	/**
	 *  Check relations between accommodations and modifications.
	 *  Set to notBinding accommodations without modifications.
	 */
	public function checkRelationsAcc() {
		$cheker = $this->accommodations;
		for ($i = 0; $i < $this->countAccommodations; $i++) {
			for ($j = 0; $j < $this->countModifications; $j++) {
				if ($cheker[$i]['ids_assessments'] == $this->modifications[$j]['marefid']) {
					unset($cheker[$i]);
					break;
				}

			}

		}

		$this->countNotBinding = count($cheker);
		$this->notBinding      = $cheker;
	}

	/**
	 *  Check relations between accommodations and modifications.
	 *  Set to notBinding accommodations without modifications.
	 */
	public function checkRelationsMod() {
		$cheker = $this->modifications;
		for ($i = 0; $i < $this->countModifications; $i++) {
			for ($j = 0; $j < $this->countAccommodations; $j++) {
				if ($cheker[$i]['marefid'] == $this->accommodations[$j]['ids_assessments'] ||
					$cheker[$i]['stsrefid'] == '') {
					unset($cheker[$i]);
					break;
				}

			}

		}

		$this->countNotBinding = count($cheker);
		$this->notBinding      = $cheker;
	}

	/**
	 *  Create message for alert about accommodations without relations.
	 *  $key - name column with id from db
	 *
	 *  @param  string $key
	 *  @return string
	 */
	public function buildNotBindingMessage($key) {
		if ($key == 'stsrefid')  {
			$title[] = 'Program Modifications and Accommodations&nbsp;';
			$title[] = 'Student Assessment Accommodation(s)&nbsp;';
		} else {
			$title[] = 'Student Assessment Accommodation(s)&nbsp;';
			$title[] = 'Program Modifications and Accommodations&nbsp;';
		}

		$message = UILayout::factory()->newLine()
			->addHTML('Suggested&nbsp;', '[float: left]')
			->addHTML($title[0], 'bold [float: left]')
			->addHTML('based upon&nbsp;', '[float: left]')
			->addHTML($title[1], 'bold [float: left]')
			->addHTML('already selected', '[float: left]')
		;
		
		$accIDs  = '';
		$element = 1;
		#if elements > 1 - generate row with id's for button 'Add All'
		foreach ($this->notBinding as $acc) {
			$accIDs .= $acc[$key];

			if ($element < $this->countNotBinding) {
				$accIDs .= ',';
			}

			$message->newLine()
				->addHTML(
					UIAnchor::factory('Add ' . $acc['title'])
						->onClick('addAccommodation(' . $acc[$key] . ')')
						->toHTML()
				);

			$element++;
		}

		if ($this->countNotBinding > 1) {
			$message->newLine()
				->addHTML(
					UIAnchor::factory('Add All')
						->css('font-weight', 'bold')
						->onClick('addAccommodation("' . $accIDs . '")')
						->toHTML()
				);
		}

		return $message->toHTML();
	}

	/**
	 * Add control by array accommodations. If Array is empty - select all posiable accommodations.
	 * If array have ID - select accommodations only with ID from array.
	 *
	 * @param EditClass $edit
	 */
	public function setControlByAcc(EditClass $edit) {
		$whereID = null;
		if ($this->countAccommodations > 0) {
			$accIDs  = implode(',', $this->accommodations);
			$whereID = 'AND macc.stsrefid IN (' . $accIDs . ')';
		} else {
			$accIDs = null;
		}

		$edit->addControl(
			FFMultiSelect::factory('Accommodation')
				->sql("
					SELECT stsrefid,
		                   TRIM(COALESCE(aacdesc, '')||': '||stsdesc, ': ')
		              FROM webset.statedef_mod_acc macc
		                   LEFT OUTER JOIN webset.statedef_assess_acc_cat cat ON cat.aacrefid = macc.aacrefid
		             WHERE macc.screfid = " . VNDState::factory()->id . "
		               AND UPPER(assessmentsw) = 'Y'
		               AND (macc.recdeactivationdt IS NULL OR NOW() < macc.recdeactivationdt)
		                   $whereID
		             ORDER BY stsseq, stscode, stsdesc
                ")
				->value($accIDs)
				->name('marefid')
		)
		->req();

	}

} 