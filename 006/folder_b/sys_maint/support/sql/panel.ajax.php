<?php
	Security::init(NO_OUPUT);
    $vid = io::post('vid', true);
    $num = io::posti('num', true);
    $dwn = io::post('dwn');
    $sql = io::post('sql', true);
    $data = '';
    $total = 0;
    
    function tableName($fields, $sql) {
        
        $sql = str_replace("\"", "", $sql);
        $sql = db::escape($sql);
        
        for ($i = 0; $i < count($fields); $i++) {
            $fields[$i] = "'" . $fields[$i] . "'";
        }
        
        return db::execSQL("
            SELECT nspname || '.' ||relname
              FROM pg_class c, pg_attribute a,pg_type t, pg_namespace n
             WHERE relkind = 'r'
               AND a.attnum > 0 AND a.atttypid = t.oid AND a.attrelid = c.oid
               AND c.relnamespace = n.oid
               AND attname IN (" . implode(',', $fields) . ")
               AND (LOWER('" . $sql . "') LIKE '%' || nspname || '.' ||relname || '%' OR
                   (LOWER('" . $sql . "') LIKE '%' || relname || '%'  AND nspname = 'public'))
             GROUP BY  relname, nspname
             ORDER BY count(1) desc
             LIMIT 1
        ")->getOne();
    }
    
    function insertSql($rows, $table_name, $columns) {
      
        foreach ($rows as $fld => $value) {           
            $rows[$fld] = ($value == '' ? 'NULL' : "'" . db::escape($value) . "'");
        }
       
        return 'INSERT INTO ' . $table_name . PHP_EOL . 
               '(' . implode(', ', $columns) . ')' . PHP_EOL . 
               'VALUES' . PHP_EOL . 
               '(' . implode(', ', $rows). ');' . PHP_EOL . PHP_EOL;
    }
    
    function updateSql($rows, $table_name) {
      
        foreach ($rows as $fld => $value) {
           
            if (!isset($key_field)) {
                $key_field = $fld . " = '" . $value . "'";               
                continue;
            }            
            
            $fields[] = $fld . ' = ' . ($value == '' ? 'NULL' : "'" . db::escape($value) . "'");
        }
       
        return 'UPDATE ' . $table_name . PHP_EOL . 
               'SET ' . implode(',' . PHP_EOL, $fields) . PHP_EOL . 
               'WHERE ' . $key_field . ';' . PHP_EOL . PHP_EOL;
    }
    
    
    if ($vid == 'table') {
        $result = db::execSQL($sql);
        $total = $result->recordCount();
        $columns = $result->columns();
        $rows = $result->assocAll();
        //io::ajax('data', print_r($result->columns(), true)); die();
        $table = UITable::factory('100%')
            ->border(1, 'gray')
            ->cellPadding(2);
        $table->addRow(); 
        foreach ($columns as $field) {        
            $table->addCell('<b>' . $field . '</b>');
        }        

        for ($i = 0; $i < count($rows); $i++) {
            if ($num == $i) break; 
            $table->addRow();
            foreach ($rows[$i] as $field => $value) {        
                $table->addCell($value);
            }
        }
        $data = $table->toHTML();
    } elseif ($vid == 'insert') {       
        $result = db::execSQL($sql);
        $total = $result->recordCount();
        $columns = $result->columns();
        $rows = $result->assocAll();
        $table_name = tableName($columns, $sql);        
        for ($i = 0; $i < count($rows); $i++) {
            if ($num == $i) break; 
            $del_id[] = "'" . db::escape($rows[$i][$columns[0]]) . "'";
            $data .= insertSql($rows[$i], $table_name, $columns);            
        }
        $data = 'DELETE FROM ' . $table_name . ' WHERE ' . $columns[0] . ' in (' . implode(', ', $del_id) . ');' . PHP_EOL . PHP_EOL . $data;
    } elseif ($vid == 'update') {       
        $result = db::execSQL($sql);
        $total = $result->recordCount();
        $columns = $result->columns();
        $rows = $result->assocAll();
        $table_name = tableName($columns, $sql);
        //io::ajax('data', print_r($table, true)); die();
        for ($i = 0; $i < count($rows); $i++) {
            if ($num == $i) break; 
            $data .= updateSql($rows[$i], $table_name);            
        }
    } elseif ($vid == 'csv') {
        $result = db::execSQL($sql);
        $total = $result->recordCount();
        $columns = $result->columns();       
        $rows = $result->assocAll();
        $data = implode(', ', $columns) . PHP_EOL;
        for ($i = 0; $i < count($rows); $i++) {
            if ($num == $i) break; 
            foreach ($rows[$i] as $fld => $value) {           
                $rows[$i][$fld] = str_replace('"', '\"', $rows[$i][$fld]);
                $rows[$i][$fld] = strstr($rows[$i][$fld], ' ') ? '"' . $rows[$i][$fld] . '"' : $rows[$i][$fld] ;
            }
            $data .= implode(', ', $rows[$i]) . PHP_EOL;            
        }
    }
    
    if ($vid == 'update' || $vid == 'insert' || $vid == 'csv') {
        $data = FFTextArea::factory()                    
            ->width('100%')
            ->css('height', '455px')
            ->css('font-size', '12px')
            ->css('background-color', 'white')
            ->css('font-family', 'Courier')
            ->css('font-size', '13px')
            ->value($data)
            ->toHTML();
    }
    
    io::ajax('total', $total);
    io::ajax('data', $data);
    
    //FileUtils::createTmpFile($data, ($vid == 'csv' ? 'csv' : 'html'));
    
?>