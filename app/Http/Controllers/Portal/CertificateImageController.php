<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\MemberCertification;
use App\Services\CertificateGeneratorService;
use Illuminate\Http\Response;

class CertificateImageController extends Controller
{
    /**
     * Render high-resolution official Alta Hospital Certificate image (SVG)
     * Guaranteed to return the genuine certificate template image!
     */
    public function show(MemberCertification $certification): Response
    {
        $svg = CertificateGeneratorService::renderSvg($certification);

        return response($svg, 200, [
            'Content-Type'        => 'image/svg+xml; charset=utf-8',
            'Cache-Control'       => 'public, max-age=86400',
            'Content-Disposition' => 'inline; filename="sertifikat_' . $certification->id . '.svg"',
        ]);
    }
}
