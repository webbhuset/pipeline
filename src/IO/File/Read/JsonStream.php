<?php

namespace Webbhuset\Whaskell\IO\File\Read;

use JsonStreamingParser\Listener;
use JsonStreamingParser\Parser;
use Webbhuset\Whaskell\FunctionInterface;
use Webbhuset\Whaskell\WhaskellException;

class JsonStream implements FunctionInterface, Listener
{
    /**
     * Depth of items in json file.
     *
     * @var int
     */
    protected $itemDepth = 1;

    /**
     * Json parser.
     *
     * @var \JsonStreamingParser\Parser
     */
    protected $parser;

    /**
     * Item stack.
     *
     * @var array
     */
    protected $items;

    /**
     * Current item.
     *
     * @var array
     */
    protected $item;

    /**
     * Keys.
     *
     * @var string
     */
    protected $keys;

    /**
     * Current depth.
     *
     * @var int
     */
    protected $currentDepth;

    /**
     * Is done.
     *
     * @var bool
     */
    protected $done;


    public function __construct(array $params = [])
    {
        if (isset($params['depth'])) {
            $this->itemDepth = $params['depth'];
        }
    }

    public function __invoke($files, $finalize = true)
    {
        foreach ($files as $filename) {
            if (!is_file($filename)) {
                $msg = "File not found {$filename}";
                if ($this->observer) {
                    $this->observer->observeError($filename, $msg);
                }

                continue;
            }

            $file = fopen($filename, 'r');

            $this->initRead($file);

            while (!$this->done) {
                $item = $this->getNextItem();
                if ($item) {
                    yield $item;
                }
            }

            fclose($file);
        }
    }

    /**
     * Initialize json parser.
     *
     * @param string $file
     *
     * @return void
     */
    protected function initRead($file)
    {
        try {
            $this->done   = false;
            $this->parser = new Parser($file, $this);
        } catch(Exception $e) {
            throw new WhaskellException(sprintf(
                "Failed to instantiate json parser: %s",
                $e->getMessage())
            );
        }
    }

    /**
     * Get next item.
     *
     * @return array
     */
    protected function getNextItem()
    {
        try {
            $this->done = $this->parser->parse();
        } catch (Exception $e) {
            throw new WhaskellException(sprintf(
                "Json parsing failed: %s",
                $e->getMessage())
            );
        }

        if ($this->done) {
            return false;
        }

        $this->item = $this->flattenItem($this->item);

        return $this->item;
    }

    /**
     * Flattens the 'type' + 'value' array structure.
     *
     * @param array $item
     *
     * @return array
     */
    protected function flattenItem($item)
    {
        foreach ($item as $key => $value) {
            if (is_array($value)) {
                $item[$key] = $this->flattenItem($value['value']);
            }
        }

        return $item;
    }

    /**
     * Process start of document.
     *
     * @return void
     */
    public function startDocument()
    {
        $this->currentDepth = 0;
    }

    /**
     * Process end of document.
     *
     * @return void
     */
    public function endDocument()
    {
        return;
    }

    /**
     * Process start of object.
     *
     * @return void
     */
    public function startObject()
    {
        $this->startItem('object');
    }

    /**
     * Process end of object.
     *
     * @return void
     */
    public function endObject()
    {
        $this->endItem();
    }

    /**
     * Process start of array.
     *
     * @return void
     */
    public function startArray()
    {
        $this->startItem('array');
    }

    /**
     * Process end of array.
     *
     * @return void
     */
    public function endArray()
    {
        $this->endItem();
    }

    /**
     * Start new item.
     *
     * @param string $type
     *
     * @return void
     */
    protected function startItem($type)
    {
        $this->currentDepth++;
        $this->items[] = ['type' => $type, 'value' => []];
    }

    /**
     * End of item.
     *
     * @return void
     */
    protected function endItem()
    {
        $item = array_pop($this->items);
        $this->item = $item['value'];
        $this->currentDepth--;

        $parent = array_pop($this->items);
        if ($this->currentDepth == $this->itemDepth) {
            $this->parser->stop();
        } elseif ($this->currentDepth > $this->itemDepth) {
            if ($parent['type'] == 'object') {
                $key = end($this->keys);
                $parent['value'][$key] = $item;
            } else {
                $parent['value'][] = $item;
            }
        }
        $this->items[] = $parent;

        if ($parent['type'] == 'object') {
            array_pop($this->keys);
        }
    }

    /**
     * Process key.
     *
     * @param string $key
     *
     * @return void
     */
    public function key($key)
    {
        $this->keys[] = $this->getUniqueKey($key);
    }

    /**
     * Gets an unique key.
     *
     * @param string $name
     * @param string $num
     *
     * @return string
     */
    protected function getUniqueKey($name, $num = 0)
    {
        $item   = end($this->items);
        $key    = $num ? "{$name}_{$num}" : $name;
        if (!isset($item['value'][$key])) {
            return $key;
        } else {
            return $this->getUniqueKey($name, $num + 1);
        }
    }

    /**
     * Process value.
     *
     * @param mixed $value
     *
     * @return void
     */
    public function value($value)
    {
        $currentItem = array_pop($this->items);

        if ($currentItem['type'] == 'object') {
            $key = array_pop($this->keys);
            $currentItem['value'][$key] = $value;
        } else {
            $currentItem['value'][] = $value;
        }

        $this->items[] = $currentItem;
    }

    /**
     * Process whitespace.
     *
     * @param mixed $whitespace
     *
     * @return void
     */
    public function whitespace($whitespace)
    {
        return;
    }
}
