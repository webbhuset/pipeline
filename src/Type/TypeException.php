<?php

namespace Webbhuset\Bifrost\Type;

use Exception;

/**
 * Exception class used for Bifrost-specific exceptions.
 *
 * @author    Webbhuset AB <info@webbhuset.se>
 * @copyright Copyright (C) 2016 Webbhuset AB
 */
class TypeException extends Exception
{
    protected $errors;

    public function __construct($message, $code = 0, Exception $previous = null, $errors)
    {
        $this->errors = $errors;

        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        $message    = $this->getMessage();
        $trace      = $this->getTraceAsString();
        $file       = $this->getFile();
        $line       = $this->getLine();
        $class      = __CLASS__;

        $result = "exception '{$class}' with message '{$message}' in {$file}:{$line}";
        /**
         * @TODO Implement a prettier format.
         */
        $result .= print_r($this->errors, true);
        $result .= "Stack Trace:\n".$trace;

        return $result;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
