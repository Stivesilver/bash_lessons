<?php

	/**
	 * Contains basic Document/IEP Type data
	 *
	 * @copyright Lumen Touch, 2012
	 */
	class IDEADocumentType extends RegularClass {

		/**
		* Default Generate File for all of the Blocks Documents
		* The location for buttons such as Refresh, Show Search Fields
		*/
		const DEFAULT_PREVIEW_GEN_FILE = 'apps/idea/library/preview_block.ajax.php';

		/**
		 * Doc Type ID
		 * DB Table: webset.sped_doctype
		 *
		 * @var int
		 */
		private $drefid;

		/**
		 * Doc Type Title
		 * DB Table: webset.sped_doctype
		 *
		 * @var string
		 */
		private $title;

		/**
		 * Block Class for Doc
		 * DB Table: webset.sped_doctype
		 *
		 * @var string
		 */
		private $block_class;

		/**
		 * Doc Type Preview Block File
		 * DB Table: webset.sped_doctype
		 *
		 * @var string
		 */
		private $preview_gen_file;

		/**
		 * Default Doc Type Flag
		 * DB Table: webset.sped_doctype
		 *
		 * @var bool
		 */
		private $default_sw;

		/**
		 * Initializes basic properties
		 *
		 * @param int $drefid
		 */
		public function __construct($drefid = null) {
			if ($drefid == null) {
				throw new Exception('Document Type ID has not been specified.');
			}
			$this->drefid = $drefid;

			$doc_type = $this->execSQL("
				SELECT drefid,
				       setrefid,
				       block_class,
				       preview_gen_file,
				       seqnum,
				       doctype,
				       enddate,
				       lastupdate,
				       lastuser,
				       docdesc,
				       defaultdoc
				  FROM webset.sped_doctype
				 WHERE drefid = " . $this->drefid . "
			")
				->assoc();

			$this->title = $doc_type['doctype'];
			$this->block_class = $doc_type['block_class'];
			$this->default_sw = ($doc_type['defaultdoc'] == 'Y' ? true : false);
			$this->preview_gen_file = $doc_type['preview_gen_file'] == '' ? CoreUtils::getVirtualPath(self::DEFAULT_PREVIEW_GEN_FILE) : CoreUtils::getVirtualPath($doc_type['preview_gen_file']);
		}

		/**
		 * Returns Document Type Blocks Objects
		 *
		 * @param string $ids
		 * @return array IDEADocumentBlock
		 */
		public function getBlocks($ids = null) {

			$block_objects = array();
			$blocks = $this->execSQL("
				SELECT ieprefid,
				       iepdesc,
				       ieprenderfunc,
				       check_method,
				       check_param
				  FROM webset.sped_iepblocks
				 WHERE ieptype = " . $this->drefid . "
				  " . ($ids == null ? "" : " AND ieprefid IN (" . $ids . ")  ") . "
				 ORDER BY iepseqnum
			")->assocAll();

			foreach ($blocks as $block) {
				$block_objects[] = IDEADocumentBlock::factory()
					->setName($block['iepdesc'])
					->setRenderFunction($block['ieprenderfunc'])
					->setCheckFunction($block['check_method'])
					->setCheckParameter($block['check_param']);

			}
			return $block_objects;
		}


		/**
		 * Returns Document Type Blocks
		 *
		 * @return array
		 */
		public function getBlocksArray() {

			$blocks = $this->execSQL("
				SELECT *
				  FROM webset.sped_iepblocks
				 WHERE ieptype = " . $this->drefid . "
				 ORDER BY iepseqnum
			")->assocAll();

			return $blocks;
		}

		/**
		 * Returns Document Type Blocks
		 *
		 * @param string $ids
		 * @return array IDEADocumentBlock
		 */
		public function getBlocksKeyedArray($ids = null) {

			return $this->execSQL("
				SELECT ieprefid,
				       iepdesc
				  FROM webset.sped_iepblocks
				 WHERE ieptype = " . $this->drefid . "
				  " . ($ids == null ? "" : " AND ieprefid IN (" . $ids . ")  ") . "
				 ORDER BY iepseqnum
			")->keyedCol();
		}

		/**
		 * Returns Document ID
		 *
		 * @return string
		 */
		public function getDocID() {
			return $this->drefid;
		}

		/**
		 * Returns Document Type Title
		 *
		 * @return string
		 */
		public function getTitle() {
			return $this->title;
		}

		/**
		 * Returns Block Class Name such as IDEABlockMO
		 *
		 * @return string
		 */
		public function getBlockClass() {
			return $this->block_class;
		}

		/**
		 * Returns Gen File path
		 *
		 * @return string
		 */
		public function getPreviewGenFile() {
			return $this->preview_gen_file;
		}

		/**
		 * Returns Document Type Title
		 *
		 * @return string
		 */
		public function isDefault() {
			return $this->default_sw;
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param int $drefid
		 * @return IDEADocumentType
		 */
		public static function factory($drefid = null) {
			return new IDEADocumentType($drefid);
		}

	}

?>
