<?php

namespace App\Services;

use App\Models\MemberCertification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateGeneratorService
{
    /**
     * Render raw SVG string with full visual certificate details
     */
    public static function renderSvg(MemberCertification $cert): string
    {
        $user = $cert->user;
        $recipientName = $user ? $user->name : 'Anggota Medis';
        $staffId = $user ? ($user->staff_id ?? 'ALTA-MED') : 'ALTA-MED';
        $certTitle = $cert->title ?: 'Sertifikat Kompetensi Medis';
        $division = strtoupper($cert->division ?? 'PND');
        
        // Ensure certificate number exists
        if (empty($cert->certificate_number)) {
            $year = $cert->issue_date ? $cert->issue_date->format('Y') : date('Y');
            $cert->certificate_number = sprintf('ALTA/%s/%s/%04d', $division, $year, $cert->id ?: rand(100, 999));
            $cert->saveQuietly();
        }
        $certNumber = $cert->certificate_number;

        // Date formatting
        $issueDateStr = $cert->issue_date ? $cert->issue_date->translatedFormat('d F Y') : date('d F Y');
        $expiryDateStr = $cert->expiry_date ? $cert->expiry_date->translatedFormat('d F Y') : 'Berlaku Selamanya (Permanen)';

        // Issuer name
        $issuerName = $cert->issuedBy ? $cert->issuedBy->name : 'Direksi Alta Hospital';

        // Division title label
        $divLabels = [
            'GA'  => 'General Affairs (GA) & Operasional',
            'MSL' => 'Medical Science & Legal (MSL)',
            'PND' => 'Pendidikan & Pengembangan (PND)',
            'IE'  => 'Internal & Eksternal (IE)',
        ];
        $divLabel = $divLabels[$division] ?? 'Manajemen Alta Hospital';

        // Escape for XML/SVG
        $safeName       = htmlspecialchars($recipientName, ENT_XML1, 'UTF-8');
        $safeStaffId    = htmlspecialchars($staffId, ENT_XML1, 'UTF-8');
        $safeTitle      = htmlspecialchars(mb_strtoupper($certTitle), ENT_XML1, 'UTF-8');
        $safeCertNumber = htmlspecialchars($certNumber, ENT_XML1, 'UTF-8');
        $safeIssueDate  = htmlspecialchars($issueDateStr, ENT_XML1, 'UTF-8');
        $safeExpiryDate = htmlspecialchars($expiryDateStr, ENT_XML1, 'UTF-8');
        $safeIssuer     = htmlspecialchars($issuerName, ENT_XML1, 'UTF-8');
        $safeDivLabel   = htmlspecialchars($divLabel, ENT_XML1, 'UTF-8');
        $safeNotes      = htmlspecialchars($cert->notes ? Str::limit($cert->notes, 120) : 'Telah memenuhi standar kompetensi operasional medis Alta Hospital.', ENT_XML1, 'UTF-8');

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 850" width="1200" height="850">
    <defs>
        <!-- Background Gradient -->
        <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#080f24" />
            <stop offset="45%" stop-color="#0f224a" />
            <stop offset="100%" stop-color="#050a18" />
        </linearGradient>

        <!-- Gold Gradients -->
        <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#cf9b38" />
            <stop offset="30%" stop-color="#fae188" />
            <stop offset="50%" stop-color="#dfb14b" />
            <stop offset="70%" stop-color="#fdf3b4" />
            <stop offset="100%" stop-color="#ad7b22" />
        </linearGradient>

        <linearGradient id="goldPlate" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" stop-color="#f5d77f" />
            <stop offset="100%" stop-color="#b88328" />
        </linearGradient>

        <linearGradient id="ribbonGrad" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" stop-color="#059669" />
            <stop offset="50%" stop-color="#10b981" />
            <stop offset="100%" stop-color="#047857" />
        </linearGradient>

        <filter id="shadow" x="-10%" y="-10%" width="120%" height="120%">
            <feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="#000000" flood-opacity="0.6"/>
        </filter>

        <filter id="glow" x="-20%" y="-20%" width="140%" height="140%">
            <feGaussianBlur stdDeviation="3" result="blur" />
            <feComposite in="SourceGraphic" in2="blur" operator="over" />
        </filter>
    </defs>

    <!-- Canvas Background -->
    <rect width="1200" height="850" fill="url(#bgGrad)" />

    <!-- Subtle Pattern Watermark Grid -->
    <g opacity="0.03" stroke="#ffffff" stroke-width="1">
        <circle cx="600" cy="425" r="150" fill="none" />
        <circle cx="600" cy="425" r="280" fill="none" />
        <circle cx="600" cy="425" r="410" fill="none" />
        <line x1="100" y1="100" x2="1100" y2="750" />
        <line x1="100" y1="750" x2="1100" y2="100" />
    </g>

    <!-- Outer Gold Filigree Border -->
    <rect x="35" y="35" width="1130" height="780" rx="16" fill="none" stroke="url(#goldGrad)" stroke-width="3" filter="url(#shadow)" />
    <!-- Secondary Thin Inner Border -->
    <rect x="48" y="48" width="1104" height="754" rx="10" fill="none" stroke="#d4af37" stroke-width="1" stroke-dasharray="10 5" opacity="0.6" />
    <!-- Deep Container Card Inner -->
    <rect x="60" y="60" width="1080" height="730" rx="6" fill="#0c1836" fill-opacity="0.45" stroke="#ffffff" stroke-opacity="0.08" stroke-width="1" />

    <!-- Corner Ornaments -->
    <!-- Top-Left -->
    <path d="M 45 75 L 75 45 M 45 85 L 85 45 M 50 50 L 80 50 L 50 80 Z" fill="url(#goldGrad)" opacity="0.8" />
    <!-- Top-Right -->
    <path d="M 1155 75 L 1125 45 M 1155 85 L 1115 45 M 1150 50 L 1120 50 L 1150 80 Z" fill="url(#goldGrad)" opacity="0.8" />
    <!-- Bottom-Left -->
    <path d="M 45 775 L 75 805 M 45 765 L 85 805 M 50 800 L 80 800 L 50 770 Z" fill="url(#goldGrad)" opacity="0.8" />
    <!-- Bottom-Right -->
    <path d="M 1155 775 L 1125 805 M 1155 765 L 1115 805 M 1150 800 L 1120 800 L 1150 770 Z" fill="url(#goldGrad)" opacity="0.8" />

    <!-- ═══ HEADER: EMBLEM & INSTITUTION ═══ -->
    <g transform="translate(600, 125)">
        <!-- Medical Cross & Laurels Emblem -->
        <circle cx="0" cy="-10" r="32" fill="#09132c" stroke="url(#goldGrad)" stroke-width="2.5" filter="url(#glow)" />
        <!-- Red/Emerald Cross -->
        <rect x="-6" y="-24" width="12" height="28" rx="3" fill="#10b981" />
        <rect x="-14" y="-16" width="28" height="12" rx="3" fill="#10b981" />
        <circle cx="0" cy="-10" r="4" fill="#ffffff" />
        
        <!-- Stars Laurel -->
        <text x="-48" y="-12" font-size="14" fill="url(#goldGrad)" text-anchor="middle">★ ★ ★</text>
        <text x="48" y="-12" font-size="14" fill="url(#goldGrad)" text-anchor="middle">★ ★ ★</text>

        <!-- Institution Title -->
        <text x="0" y="42" font-family="'Cinzel', 'Georgia', serif" font-size="24" font-weight="900" letter-spacing="7" fill="url(#goldGrad)" text-anchor="middle" filter="url(#shadow)">
            ALTA HOSPITAL MEDICAL CENTER
        </text>
        <text x="0" y="65" font-family="'Inter', 'Segoe UI', sans-serif" font-size="12" font-weight="600" letter-spacing="3" fill="#93c5fd" text-anchor="middle" opacity="0.85">
            DEPARTEMEN PELAYANAN KESEHATAN &amp; AKREDITASI KLINIS
        </text>
    </g>

    <!-- ═══ CERTIFICATE TITLE BANNER ═══ -->
    <g transform="translate(600, 235)">
        <!-- Gold Ribbon Pill -->
        <rect x="-240" y="-18" width="480" height="36" rx="18" fill="url(#goldGrad)" filter="url(#shadow)" />
        <text x="0" y="6" font-family="'Cinzel', 'Georgia', serif" font-size="14" font-weight="900" letter-spacing="3" fill="#0b1329" text-anchor="middle">
            SERTIFIKAT KOMPETENSI RESMI
        </text>
    </g>

    <!-- ═══ RECIPIENT PRESENTATION ═══ -->
    <text x="600" y="305" font-family="'Inter', 'Segoe UI', sans-serif" font-size="13" font-weight="500" letter-spacing="4" fill="#94a3b8" text-anchor="middle">
        DIBERIKAN SECARA RESMI KEPADA:
    </text>

    <!-- Recipient Name in Large Elegant Serif -->
    <text x="600" y="365" font-family="'Cinzel', 'Georgia', serif" font-size="38" font-weight="bold" fill="#ffffff" text-anchor="middle" filter="url(#shadow)">
        {$safeName}
    </text>
    
    <!-- Accent Underline for Name -->
    <line x1="380" y1="385" x2="820" y2="385" stroke="url(#goldGrad)" stroke-width="2" stroke-linecap="round" />
    <polygon points="600,381 606,385 600,389 594,385" fill="url(#goldGrad)" />

    <!-- Staff ID & Unit -->
    <text x="600" y="415" font-family="'Courier New', monospace" font-size="13" font-weight="700" letter-spacing="2" fill="#38bdf8" text-anchor="middle">
        NO. INDUK STAF: {$safeStaffId} &bull; {$safeDivLabel}
    </text>

    <!-- ═══ CERTIFICATE DESIGNATION BOX ═══ -->
    <g transform="translate(600, 485)">
        <text x="0" y="-20" font-family="'Inter', 'Segoe UI', sans-serif" font-size="13" fill="#cbd5e1" text-anchor="middle">
            Atas pencapaian, integritas klinis, dan kelayakan operasional dalam kualifikasi:
        </text>

        <!-- Designation Box -->
        <rect x="-380" y="-5" width="760" height="52" rx="12" fill="#06122d" stroke="url(#goldGrad)" stroke-width="1.8" filter="url(#shadow)" />
        <text x="0" y="30" font-family="'Cinzel', 'Georgia', serif" font-size="20" font-weight="900" letter-spacing="2" fill="url(#goldPlate)" text-anchor="middle">
            {$safeTitle}
        </text>
    </g>

    <!-- Notes / Description -->
    <text x="600" y="575" font-family="'Inter', 'Segoe UI', sans-serif" font-size="12" fill="#94a3b8" text-anchor="middle" font-style="italic">
        "{$safeNotes}"
    </text>

    <!-- ═══ FOOTER: DETAILS, SEAL, & SIGNATURES ═══ -->
    <line x1="120" y1="620" x2="1080" y2="620" stroke="#ffffff" stroke-opacity="0.12" stroke-width="1" />

    <!-- Left Footer: Identifiers & Dates -->
    <g transform="translate(140, 660)">
        <text x="0" y="0" font-family="'Inter', sans-serif" font-size="11" font-weight="700" letter-spacing="1" fill="#94a3b8">NOMOR REGISTRASI:</text>
        <text x="0" y="20" font-family="'Courier New', monospace" font-size="13" font-weight="700" fill="#f8fafc">{$safeCertNumber}</text>

        <text x="0" y="55" font-family="'Inter', sans-serif" font-size="11" font-weight="700" letter-spacing="1" fill="#94a3b8">TANGGAL TERBIT:</text>
        <text x="0" y="75" font-family="'Inter', sans-serif" font-size="12" font-weight="600" fill="#38bdf8">{$safeIssueDate}</text>

        <text x="0" y="100" font-family="'Inter', sans-serif" font-size="10" fill="#64748b">MASA BERLAKU: {$safeExpiryDate}</text>
    </g>

    <!-- Center Footer: Golden Holographic Seal -->
    <g transform="translate(600, 715)">
        <!-- Seal Star Rays -->
        <circle cx="0" cy="0" r="48" fill="none" stroke="url(#goldGrad)" stroke-width="1.5" stroke-dasharray="4 3" />
        <circle cx="0" cy="0" r="42" fill="#081432" stroke="url(#goldGrad)" stroke-width="2.5" filter="url(#glow)" />
        
        <!-- Inner Seal Stamp -->
        <circle cx="0" cy="0" r="34" fill="none" stroke="#d4af37" stroke-width="1" />
        <text x="0" y="-12" font-family="'Cinzel', serif" font-size="8" font-weight="900" letter-spacing="1.5" fill="url(#goldGrad)" text-anchor="middle">ALTA HOSPITAL</text>
        <text x="0" y="2" font-size="14" fill="#fbbf24" text-anchor="middle">★</text>
        <text x="0" y="16" font-family="'Cinzel', serif" font-size="7" font-weight="bold" letter-spacing="2" fill="#93c5fd" text-anchor="middle">OFFICIAL SEAL</text>
        <text x="0" y="25" font-family="'Courier New', monospace" font-size="6" fill="#cbd5e1" text-anchor="middle">AUTHENTIC</text>

        <!-- Ribbon Tails -->
        <path d="M -16 40 L -24 75 L -10 65 L 0 75 L -5 40 Z" fill="url(#goldPlate)" opacity="0.8" />
        <path d="M 16 40 L 24 75 L 10 65 L 0 75 L 5 40 Z" fill="url(#goldPlate)" opacity="0.8" />
    </g>

    <!-- Right Footer: Official Endorsement & Signature -->
    <g transform="translate(1060, 660)">
        <text x="0" y="0" font-family="'Inter', sans-serif" font-size="11" font-weight="700" letter-spacing="1" fill="#94a3b8" text-anchor="end">TERVERIFIKASI &amp; DISAHKAN:</text>
        
        <!-- Digital Sign Signature Graphic Simulation -->
        <path d="M -160 30 Q -120 10 -90 35 T -40 25 T 0 35" fill="none" stroke="#38bdf8" stroke-width="2" opacity="0.85" stroke-linecap="round"/>
        <line x1="-180" y1="48" x2="0" y2="48" stroke="url(#goldGrad)" stroke-width="1.5" />

        <text x="0" y="66" font-family="'Inter', sans-serif" font-size="14" font-weight="700" fill="#ffffff" text-anchor="end">
            {$safeIssuer}
        </text>
        <text x="0" y="84" font-family="'Inter', sans-serif" font-size="11" font-weight="500" fill="#94a3b8" text-anchor="end">
            {$safeDivLabel}
        </text>
        <text x="0" y="100" font-family="'Courier New', monospace" font-size="10" fill="#10b981" font-weight="700" text-anchor="end">
            &bull; SECURE DIGITAL VERIFICATION &bull;
        </text>
    </g>
</svg>
SVG;
    }

    /**
     * Generate an official, high-resolution vector certificate image (SVG)
     * and store it in the public disk.
     *
     * @param MemberCertification $cert
     * @return string Relative path in public storage
     */
    public static function generate(MemberCertification $cert): string
    {
        $svg = self::renderSvg($cert);

        // Ensure storage directory exists
        $folder = 'certifications/generated';
        Storage::disk('public')->makeDirectory($folder);

        $filename = 'cert_' . ($cert->id ?: Str::random(8)) . '_' . time() . '.svg';
        $fullPath = $folder . '/' . $filename;

        Storage::disk('public')->put($fullPath, $svg);

        return $fullPath;
    }

    /**
     * Ensure certificate file exists or auto-generate
     */
    public static function ensureFile(MemberCertification $cert): string
    {
        if (!empty($cert->file_path) && Storage::disk('public')->exists($cert->file_path)) {
            return $cert->file_path;
        }

        $generatedPath = self::generate($cert);
        $cert->file_path = $generatedPath;
        $cert->saveQuietly();

        return $generatedPath;
    }
}
