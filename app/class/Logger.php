<?php

namespace Wcms;

use RuntimeException;
use Throwable;

/**
 * Class used to log messages.
 * It must be init once at the very beginning of the application.
 */
class Logger
{
    private static $file = false;
    private static int $verbosity = 4;

    /**
     * Initialize the logger by openning the file and setting the log level.
     *
     * @param string $path the logfile's path
     * @param int $verbosity 0: no log, 1: errors only, 2: add warn, 3: add info, 4: add debug.
     * @throws RuntimeException if failed to create logfile.
     */
    public static function init(string $path, int $verbosity = 4): void
    {
        if (!is_dir(dirname($path))) {
            throw new RuntimeException("Parent directory of '$path' does not exist.");
        }
        if (!is_writable(dirname($path))) {
            throw new RuntimeException("Parent directory of '$path' is not writable.");
        }
        if (is_file($path) && !is_writable($path)) {
            throw new RuntimeException("The logfile '$path' is not writable.");
        }
        self::$file = fopen($path, "a");
        if (self::$file === false) {
        }
        self::$verbosity = $verbosity;
    }

    public static function close(): void
    {
        if (self::$file !== false) {
            fclose(self::$file);
            self::$file = false;
        }
    }

    protected static function write(string $level, string $msg, array $args = []): void
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

    protected static function exceptionmessage(Throwable $e): string
    {
        return "{$e->getMessage()} in {$e->getFile()}({$e->getLine()})";
    }

    /**
     * Log an error message using printf format.
     */
    public static function error(string $msg, ...$args): void
    {
        if (self::$verbosity > 0) {
            self::write('ERROR', $msg, $args);
        }
    }

    /**
     * Log a warning message using printf format.
     */
    public static function warning(string $msg, ...$args): void
    {
        if (self::$verbosity > 1) {
            self::write('WARN', $msg, $args);
        }
    }

    /**
     * Log an info message using printf format.
     */
    public static function info(string $msg, ...$args): void
    {
        if (self::$verbosity > 2) {
            self::write('INFO', $msg, $args);
        }
    }

    /**
     * Log a debug message using printf format.
     */
    public static function debug(string $msg, ...$args): void
    {
        if (self::$verbosity > 3) {
            self::write('DEBUG', $msg, $args);
        }
    }

    /**
     * Log an exception as an error.
     */
    public static function errorex(Throwable $e, bool $withtrace = false): void
    {
        if (self::$verbosity > 0) {
            $msg = self::exceptionmessage($e);
            if ($withtrace) {
                // TODO: Maybe print a more beautiful stack trace.
                $msg .= PHP_EOL . $e->getTraceAsString();
            }
            self::write('ERROR', $msg);
        }
    }

    /**
     * Log an exception as a warning.
     */
    public static function warningex(Throwable $e): void
    {
        if (self::$verbosity > 1) {
            self::write('WARN', self::exceptionmessage($e));
        }
    }

    /**
     * Log an exception as an info.
     */
    public static function infoex(Throwable $e): void
    {
        if (self::$verbosity > 2) {
            self::write('INFO', self::exceptionmessage($e));
        }
    }

    /**
     * Log an exception as a debug.
     */
    public static function debugex(Throwable $e): void
    {
        if (self::$verbosity > 3) {
            self::write('DEBUG', self::exceptionmessage($e));
        }
    }
}
