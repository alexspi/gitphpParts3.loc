<?php

require 'Interface/MessageInterface.php';
require 'Interface/MessageBase.php';
require 'Messengers/FaceBookMessage.php';
require 'Messengers/TwitterMessage.php';

$textMassage = ' Lorem Example ';
$exa = new TwitterMessage(new FaceBookMessage(new MessageBase()));
echo $exa->send($textMassage);
