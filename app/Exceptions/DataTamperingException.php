<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class DataTamperingException extends RuntimeException
{
    public static function forRevision(int $revisionId): self
    {
        return new self("Peringatan Keamanan STRIDE: Integritas data revisi ID {$revisionId} tidak valid (terdeteksi perubahan tidak sah). Rollback dibatalkan.");
    }
}
