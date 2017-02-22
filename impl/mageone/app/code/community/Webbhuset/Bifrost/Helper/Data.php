<?php

/**
 * Helper class for Bifrost module.
 *
 * @author    Webbhuset AB <info@webbhuset.se>
 * @copyright Copyright (C) 2016 Webbhuset AB
 */
class Webbhuset_Bifrost_Helper_Data
    extends Mage_Core_Helper_Abstract
{
    /**
     * Get available jobs from config.
     *
     * @return array
     */
    public function getJobs()
    {
        $node = Mage::getConfig()->getNode('global/webbhuset/bifrost');

        if (!$node) {
            return [];
        }

        $config = array_keys($node->asArray());
        $jobs   = [];
        foreach ($config as $type) {
            $array = Mage::getConfig()
                ->getNode("global/webbhuset/bifrost/{$type}")
                ->asArray();
            $jobs[$type] = $array;
        }

        return $jobs;
    }
}
