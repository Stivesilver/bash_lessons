<?php

/**
 *  IDEAGoalDBHelper
 *  Helper for sql-query builder. Goals have different tables and columns,
 *  but use similar logic.
 * 
 * 	@author Ganchar Danila
 * 	@version created 08.11.2013; updated 11.11.2013  
 */

   class IDEAGoalDBHelper {
   	   
   	   /**
	   *  Type - condition (type goal)
	   * 
	   *  @var string 
	   */
   	   const T_CONDITION = 'condition';
   	   
   	   /**
	   *  Type - verb (type goal)
	   * 
	   *  @var string 
	   */
   	   const T_VERB 	 = 'verb';
   	   
   	   /**
	   *  Type - content (type goal)
	   * 
	   *  @var string 
	   */
   	   const T_CONTENT   = 'content';
   	   
   	   /**
	   *  Type - measure (type goal)
	   * 
	   *  @var string 
	   */
   	   const T_MEASURE   = 'measure';
   	   
   	   /**
	   *  Type - schedule (type goal)
	   * 
	   *  @var string 
	   */
   	   const T_SCHEDULE  = 'schedule'; 
	   
	   /**
	   *  Name table for current goal
	   * 
	   *  @var string 
	   */
	   protected $table;
	   
	   /**
	   *  @var array name columns for current goal
	   */
	   protected $columns = array();
	   
	   /**
	   *  @var string type goal
	   */
	   protected $type;
	   
	   public function __construct($typeGoal) {
		   $this->type = $typeGoal; 
		   $this->setOptionsTable();     
	   }
	   
	   /**
	    * Get attribute by name
	    * 
	    * @return string
	    */
	   public function getAttr($name) {
		   return $this->$name;
	   }
	   
	   /**
	    * Return refid column
	    * 
	    * @return string column refid
	    */
	   public function getRifID() {
		   return $this->columns['refid'];
	   }
	   
	   /**
	    * Return name column
	    * 
	    * @return string 
	    */
	   public function getColumn($nameColumn) {
		   return $this->columns[$nameColumn];
	   }
	   
	   /**
	    * Set table name and columns by type Goal 
	    * 
	    * @return void
	    */
	   public function setOptionsTable() {
		   $paramTable = array(
   				self::T_CONDITION => array(
   					'table' => 'webset.disdef_bgb_ksaconditions',
   					'columns' => 
   						array(
   							'refid' => 'crefid',
   							'item'  => 'cdesc',
   							'ksaID' => 'blksa',
   							'ksaCM' => 'blksa',
   							'ksaWH' => 'AND blksa = '
   						)
   				),
   				 self::T_VERB     => array(
   					 'table' => 'webset.disdef_bgb_ksaksgoalactions',
   					 'columns' => 
   					 	 array(
   							 'refid' => 'gdskgarefid',
   							 'item'  => 'gdskgaaction',
   							 'ksaID' => 'gdskgrefid',
   							 'ksaCM' => 'gdskgrefid',
   							 'ksaWH' => 'AND gdskgrefid = '
   						 )
   				 ),
   				self::T_CONTENT   => array(
   					'table' => 'webset.disdef_bgb_scpksaksgoalcontent',
   					'columns' => 
   						array(
   							'refid' => 'gdskgcrefid',
   							'item'  => 'gdskgccontent',
   							'ksaID' => 'gdskgrefid',
   							'ksaCM' => 'gdskgrefid',
   							'ksaWH' => 'AND gdskgrefid = '
   						)
   				),
   				self::T_MEASURE   => array(
   					'table' => 'webset.disdef_bgb_measure',
   					'columns' => 
   						array(
   							'refid' => 'mrefid',
   							'item'  => 'mdesc',
   							'ksaID' => '',
   							'ksaCM' => '',
   							'ksaWH' => ''
   						)
   				),
   				self::T_SCHEDULE  => array(
   					'table' => 'webset.disdef_bgb_ksaeval',
   					'columns' => 
   						array(
   							'refid' => 'erefid',
   							'item'  => 'edesc',
   							'ksaID' => '',
   							'ksaCM' => '',
   							'ksaWH' => ''
   						)
   				)
   			);
   			
   			$this->table   = $paramTable[$this->type]['table'];
   			$this->columns = $paramTable[$this->type]['columns'];
   		
	   }	
	   
	   /**
	    * Add areaID to ksaWH column(if Goal have necessary type)
	    * 
	    * @return void
	    */
	   public function addAriaID($areaID) {
		   if ($this->type == self::T_CONDITION || $this->type == self::T_VERB
		   	   || $this->type == self::T_CONTENT) {
			   $this->columns['ksaWH'] .= $areaID;
		   }

		   if ($areaID == '') {
			   $this->columns['ksaWH'] = null;
		   }
		   
	   }

       /**
        * Insert new bank.
        *
        * @param string $nameBank
        * @return true|null
        */
       public function addGoalBank($nameBank) {
           if ($this->columns['ksaCM'] != '') {
               $this->columns['ksaCM'] .= ',';
               $this->columns['ksaID'] .= ',';
           }

           $sql = "
            INSERT INTO " . $this->table . "
                   (" . $this->columns['item'] . ",
                    umrefid,
                    " . $this->columns['ksaCM'] . "
                    lastuser,
                    lastupdate)
            VALUES ('" . $nameBank . "',
                   " . SystemCore::$userID . ",
                   " . $this->columns['ksaID'] . "
                   '" . SystemCore::$userUID . "',
                   now())
           ";

           if (db::execSQL($sql)) {
               return true;
           }

       }
	   
	   /**
	    * Build SQL query for list items from object. 
	    * 
	    * @return string SQL query 
	    */    
	   public function getQueryItemList() {
		   $sql = "
			   SELECT " . $this->columns['refid'] . ",
        			  " . $this->columns['item'] . ",
			          CASE WHEN NOW() > enddate  THEN 'In-Active' ELSE 'Active' END
				 FROM " . $this->table . "
			    WHERE umrefid = USERID ADD_SEARCH
			          " . $this->columns['ksaWH'] . "
			    ORDER BY " . $this->columns['item'] 
	     	   ;
	       
	       return $sql;
	                  
	   }     
	   
	   /**
	    * Build SQL query for edit items from object. 
	    * 
	    * @return string SQL query 
	    */  
	   public function getQueryItemEdit($RefID) {
		   $sql = "
		   	   SELECT " . $this->columns['item'] . ",
		         	  enddate,
		        	  lastuser,
		           	  lastupdate,     
		           	  " . $this->columns['ksaCM'] . "
		           	  umrefid
			     FROM " . $this->table . "
			    WHERE " . $this->getRifID() . " = " . $RefID
			; 
			
		   return $sql; 
	   } 
	   
	   /**
	    * Build SQL query for deactivate items from object. 
	    * 
	    * @return string SQL query 
	    */  
	   public function getQueryDActive($RefID) {
		   $sql = "
		       UPDATE " . $this->getAttr('table') . "
			      SET lastupdate = now(), 
		  		      lastuser = '" . Systemcore::$userUID . "', 
		  		      enddate = now(),
		  		      umrefid = '" . Systemcore::$userID . "'
			    WHERE " . $this->getRifID() . "='" . $RefID . "'	   
			";
			
			return $sql;
	   }

       /**
        * Check exist condition in db
        *
        * @param string $bank name condition
        * @return bool true - exist, false - not exist
        */
       public function checkExistBank($bank) {
		   $sql = "
		       SELECT 1
	             FROM " . $this->getAttr('table') . "
	            WHERE umrefid = " . Systemcore::$userID . "
	              AND TRIM(LOWER(" . $this->getColumn('item') . ")) LIKE TRIM(LOWER('" . $bank . "'))
	                " . $this->getColumn('ksaWH') . "	   
			   ";
			
			$result = db::execSQL($sql);
			
			if ($result->fields['0'] == '' && $result->fields['?column?'] == '') {
				$return = false;
			} else {
				$return = true;
			}

			return $return;
	   }

       /**
        * Add area ID to ksaID column if goal have necessary type
        *
        * @param int $area_id
        * @return void
        */
       public function checkKsaID($area_id) {
           if ($this->type == self::T_CONDITION || $this->type == self::T_CONTENT || $this->type == self::T_VERB) {
               $this->columns['ksaID'] = $area_id;
           } else {
               $this->columns['ksaID'] = '';
           }

       }
	   
	   /**
	    * Add ',' if empty ksaCM column. 
	    * 
	    * @return void
	    */  
	   public function checkKsaCMColumn() {
	   	   if ($this->columns['ksaCM'] != '') {
			   $this->columns['ksaCM'] .= ',';  
	   	   }   
	   	   
	   }
	   
   }
 
?>
