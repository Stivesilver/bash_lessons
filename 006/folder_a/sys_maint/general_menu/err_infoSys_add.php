<?PHP
   require("../../../../uplinkos/security.php");
   require("$g_physicalRoot/uplinkos/classes/editClass.php");

   if (!isset($edit)) {
      $edit = new editClass("edit1", $_GET["RefID"]);
      $edit->title =  "Add/Edit Info System";
      $edit->SQL   = "SELECT isdesc, lastuser, lastupdate 
                      FROM webset.err_infosysdef
                      WHERE isrefid=$RefID";

      $edit->groups[0]   = "General Information";
      $edit->groups[1]   = "Update Information";

      $edit->addControl("Description", "text", "size=80", "", "", true);
      $edit->addControl("Last User", "PROTECTED", "", "", $_SESSION["s_userUID"]);
      $edit->addControl("Last Update", "PROTECTED", "", "", date("m-d-Y H:i:s"));

      $edit->finishURL = "err_infoSys.php";
      $edit->cancelURL = "err_infoSys.php";

      $edit->printEdit();
   }
?>