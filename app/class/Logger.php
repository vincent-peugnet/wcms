<?php

namespace Wcms;

use Throwable;

/**
 * Class used to log messages.
 * It must be init once at the very beginning of the application.
 */
class Logger
{
    private static $file = null;
    private static $verbosity = 4;

    /**
     * Initialize the logger by openning the file and setting the log level.
     *
     * @param string $path the logfile's path
     * @param int $verbosity 0: no log, 1: errors only, 2: add warn, 3: add info, 4: add debug.
     */
    public static function init(string $path, int $verbosity = 4)
    {
        self::$file = fopen($path, "a") or die("Unable to open log file!");
        self::$verbosity = $verbosity;
    }

    protected static function write(string $level, string $msg, array $args = [])
    {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
        $pwd = getcwd() . DIRECTORY_SEPARATOR;
        $args = array_merge([
            "[ $level ]",
            str_replace($pwd, '', $caller['file']),
            $caller['line']
        ], $args);
        vfprintf(self::$file, date('c') . " %-9s %s(%d) $msg\n", $args);
    }

    /**
     * Log an error message using printf format.
     */
    public static function error(string $msg, ...$args)
    {
        if (self::$verbosity > 0) {
            self::write('ERROR', $msg, $args);
        }
    }

    /**
     * Log a xarning message using printf format.
     */
    public static function warning(string $msg, ...$args)
    {
        if (self::$verbosity > 1) {
            self::write('WARN', $msg, $args);
        }
    }

    /**
     * Log an info message using printf format.
     */
    public static function info(string $msg, ...$args)
    {
        if (self::$verbosity > 2) {
            self::write('INFO', $msg, $args);
        }
    }

    /**
     * Log a debug message using printf format.
     */
    public static function debug(string $msg, ...$args)
    {
        if (self::$verbosity > 3) {
            self::write('DEBUG', $msg, $args);
        }
    }

    /**
     * Log an exception.
     */
    public static function exception(Throwable $e, bool $withtrace = false)
    {
        if (self::$verbosity > 0) {
            $msg = $e->getMessage();
            if ($withtrace) {
                // TODO: Maybe print a more beautiful stack trace.
                $msg .= PHP_EOL . $e->getTraceAsString();
            }
            self::write('ERROR', $msg);
        }
    }
}
