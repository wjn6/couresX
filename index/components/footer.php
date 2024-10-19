<?php
    if($conf["webVfx_open"] == '1'){
        preg_match_all('/<script.*?<\/script>/', $conf["webVfx"], $matches);
        echo implode('', $matches[0]);
    } 
?>