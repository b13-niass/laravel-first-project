<?php

namespace App\Services\Interfaces;

interface MessageService{
    public function configure();
    public function send($to, $subject, $body);
}
