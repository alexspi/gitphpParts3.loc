<?php

require 'Jobs/Job.php';
require 'Jobs/DeveloperJobs.php';
require 'Observers/DeveloperObserver.php';
require 'Singletone/TSingletone.php';


$subject = DeveloperJobs::getInstance();
$newJob = new Job('Yahoo', 'PHP');
$newJob1 = new Job('Google', 'PHP');
$php1 = new DeveloperObserver('Nikita', 'PHP');
$php2 = new DeveloperObserver('Alex', 'PHP');
$java1 = new DeveloperObserver('Dmitri', 'Java');
$newJavaJob = new Job('Yandex', 'Java');


$subject->attach($php1);
$subject->attach($php2);
$subject->addJob($newJob);
$subject->detach($php2);
$subject->addJob($newJob1);
echo '<br>' . PHP_EOL;
$subject->attach($java1);
$subject->addJob($newJavaJob);
$subject->addJob($newJavaJob);
