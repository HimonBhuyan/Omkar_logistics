<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Bilty;
use Dompdf\Dompdf;
use Dompdf\Options;

$logoPath = __DIR__ . '/public/assets/logo.jpg';
$logoBase64 = '';
if (file_exists($logoPath)) {
    $logoData = file_get_contents($logoPath);
    $logoBase64 = 'data:image/jpeg;base64,' . base64_encode($logoData);
}

$outputDir = __DIR__ . '/storage/app/public/receipts';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}

$artifactsDir = '/home/sahil/.gemini/antigravity-ide/brain/1cb76a33-b518-4355-b411-6c9b8b602125';

$bilties = [
    1 => 'sample_receipt_1_single_row.pdf',
    2 => 'sample_receipt_2_two_rows.pdf'
];

foreach ($bilties as $id => $filename) {
    $bilty = Bilty::with(['fromLocation', 'toLocation', 'consignor', 'consignee', 'billingParty', 'items'])->find($id);
    if (!$bilty) {
        echo "Bilty #{$id} not found.\n";
        continue;
    }

    $isPdf = true;
    $html = view('bilty.print', compact('bilty', 'isPdf'))->render();

    // Replace relative logo asset with base64 embedded image
    if ($logoBase64) {
        $html = preg_replace('/src="[^"]*assets\/logo\.jpg"/', 'src="' . $logoBase64 . '"', $html);
    }

    // Configure Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultMediaType', 'print');
    $options->set('defaultFont', 'Helvetica');
    $options->set('dpi', 96);

    $dompdf = new Dompdf($options);
    $dompdf->setPaper('a5', 'landscape');
    $dompdf->loadHtml($html);
    $dompdf->render();

    $pdfOutput = $dompdf->output();

    $destPath1 = $outputDir . '/' . $filename;
    file_put_contents($destPath1, $pdfOutput);

    $destPath2 = $artifactsDir . '/' . $filename;
    file_put_contents($destPath2, $pdfOutput);

    $destPathPublic = __DIR__ . '/public/' . $filename;
    file_put_contents($destPathPublic, $pdfOutput);

    echo "✓ Generated PDF: {$filename} (Size: " . strlen($pdfOutput) . " bytes)\n";
    echo "  - Storage Path: {$destPath1}\n";
    echo "  - Artifact Path: {$destPath2}\n";
    echo "  - Public Path: {$destPathPublic}\n";
}
