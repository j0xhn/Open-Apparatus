<?php
class Log_error_log extends Log
{
    public $_type = PEAR_LOG_TYPE_SYSTEM;
    public $_destination = '';
    public $_extra_headers = '';
    function Log_error_log($name, $ident = '', $conf = array(),$level = PEAR_LOG_DEBUG)
    {
    }
    function log($message, $priority = null)
    {
    }
}
