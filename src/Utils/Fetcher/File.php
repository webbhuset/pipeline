<?php
namespace Webbhuset\Bifrost\Utils\Fetcher;

class File implements FetcherInterface
{
    protected $importDir;

    protected $processingDir;

    protected $globPattern;

    protected $noArchive;

    protected $logger;

    public function __construct($logger, $params)
    {
        $this->logger        = $logger;
        $this->importDir     = $params['import_dir'];
        $this->processingDir = $params['processing_dir'];
        $this->globPattern   = $params['glob_pattern'];
    }

    public function init($args)
    {
        $this->noArchive = $args['no_archive'];
    }

    public function fetch()
    {
        $files = glob($this->importDir . DS . $this->globPattern, GLOB_BRACE);
        if (!isset($files[0])) {
            return false;
        }

        $file       = $files[0];
        $import     = $this->importDir;
        $processing = $this->processingDir;
        $subPath    = substr($file, strlen($import));
        $movedFile  = $processing . $subPath;
        $dir        = dirname($movedFile);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $wasCopied = copy($file, $movedFile);

        if (!$wasCopied) {
            $this->logger->log("Could not copy {$file} to {$movedFile}");
            return false;
        }

        if ($this->noArchive) {
            $this->logger->log("Copying {$file} to {$movedFile}");
        } else {
            unlink($file);
            $this->logger->log("Moving {$file} to {$movedFile}");
        }

        return $movedFile;
    }
}
