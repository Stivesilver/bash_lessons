<?php
    function xmlEncode($RefID, &$data) {
        $data['xmlbody'] = base64_encode(io::post('xmlbody'));
    }
?>