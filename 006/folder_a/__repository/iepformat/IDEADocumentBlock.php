<?php

	/**
	 * Contains basic Document Block information
	 *
	 * @copyright Lumen Touch, 2015
	 */
	class IDEADocumentBlock extends RegularClass {

		
		/**
		 * Block Name
		 *
		 * @var string
		 */
		public $name;
		
		/**
		 * Block Render Function located at IDEABlock* classes
		 *
		 * @var string
		 */
		public $renderFunction;

		/**
		 * Block Check Function located at IDEAStudentChecker
		 *
		 * @var string
		 */
		public $checkFunction;
		
		/**
		 * Block Paramater for Check Function
		 *
		 * @var string
		 */
		public $checkParamater;
		
		/**
		 * Set Block Name
		 * 
		 * @return IDEADocumentBlock
		 */
		public function setName($name = null) {
			if ($name == null) {
				throw new Exception('Block name has not be specified');
			} 
			$this->name = $name;
			return $this;
		}

		/**
		 * Set Block Render Function
		 * 
		 * @return IDEADocumentBlock
		 */
		public function setRenderFunction($func = null) {
			$this->renderFunction = $func;
			return $this;
		}

		/**
		 * Set Block Check Function
		 * 
		 * @return IDEADocumentBlock
		 */
		public function setCheckFunction($func = null) {
			$this->checkFunction = $func;
			return $this;
		}

		/**
		 * Set Block Check Paramater
		 * 
		 * @return IDEADocumentBlock
		 */
		public function setCheckParameter($param = null) {
			$this->checkParamater = $param;
			return $this;
		}

		/**
		 * Creates an instance of this class using Database
		 * @param int $id of block || references to webset.sped_iepblocks_
		 * 
		 * @return IDEADocumentBlock
		 */
		public static function getBlockByID($id = null) {
			if ($id == null) {
				throw new Exception('Block ID has not been specified.');
			}
			$block_object = self::factory();
			$block = $block_object->execSQL("
				SELECT iepdesc,
				       ieprenderfunc,
				       check_method,
				       check_param
				  FROM webset.sped_iepblocks
				 WHERE ieprefid  = " . $id . "
			")->assoc();

			if (isset($block['iepdesc'])) { 
				$block_object
					->setName($block['iepdesc'])
					->setRenderFunction($block['ieprenderfunc'])
					->setCheckFunction($block['check_method'])
					->setCheckParameter($block['check_param']);
			} else {
				throw new Exception('Block ID ' . $id . ' has not found in webset.sped_iepblocks');
			}
			return $block_object;
		}

		/**
		 * Creates an instance of this class
		 * 
		 * @return IDEADocumentBlock
		 */
		public static function factory() {
			return new IDEADocumentBlock();
		}

	}

?>
