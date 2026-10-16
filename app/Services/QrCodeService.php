<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Generate pure SVG string for the given payload string.
     */
    public function generateSvg(string $data, int $size = 200): string
    {
        return (string) QrCode::size($size)
            ->format('svg')
            ->generate($data);
    }

    /**
     * Generate Base64 Data URL for the given payload string.
     */
    public function generateDataUrl(string $data, int $size = 200): string
    {
        $svg = $this->generateSvg($data, $size);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
