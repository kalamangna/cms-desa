<?php

namespace Tests\Feature;

use App\Http\Middleware\MinifyHtml;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MinifyHtmlTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(MinifyHtml::class)->group(function () {
            Route::get('/_test/minify-html', function () {
                return response("<html>\n  <body>\n    <h1>Hello World</h1>\n  </body>\n</html>", 200, ['Content-Type' => 'text/html']);
            });

            Route::get('/_test/minify-json', function () {
                return response()->json(['html' => "<html>\n  <body>\n    <h1>Hello World</h1>\n  </body>\n</html>"]);
            });

            Route::get('/admin/test-minify', function () {
                return response("<html>\n  <body>\n    <h1>Hello World</h1>\n  </body>\n</html>", 200, ['Content-Type' => 'text/html']);
            });

            Route::get('/livewire/test-minify', function () {
                return response("<html>\n  <body>\n    <h1>Hello World</h1>\n  </body>\n</html>", 200, ['Content-Type' => 'text/html']);
            });
        });
    }

    public function test_it_minifies_html_responses_on_public_routes(): void
    {
        $response = $this->get('/_test/minify-html');

        $response->assertStatus(200);
        // The whitespace between tags should be minified to single space
        $response->assertSee('<html> <body> <h1>Hello World</h1> </body> </html>', false);
    }

    public function test_it_does_not_minify_non_html_responses(): void
    {
        $response = $this->get('/_test/minify-json');

        $response->assertStatus(200);
        // JSON content should preserve the original newlines/whitespace in the string
        $this->assertStringContainsString('\n  <body>\n', $response->getContent());
    }

    public function test_it_does_not_minify_admin_routes(): void
    {
        $response = $this->get('/admin/test-minify');

        $response->assertStatus(200);
        // Admin route HTML should not be minified (newlines preserved)
        $this->assertStringContainsString("\n  <body>\n", $response->getContent());
    }

    public function test_it_does_not_minify_livewire_routes(): void
    {
        $response = $this->get('/livewire/test-minify');

        $response->assertStatus(200);
        // Livewire route HTML should not be minified (newlines preserved)
        $this->assertStringContainsString("\n  <body>\n", $response->getContent());
    }

    public function test_it_preserves_alpine_multiline_attributes(): void
    {
        Route::middleware(MinifyHtml::class)->get('/_test/alpine-attrs', function () {
            $html = <<<'HTML'
<html>
<body>
<div x-show="openMenu === 'info'"
    x-transition:enter="transition ease-out duration-150"
    x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    class="dropdown" x-cloak>content</div>
</body>
</html>
HTML;
            return response($html, 200, ['Content-Type' => 'text/html']);
        });

        $response = $this->get('/_test/alpine-attrs');
        $content = $response->getContent();

        // Semua atribut Alpine.js harus tetap ada setelah minifikasi
        $this->assertStringContainsString('x-show="openMenu === \'info\'"', $content);
        $this->assertStringContainsString('x-transition:enter="transition ease-out duration-150"', $content);
        $this->assertStringContainsString('x-transition:enter-start="opacity-0 translate-y-2"', $content);
        $this->assertStringContainsString('x-cloak', $content);
        // Atribut tidak boleh menempel tanpa spasi
        $this->assertStringNotContainsString('"x-transition', $content);
    }
}
