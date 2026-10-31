<?php

use App\Http\Controllers\Controller;
use App\Http\Kernel;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;

// Phase 0: CSRF is back on for web routes, and uploads are restricted.
// Laravel skips CSRF checks while running tests, so the configuration is asserted directly.

it('runs CSRF verification on every web route', function () {
    $groups = (new ReflectionProperty(Kernel::class, 'middlewareGroups'));
    $groups->setAccessible(true);

    expect($groups->getValue(app(Kernel::class))['web'])->toContain(VerifyCsrfToken::class);
});

it('exempts only gateway callbacks, punchout setup and the legacy mobile endpoints', function () {
    $except = (new ReflectionProperty(VerifyCsrfToken::class, 'except'));
    $except->setAccessible(true);
    $paths = $except->getValue(app(VerifyCsrfToken::class));

    expect($paths)->toContain('punchout/setup', 'success', 'atomecallback', 'grabpaywebhook', 'grabpay-complete', 'customerlogin')
        ->and($paths)->not->toContain('admin/*', '*', 'admin/verifylogin', 'logincheck', 'placeorder', 'applycouponcode');
});

function uploadName(UploadedFile $file)
{
    $controller = new class extends Controller {
        public function name($file) { return $this->safeUploadName($file); }
    };

    return $controller->name($file);
}

it('keeps a slugged version of the original name with a safe extension', function () {
    $name = uploadName(UploadedFile::fake()->image('Bosch Drill (New).JPG'));

    expect($name)->toMatch('/^\d+_bosch-drill-new\.jpg$/');
});

it('accepts PDFs for data sheets', function () {
    $pdf = UploadedFile::fake()->createWithContent('TDS sheet.pdf', "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF");

    expect(uploadName($pdf))->toEndWith('_tds-sheet.pdf');
});

// A real UploadedFile backed by a temp file, so the content-type check sees the
// actual bytes (Laravel's fake files report a type based on the name only).
function realUpload($filename, $content)
{
    $path = tempnam(sys_get_temp_dir(), 'upl');
    file_put_contents($path, $content);

    return new UploadedFile($path, $filename, null, null, true);
}

it('rejects executable and other non-image files', function ($filename, $content) {
    expect(fn () => uploadName(realUpload($filename, $content)))
        ->toThrow(HttpException::class, 'Only JPG, PNG, GIF, WEBP or PDF files can be uploaded.');
})->with([
    'php' => ['shell.php', '<?php system($_GET["c"]);'],
    'phtml' => ['shell.phtml', '<?php echo 1;'],
    'svg (can carry script)' => ['logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'],
    'html' => ['page.html', '<html><script>alert(1)</script></html>'],
    'php disguised as jpg' => ['photo.jpg', '<?php system($_GET["c"]);'],
    'png bytes named .jpg' => ['photo.jpg', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==')],
]);

it('accepts a real image whose bytes match its extension', function () {
    $png = realUpload('pixel.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='));

    expect(uploadName($png))->toEndWith('_pixel.png');
});
