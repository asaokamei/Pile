<?php
namespace Demo\Tasks;

class TaskDao
{
    const ACTIVE = '1';
    const DONE   = 'D';
    
    /**
     * @var string
     */
    protected $task_file;

    /**
     * @var array
     */
    protected $tasks = [];
    
    protected $max_task_id = 0;

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

    public function initialize()
    {
        $this->tasks = array(
            [1, self::ACTIVE, 'set done this task'],
            [2, self::ACTIVE, 'modify this task'],
            [3, self::ACTIVE, 'add a new task'],
            [4, self::ACTIVE, 'try validation? set all blank and update/insert a task. '],
            [5, self::ACTIVE, 'delete all finished tasks and setup the task list'],
        );
        $this->save();
    }
    
    protected function save()
    {
        $fp = fopen($this->task_file, 'wb+');
        rewind($fp);
        foreach($this->tasks as $task) {
            fputcsv($fp, $task);
        }
    }
    
    protected function load()
    {
        if(!file_exists($this->task_file)) {
            return;
        }
        $fp = fopen($this->task_file, 'rb');
        while($csv = fgetcsv($fp)) {
            $this->tasks[] = $csv;
            $this->max_task_id = $this->max_task_id < $csv[0] ? $csv[0] :$this->max_task_id;   
        }
    }

    /**
     * @return array
     */
    public function getTasks()
    {
        $this->load();
        return $this->tasks;
    }
}