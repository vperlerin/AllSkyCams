<?php

function pp($v)
{
    echo('<pre>');
    print_r($v);
    echo('</pre>');
}

function dd($v)
{
    echo($v);
    echo('<hr/>');
}

function pg($v)
{
    if($_SERVER['REMOTE_ADDR'] == '68.55.56.39')
    {
        echo("This is only printed for Castle IPs<br>\n");
        pp($v);
    }
}

 

function redirect($url)
{
    header("Location: $url");
    exit ;
}

function d()
{
    ini_set('display_errors', 'on');
}

function get_lang()
{
    $lang = 'en';

    if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
    {
        $pos = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        foreach($pos as $val)
        {
            // check for q-value. No q-value means 1 (highest)
            if(preg_match("/(.*);q=([0-1]{0,1}\.\d{0,4})/i", $val, $matches))
            {
                $pos_lang[$matches[1]] = (float)$matches[2];
            }
            else
            {
                $pos_lang[$val] = 1.0;
            }

            // Look for the highest q-value
            $qval = 0.0;
            foreach($pos_lang as $key => $value)
            {
                if($value > $qval)
                {
                    $qval = (float)$value;
                    $deflang = $key;
                }
            }
        }

        list($lang, $trash) = explode('-', $deflang);
    }

    $lang = strtolower($lang);

    return $lang;
}

function clean($txt, $type = '')
{
    if ($type == 'email') {
        $pattern = "/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/i";
    
        if (!preg_match($pattern, $txt)) {
            $txt = false;
        }
    }
    
    return  strip_tags(trim($txt));
}
 