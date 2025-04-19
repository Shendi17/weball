<?php

class LogRotator {
    private $logFile;
    private $maxSize; // Taille maximale en octets
    private $maxFiles; // Nombre maximum de fichiers de rotation

    public function __construct($logFile, $maxSize = 5242880, $maxFiles = 5) { // 5MB par défaut
        $this->logFile = $logFile;
        $this->maxSize = $maxSize;
        $this->maxFiles = $maxFiles;
    }

    public function write($message) {
        if ($this->shouldRotate()) {
            $this->rotate();
        }

        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message" . PHP_EOL;
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }

    private function shouldRotate() {
        return file_exists($this->logFile) && filesize($this->logFile) >= $this->maxSize;
    }

    private function rotate() {
        // Supprimer le fichier le plus ancien si nécessaire
        $lastRotation = $this->maxFiles - 1;
        if (file_exists($this->logFile . '.' . $lastRotation)) {
            unlink($this->logFile . '.' . $lastRotation);
        }

        // Rotation des fichiers existants
        for ($i = $lastRotation - 1; $i >= 0; $i--) {
            $oldFile = $this->logFile . ($i > 0 ? '.' . $i : '');
            $newFile = $this->logFile . '.' . ($i + 1);
            if (file_exists($oldFile)) {
                rename($oldFile, $newFile);
            }
        }

        // Renommer le fichier de log actuel
        if (file_exists($this->logFile)) {
            rename($this->logFile, $this->logFile . '.1');
        }
    }
}
