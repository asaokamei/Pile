<?php
namespace Demo\Tasks;

class TaskDao
{
    /**
     * @var string
     */
    protected $task_file;

    /**
     * @param string $taskFile
     */
    public function __construct($taskFile)
    {
        $dir = dirname($taskFile);
        if(!file_exists($dir)) {
            mkdir($dir, 0777);
        }
        if(!is_dir($dir)) {
            throw new \RuntimeException('cannot find dir:'.$dir);
        }
        $this->task_file = $taskFile;
    }
}