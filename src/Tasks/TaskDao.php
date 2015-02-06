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

    /**
     * loads tasks from csv file.
     */
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

    /**
     * saves tasks to csv file.
     */
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

    /**
     * @param int $id
     * @return bool
     */
    public function toggle($id)
    {
        $this->load();
        foreach($this->tasks as $idx => $task) {
            if($task[0] === (string) $id) {
                if($task[1] === self::ACTIVE) {
                    $this->tasks[$idx][1] = self::DONE;
                } else {
                    $this->tasks[$idx][1] = self::ACTIVE;
                }
                $this->save();
                return true;
            }
        }
        return false;
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        $this->load();
        foreach($this->tasks as $idx => $task) {
            if($task[0] === (string) $id) {
                if($task[1] === self::DONE) {
                    unset($this->tasks[$idx]);
                }
                $this->save();
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $task
     * @return int
     */
    public function insert($task)
    {
        $this->load();
        $this->max_task_id++;
        $task = [
            $this->max_task_id,
            self::ACTIVE,
            $task,
        ];
        $this->tasks[] = $task;
        $this->save();
        return $this->max_task_id;
    }
}