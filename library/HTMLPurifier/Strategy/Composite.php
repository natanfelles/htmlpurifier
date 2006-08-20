<?php

require_once 'HTMLPurifier/Strategy.php';
require_once 'HTMLPurifier/Config.php';

/**
 * Composite strategy that runs multiple strategies on tokens.
 */
class HTMLPurifier_Strategy_Composite
{
    
    /**
     * List of strategies to run tokens through.
     */
    var $strategies = array();
    
    function HTMLPurifier_Strategy_Composite() {
        trigger_error('Attempt to instantiate abstract object', E_USER_ERROR);
    }
    
    function execute($tokens, $config) {
        foreach ($this->strategies as $strategy) {
            $tokens = $strategy->execute($tokens, $config);
        }
        return $tokens;
    }
    
}

?>