<?php defined('BASEPATH') OR exit('No direct script access allowed');
function fran()
{
    static $fran = null;

    if ($fran === null) {
        $fran = new \Pimple\Container();
    }

    return $fran;
}

function get_fran($key = false)
{
    if ($key) {
        return fran()[$key];
    }

    return fran();
}

function set_fran($key, $value)
{
    fran()[$key] = $value;
}

function qb($database = false) : \CI_Qb
{
    if ($database) {
        return fran()['qb'][$database];
    }


    return fran()['qb'];
}

function log_message($level, $msg)
{
    if (!defined('THRESHOLD_LOG_LEVEL') && THRESHOLD_LOG_LEVEL > 0) {
        return false;
    }

    $_levels = ['ERROR' => 1, 'DEBUG' => 2, 'INFO' => 3, 'ALL' => 4];

    $level = strtoupper($level);

    if (( ! isset($_levels[$level]) || ($_levels[$level] > THRESHOLD_LOG_LEVEL))) {
        return FALSE;
    }

    $filepath = BASEPATH.'../log/log-'.date('Y-m-d').'.php';
    $message = '';

    if ( ! file_exists($filepath))
    {
        $newfile = TRUE;
        $message .= "<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>\n\n";
    }

    if ( ! $fp = @fopen($filepath, 'ab'))
    {
        return FALSE;
    }

    if ( ! $fp = @fopen($filepath, 'ab'))
    {
        return FALSE;
    }

    flock($fp, LOCK_EX);

    $date = date('Y-m-d H:i:s');
    $message .= $level.' - '.$date.' --> '.$msg.PHP_EOL;

    for ($written = 0, $length = mb_strlen($message, '8bit'); $written < $length; $written += $result)
    {
        if (($result = fwrite($fp, mb_substr($message, $written, null, '8bit'))) === FALSE)
        {
            break;
        }
    }

    flock($fp, LOCK_UN);
    fclose($fp);

    if (isset($newfile) && $newfile === TRUE)
    {
        chmod($filepath, 0644);
    }

    return is_int($result);
}