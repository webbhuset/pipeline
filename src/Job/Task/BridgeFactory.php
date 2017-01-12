<?php
namespace Webbhuset\Bifrost\Core\Job\Task;
use \Webbhuset\Bifrost\Core\Utils as Utils;
use \Webbhuset\Bifrost\Core\Utils\Reader\ReaderInterface;
use \Webbhuset\Bifrost\Core\BifrostException;

/**
 * Bridge factory.
 *
 * @uses      Factory
 * @author    Webbhuset AB <info@webbhuset.se>
 * @copyright Copyright (C) 2016 Webbhuset AB
 */
abstract class BridgeFactory
{
    /**
     * Bridge specification.
     *
     * This string should be overwritten to contain your bridge specification.
     * Each part of the string should be a function that returns a processor
     * factory or an array of processor factories. The parts are separated by
     * '->'. To split the flow, put the subflows on separate lines and increase
     * the indentation level by one.
     *
     * Example:
     *  aaa->bbb->ccc
     *      ->ddd
     *      ->eee->fff
     *
     * The above example would result in two flows, a-b-c-d  and a-b-c-e-f.
     *
     * @var string
     */
    protected $bridgeSpecification;

    /**
     * Parsed bridge specification array.
     *
     * @var array
     */
    protected $bridgeArray;

    /**
     * Logger.
     *
     * @var Utils\Logger\LoggerInterface
     */
    protected $logger;


    /**
     * Constructor.
     *
     * @param Utils\Log\LogInterface $log
     * @param array $factories
     *
     * @return void
     */
    public function __construct(Utils\Logger\LoggerInterface $logger)
    {
        $this->bridgeArray = Utils\BridgeSpecificationParser::parse($this->bridgeSpecification);
        $this->logger       = $logger;
    }

    /**
     * Creates a bridge and returns the first part (i.e. the reader).
     *
     * @return ReaderInterface
     */
    public function create()
    {
        return $this->recursiveCreate($this->bridgeArray, true);
    }

    /**
     * Recursively creates processorFactories and uses them to create processors.
     *
     * @param array $array
     *
     * @return Utils\ProcessorInterface
     */
    protected function recursiveCreate($array, $isRoot)
    {

        $children   = [];
        $factories  = call_user_func([$this, $array['name']]);

        if (!is_array($factories)) {
            $factories = [$factories];
        }
        $factories  = array_reverse($factories);

        foreach ($array['children'] as $child) {
            $children[] = $this->recursiveCreate($child, false);
        }

        foreach ($factories as $factory) {
            $processor = $factory->create($this->logger, $children);
            if (!$isRoot && $processor instanceof ReaderInterface) {
                throw new BifrostException('Only the first processor can implement ReaderInterface');
            }
            $children = [$processor];
        }

        $children = reset($children);

        return $children;
    }
}
