<?php

namespace Tests\Feature;

use App\Models\User;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Facades\Filament;
use Tests\TestCase;

class LanguageSwitchTest extends TestCase
{
    public function test_language_switch_is_configured_properly(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $ls = LanguageSwitch::make();

        $this->assertEquals(['id', 'en'], $ls->getLocales());
        $this->assertEquals('Indonesia', $ls->getLabels()['id'] ?? null);
        $this->assertEquals('English', $ls->getLabels()['en'] ?? null);
        $this->assertTrue($ls->isVisible());
        $this->assertTrue($ls->isVisibleInsidePanels());
    }

    public function test_login_page_renders_with_language_switch(): void
    {
        $response = $this->get('/admin/login');

        $response->assertSuccessful();
        $response->assertSee('fi-ls');
    }

    public function test_locale_switching_works(): void
    {
        LanguageSwitch::switchLocale('en');
        $this->assertEquals('en', session('locale'));

        LanguageSwitch::switchLocale('id');
        $this->assertEquals('id', session('locale'));
    }

    public function test_authenticated_admin_page_renders_language_switch(): void
    {
        $user = User::factory()->create([
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertSuccessful();
        $response->assertSee('fi-ls');
    }
}
