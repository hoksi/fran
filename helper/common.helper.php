<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 컨테이너 인스턴스를 반환한다.
 * @return \Pimple\Container|null
 */
function fran()
{
    static $fran = null;

    if ($fran === null) {
        $fran = new \Pimple\Container();
    }

    return $fran;
}

/**
 * 컨테이너에 등록된 서비스를 반환한다.
 * @param $key
 * @return mixed|\Pimple\Container|null
 */
function get_fran($key = false)
{
    if ($key) {
        return fran()[$key];
    }

    return fran();
}

/**
 * 컨테이너에 서비스를 등록한다.
 * @param $key
 * @param $value
 * @return void
 */
function set_fran($key, $value)
{
    fran()[$key] = $value;
}

/**
 * 컨테이너에 등록된 쿼리빌더를 반환한다.
 * @param $key
 * @return void
 */
function qb($database = false) : \CI_Qb
{
    if ($database) {
        return fran()['qb'][$database];
    }


    return fran()['qb'];
}

function fb_import($resource, $params = false, $opt = false)
{
    $res_parse = explode('.', $resource);
    if (!empty($res_parse) && ($res_parse[0] == 'db' || count($res_parse) >= 2)) {
        $res_type = array_shift($res_parse);

        return getResource($res_type, $res_parse, $params, $opt);
    } else {
        show_error('Resource is Empty!');
    }
}

function getResource($type, $res_params, $params, $opt)
{
    switch ($type) {
        case 'model':
            return getObj($res_params, 'model');
    }

    return false;
}

function getObj($class, $postfix, $params = null)
{
    $coreClass   = '\\Forbiz\\'.ucfirst($postfix).'\\'.ucfirst($class[1]);

    if (class_exists($coreClass)) {
        return new $coreClass($params);
    }

    return false;
}

function getForbiz()
{
    return fran();
}

function is_cli()
{
    return (PHP_SAPI === 'cli' OR defined('STDIN'));
}

function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered')
{
    $status_code = abs($status_code);
    if ($status_code < 100)
    {
        $exit_status = $status_code + 9; // 9 is EXIT__AUTO_MIN
        $status_code = 500;
    }
    else
    {
        $exit_status = 1; // EXIT_ERROR
    }

    if (is_cli())
    {
        $message = "\t".(is_array($message) ? implode("\n\t", $message) : $message);
    }
    else
    {
        set_status_header($status_code);
        $message = '<p>'.(is_array($message) ? implode('</p><p>', $message) : $message).'</p>';
    }

    echo $message;
    exit($exit_status);
}

/**
 * 로그를 작성한다.
 * @param $level
 * @param $msg
 * @return bool
 */
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

/**
 * Set HTTP Status Header
 *
 * @param	int	the status code
 * @param	string
 * @return	void
 */
function set_status_header($code = 200, $text = '')
{
    if (is_cli())
    {
        return;
    }

    if (empty($code) OR ! is_numeric($code))
    {
        show_error('Status codes must be numeric', 500);
    }

    if (empty($text))
    {
        is_int($code) OR $code = (int) $code;
        $stati = array(
            100	=> 'Continue',
            101	=> 'Switching Protocols',

            200	=> 'OK',
            201	=> 'Created',
            202	=> 'Accepted',
            203	=> 'Non-Authoritative Information',
            204	=> 'No Content',
            205	=> 'Reset Content',
            206	=> 'Partial Content',

            300	=> 'Multiple Choices',
            301	=> 'Moved Permanently',
            302	=> 'Found',
            303	=> 'See Other',
            304	=> 'Not Modified',
            305	=> 'Use Proxy',
            307	=> 'Temporary Redirect',

            400	=> 'Bad Request',
            401	=> 'Unauthorized',
            402	=> 'Payment Required',
            403	=> 'Forbidden',
            404	=> 'Not Found',
            405	=> 'Method Not Allowed',
            406	=> 'Not Acceptable',
            407	=> 'Proxy Authentication Required',
            408	=> 'Request Timeout',
            409	=> 'Conflict',
            410	=> 'Gone',
            411	=> 'Length Required',
            412	=> 'Precondition Failed',
            413	=> 'Request Entity Too Large',
            414	=> 'Request-URI Too Long',
            415	=> 'Unsupported Media Type',
            416	=> 'Requested Range Not Satisfiable',
            417	=> 'Expectation Failed',
            422	=> 'Unprocessable Entity',
            426	=> 'Upgrade Required',
            428	=> 'Precondition Required',
            429	=> 'Too Many Requests',
            431	=> 'Request Header Fields Too Large',

            500	=> 'Internal Server Error',
            501	=> 'Not Implemented',
            502	=> 'Bad Gateway',
            503	=> 'Service Unavailable',
            504	=> 'Gateway Timeout',
            505	=> 'HTTP Version Not Supported',
            511	=> 'Network Authentication Required',
        );

        if (isset($stati[$code]))
        {
            $text = $stati[$code];
        }
        else
        {
            show_error('No status text available. Please check your status code number or supply your own message text.', 500);
        }
    }

    if (strpos(PHP_SAPI, 'cgi') === 0)
    {
        header('Status: '.$code.' '.$text, TRUE);
        return;
    }

    $server_protocol = (isset($_SERVER['SERVER_PROTOCOL']) && in_array($_SERVER['SERVER_PROTOCOL'], array('HTTP/1.0', 'HTTP/1.1', 'HTTP/2'), TRUE))
        ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
    header($server_protocol.' '.$code.' '.$text, TRUE, $code);
}