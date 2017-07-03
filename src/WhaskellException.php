<?php

namespace Webbhuset\Whaskell;

/**
 * Exception class used for Whaskell-specific exceptions.
 *
 * @author    Webbhuset AB <info@webbhuset.se>
 * @copyright Copyright (C) 2016 Webbhuset AB
 */
class WhaskellException extends \Exception
{
    /**
     * Message prefix.
     *
     * @var string
     * @access protected
     */
    protected $_prefix = 'Error';

    /**
     * Constructor.
     *
     * @param string $message
     * @param int $code
     * @param Exception $previous
     *
     * @return void
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Returns message prefix.
     *
     * @access public
     * @return string
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }
}
