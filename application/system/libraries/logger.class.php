<?php

/**
 * @author     Derek Sanford
 * @copyright  (c) 2011 servalphp.com
 */

class Logger {
    function queryLog($txt) {
        self::writeLog('queryLog', $txt);
        unset ($txt);
    }

    function urlLog($txt) {
        if (is_array($txt)) {
            $txt = json_encode($txt);
        }

        self::writeLog('urlLog', $txt);
        unset ($txt);
    }

    function echoLog($txt) {
        self::writeLog('echoLog', $txt);
        unset ($txt);
    }

    function accessLog($txt) {
        self::writeLog('accessLog', $txt);
        unset ($txt);
    }

    function writeLog($filename, $txt) {
        $filename = LOG_DIR . '/' . $filename . date("_Ymd") . '.txt';

        $txt = date("Y-m-d H:i:s") . " >> $txt\n";

        file_put_contents($filename, $txt, FILE_APPEND);
    }
}
