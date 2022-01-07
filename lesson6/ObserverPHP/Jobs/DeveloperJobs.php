<?php


class DeveloperJobs implements SplSubject
{
    use TSingletone;

    private $jobs;
    private $observers;
    public $lastAddedJob;


    public function attach(SplObserver $observer)
    {
        $this->observers[$observer->course][] = $observer;
    }

    public function detach(SplObserver $observer)
    {
        foreach ($this->observers[$observer->course] as $key => $value){
            if ($value === $observer){
                unset($this->observers[$observer->course][$key]);
                return;
            }
        }
    }

    public function notify()
    {
        $data = $this->lastAddedJob;
        if (!isset($data)){
            echo 'Error';
            return;
        }
        foreach ($this->observers[$data->course] as $observer){
            $observer->update($this);
        }
    }
    public function addJob(Job $job){
        $this->jobs[$job->course][] = $job;
        $this->lastAddedJob = $job;
        $this->notify();
    }
}
