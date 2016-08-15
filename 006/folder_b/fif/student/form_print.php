<?php
    Security::init();
    
    $form = db::execSQL("
        SELECT xmlbody
          FROM webset.disdef_fif_forms
         WHERE frefid = ".io::posti('frefid')."
    ")->assoc();
    
    $xmlvalues = '';
    foreach ($_POST as $key => $val){
        if ($val!='' and substr($key,0,7)=='constr_')
            $values[substr($key,7,strlen($key))] = stripslashes($val);
            $xmlvalues .= '<value name="'.substr($key,7,strlen($key)).'">' . stripslashes($val). '</value>'.chr(10);
    }
        
    require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");
    $doc = new xmlDoc();    
    $xml_content  = base64_decode($form['xmlbody']);
    $doc->xml_data = $doc->xml_merge($xml_content, $xmlvalues);             
    
    print '<script type="text/javascript">
               location = "'.$doc->getPdf().'";
           </script>';
?>