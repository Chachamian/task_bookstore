<?php

namespace App;

class FlashSystem
{
    public function setMessage(string $message)
    {
        $_SESSION['flash_message'] = $message;
    }

    public function tryGetFlashMessage()
    {
        $session = $_SESSION['flash_message'] ?? null;
        unset($_SESSION['flash_message']);
        return $session;
    }
}