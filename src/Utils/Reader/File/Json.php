<?php
namespace Webbhuset\Bifrost\Core\Utils\Reader\File;
use \Webbhuset\Bifrost\Core\Utils\Reader\AbstractReader;
use \Webbhuset\Bifrost\Core\BifrostException;

class Json extends AbstractReader
    implements \JsonStreamingParser\Listener
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
     * Item stack.
     *
     * @var array
     */
    protected $size;

    /**
     * Current item.
     *
     * @var array
     */
    protected $item;

    /**
     * Flag for if first item has been written.
     *
     * @var bool
     */
    protected $firstWrite = true;

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
     * Current.
     *
     * @var int
     */
    protected $current;

    public function init($args)
    {
        if (!is_file($args['filename'])) {
            throw new BifrostException("File not found {$args['filename']}");
        }
        $this->file = fopen($args['filename'], 'r');

        try {
            $this->parser = new \JsonStreamingParser\Parser($this->file, $this);
        } catch(Exception $e) {
            throw new BifrostException("Failed to instantiate json parser: " . $e->getMessage());
        }

        parent::init($args);
    }

    public function getEntityCount()
    {
        $count = parent::getEntityCount();

        $this->parser->reset();
        $this->current = 0;

        return $count;
    }

    public function finalize()
    {
        parent::finalize();
        fclose($this->file);
    }


    public function getData()
    {
        $done = $this->parser->parse();

        if ($done) {
            return false;
        }
        $this->current++;

        $item = $this->flattenItem($this->item);

        return [$item];
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
        if (!isset($item[$key])) {
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

    }
}
