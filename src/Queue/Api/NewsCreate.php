<?php

// src/Message/SmsNotification.php
namespace App\Queue\Api;

class NewsCreate
{
    public function __construct(
        public object $content,
    ) {
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
