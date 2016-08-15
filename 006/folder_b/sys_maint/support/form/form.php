<?php
    Security::init();
    require_once("form_inc.php");

    $SQL = "
        SELECT $content,
               $name as name
          FROM $table
         WHERE $refid = " . io::get('form_id') . "
    ";

    $result = db::execSQL($SQL);

    if ($base64 == "yes") {
        $result->fields[0] = base64_decode($result->fields[0]);
    }
?>
<body onkeyup='if (event.keyCode==113) {saveForm();} if (event.keyCode==119) {printprev();} '>
    <form name=mainform id=mainform action='form_save.php?form_id=<?= io::get('form_id'); ?>&area=<?= io::get('area'); ?>' method=post target=workFrame>
        <span class=zText>Form Name: <b id="form_title" onmousedown='makeTextarea(this);'><?= $result->fields["name"]; ?></b></span>
        <br>
        <span class=zText>Form Link: <a target="newTab" href="form.php?area=<?= io::get('area'); ?>&form_id=<?= io::get('form_id'); ?>">http://<?= $_SERVER["SERVER_NAME"] . SystemCore::$virtualRoot; ?>/applications/webset/support/form.php?area=<?= io::get('area'); ?>&form_id=<?= io::get('form_id'); ?></a></span>
        <input type=button id=saveButton onclick="saveForm()" value="Save Form - F2" style="width:99%; height:50px;font-size:20px;;">
        <input type=hidden name=format id=format>
        <input type=hidden name=xmldata id=xmldata>
        <input type=hidden name=testmode id=testmode>
        <input type=hidden name=editmode id=editmode>
        <table style="width:99%;height:76%;">
            <tr>
                <td style="width:99%;height:100%;">
                    <iframe id=workFrame name=workFrame style="width:100%;height:100%;display:none;" frameborder=0 src="<?= SystemCore::$virtualRoot; ?>/uplinkos/empty.php"></IFRAME>
                    <textarea name=cont id=cont style="width:100%;height:400px;"><?= htmlentities($result->fields[0]); ?></textarea>
                </td>
            </tr>
        </table>

        <table width="99%">
            <tr>
                <td><input type=button value="Form Edit" style="width:100%; height:50px;font-size:20px;;" onclick='seeForm();'></td>
                <td><input type=button value="PDF Fields" style="width:100%; height:50px;font-size:20px;;" onclick='document.getElementById("testmode").value="yes";printprev("PDF");'></td>
                <td><input type=button value="PDF Clear" style="width:100%; height:50px;font-size:20px;;"       onclick='document.getElementById("testmode").value="no"; printprev("PDF");'></td>
                <td><input type=button value="HTML Fields" style="width:100%; height:50px;font-size:20px;;"     onclick='document.getElementById("editmode").value="yes";printprev("HTML");'></td>
                <td><input type=button value="HTML Clear" style="width:100%; height:50px;font-size:20px;;"      onclick='document.getElementById("editmode").value="no"; printprev("HTML");'></td>
                <td><input type=button value="ODT Clear" style="width:100%; height:50px;font-size:20px;;" onclick='printprev("ODT");'></td>
                <td><input type=button value="Export" style="width:100%; height:50px;font-size:20px;;" onclick='exportThis();'></td>
                <td><input type=button value="Close" style="width:100%; height:50px;font-size:20px;;" onclick='api.window.destroy();'></td>
            </tr>
        </table>
    </form>
    <script>

        function saveForm() {
            document.getElementById("saveButton").disabled = true;
            document.getElementById("mainform").action = "form_save.php?form_id=<?= io::get('form_id'); ?>&area=<?= io::get('area'); ?>";
            document.getElementById("mainform").submit();
        }

        function seeForm() {
            document.getElementById("workFrame").style.display = 'none';
            document.getElementById("cont").style.display = '';
        }

        function acctivateSave() {
            document.getElementById("saveButton").disabled = false;
        }

        function printprev (format) {
            txt = document.getElementById("cont").value;
            if (txt.indexOf("</line>")>0 || format!="") {
                document.getElementById("workFrame").style.display = '';
                document.getElementById("cont").style.display = 'none';
                document.getElementById("xmldata").value = txt;
                document.getElementById("format").value = format;
                document.getElementById("mainform").action = "xml/result.php";
                document.getElementById("mainform").submit();
                return;
            }
            w = window.screen.availWidth -100;
            h = window.screen.availHeight -150;
            t = 50;
            l = 50;
            options = 'top='+t+', left='+l+', width='+w+', height='+h+', menubar=yes, status=yes, toolbar=no, resizable=yes, scrollbars=yes';

            a = window.open("", '1', options);
            a.document.write ("<BODY leftMargin=0 topMargin=0 marginwidth=0 marginheight=0 ondblclick='window.close();' title='Double Click to Close'><title>Double Click to Close</title>");
            a.document.write ("<STYLE>");
            a.document.write ("BODY, TABLE, TH, TD, SPAN, P, DIV {");
            a.document.write ("font-family: Arial;");
            a.document.write ("font-size: 12px;}");
            a.document.write (".def_es {");
            a.document.write ("font-family: Arial;");
            a.document.write ("font-size: 12px;}");
            a.document.write (".break {page-break-before: always}");
            a.document.write ("</STYLE>");
            a.document.write (document.getElementById("cont").value);
            a.document.close();
        }

        function exportThis() {
            document.getElementById("workFrame").style.display = '';
            document.getElementById("cont").style.display = 'none';
            document.getElementById("workFrame").src = '<?= SystemCore::$virtualRoot; ?>/applications/webset/sys_maint/includes/export_popup.php?table=<?= $table; ?>&refid=<?= $refid; ?>&ids=<?= io::get('form_id'); ?>,';
        }

        function makeTextarea(obj) {
            if (obj.innerHTML.indexOf("tempText")>0) return;
            obj.innerHTML = "<textarea id=tempText style=\"height:22px;width:600px;\">" + obj.innerHTML + "</textarea>";
            document.getElementById("tempText").select();
        }

        document.getElementById("cont").select();

        if ("<?= io::get('mode'); ?>"=="view") {
            printprev();
            window.close();
        }

        zWindow.changeCaption(document.getElementById("form_title").innerHTML);

    </script>