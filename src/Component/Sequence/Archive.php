<?php

namespace Webbhuset\Bifrost\Core\Component\Sequence;

use Webbhuset\Bifrost\Core\Component;
use Webbhuset\Bifrost\Core\BifrostException;

class Archive implements Component\ComponentInterface
{
    protected $component;


    public function __construct(array $config)
    {
        $requiredConfig = [
            'from_dir',
            'processing_dir',
            'archive_dir',
            'file_component',
            'main_component',
        ];
        foreach ($requiredConfig as $required) {
            if (!isset($config[$required])) {
                throw new BifrostException("{$required} is required.");
            }
        }

        $fromDir        = $config['from_dir'];
        $processingDir  = $config['processing_dir'];
        $archiveDir     = $config['archive_dir'];
        $fileComponent  = $config['file_component'];
        $mainComponent  = $config['main_component'];

        $moveToProcessing = function($oldPath) use ($fromDir, $processingDir) {
            if (substr($oldPath, 0, strlen($fromDir)) != $fromDir) {
                throw new BifrostException("File '{$oldPath}' is not in directory '{$fromDir}'.");
            }
            $subPath = substr($oldPath, strlen($fromDir));
            $newPath = $processingDir . $subPath;

            return $newPath;
        };

        $moveToArchive = function($oldPath) use ($processingDir, $archiveDir) {
            if (substr($oldPath, 0, strlen($processingDir)) != $processingDir) {
                throw new BifrostException("File '{$oldPath}' is not in directory '{$processingDir}'.");
            }
            $subPath = substr($oldPath, strlen($processingDir));
            $newPath = $archiveDir . $subPath;

            return $newPath;
        };

        $tmpFile = tempnam(sys_get_temp_dir(), 'bifrost_');

        $replaceWithTmpFile = function($item) use ($tmpFile) {
            return $tmpFile;
        };


        $processing = new Component\Flow\Pipeline([
            $fileComponent,
            new Component\File\Move($moveToProcessing),
            new Component\Write\File\Line($tmpFile),
        ]);

        if (is_array($mainComponent)) {
            $components = [];
            foreach ($mainComponent as $key => $component) {
                $components[$key] = new Component\Flow\Pipeline([
                    new Component\Transform\Map($replaceWithTmpFile),
                    new Component\Read\File\Line(['ignore_empty' => true]),
                    $component,
                ]);
            }
            $main = new Component\Flow\TaskList($components, 'archive_main');
        } else {
            $main = new Component\Flow\Pipeline([
                new Component\Transform\Map($replaceWithTmpFile),
                new Component\Read\File\Line(['ignore_empty' => true]),
                $mainComponent,
            ]);
        }

        $archive = new Component\Flow\Pipeline([
            new Component\Transform\Map($replaceWithTmpFile),
            new Component\Read\File\Line(['ignore_empty' => true]),
            new Component\File\Move($moveToArchive)
        ]);

        $taskList = new Component\Flow\TaskList([
            'processing'    => $processing,
            'main'          => $main,
            'archive'       => $archive,
        ], 'archive');

        $this->component = $taskList;
    }

    public function process($items, $finalize = true)
    {
        return $this->component->process($items, $finalize);
    }
}
