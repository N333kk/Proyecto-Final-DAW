<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Throwable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ShoppingFlowTest extends DuskTestCase
{
    /**
     * Test del flujo completo de compra.
     *
     * Este test realiza las siguientes acciones:
     * 1. Visita la página principal
     * 2. Registra una nueva cuenta de usuario
     * 3. Navega a la página de artículos
     * 4. Añade artículos al carrito usando la interfaz de usuario
     * 5. Completa el proceso de compra
     *
     * @return void
     */
    public function testShoppingFlow()
    {
        // Variable para almacenar logs del navegador
        $consoleLogs = [];
        $networkLogs = [];
        $jsErrors = [];

        try {
            $this->browse(function (Browser $browser) use (&$consoleLogs, &$networkLogs, &$jsErrors) {
                // Email aleatorio para el registro
                $email = 'test_' . time() . '@example.com';
                $password = 'password123';
                $testId = Str::random(8); // Identificador único para este test

                // Habilitar la recolección de logs
                $browser->enableConsoleLog()
                    ->enableNetworkLog();

                $browser->visit('/')
                    // 1. Verificar que estamos en la página principal
                    ->assertPathIs('/')
                    ->screenshot($testId . '_01_home_page')

                    // 2. Ir a la página de registro
                    ->clickLink('Register')
                    ->waitForLocation('/register')
                    ->assertSee('Nombre')
                    ->screenshot($testId . '_02_register_page')

                    // 3. Completar el formulario de registro con todos los campos requeridos
                    ->type('#name', 'Usuario Test')
                    ->type('#email', $email)
                    ->type('#password', $password)
                    ->type('#password_confirmation', $password)
                    ->type('#telefono', '123456789')
                    ->type('#direccion_envio', 'Calle Test 123')
                    ->type('#direccion_facturacion', 'Calle Test 123')
                    ->screenshot($testId . '_03_filled_register_form')
                    ->click('button[type="submit"]')

                    // 4. Verificar registro exitoso - redirige a la página principal
                    ->waitForLocation('/')
                    ->assertSee('Usuario Test')
                    ->screenshot($testId . '_04_logged_in')

                    // 5. Navegar a la página de artículos
                    ->clickLink('Articulos')
                    ->waitForLocation('/articulos')
                    ->screenshot($testId . '_05_articulos_page')

                    // 6. Verificar que se muestran los artículos
                    ->waitFor('.articulo-card', 10)
                    ->screenshot($testId . '_06_articulos_loaded')

                    // 7. Hacer hover sobre el primer artículo para mostrar los botones
                    ->mouseover('.articulo-card:first-child')
                    ->pause(1000) // Pausa para asegurar que aparezcan los botones
                    ->screenshot($testId . '_07_hover_on_articulo')

                    // 8. Hacer clic en el botón de carrito usando la clase específica
                    ->click('.articulo-card:first-child .añadir-carrito')
                    ->screenshot($testId . '_08_clicked_add_to_cart')

                    // 9. Se abre un modal para seleccionar talla - esperamos a que aparezca
                    ->waitFor('.fixed.inset-0.flex.items-center.justify-center.z-50', 10)
                    ->screenshot($testId . '_09_talla_modal_opened')

                    // 10. Seleccionar la primera talla disponible (excluyendo las agotadas)
                    ->click('.fixed.inset-0 button:not([disabled]):first-child')
                    ->screenshot($testId . '_10_talla_selected')

                    // 11. Hacer clic en el botón "Añadir al carrito" del modal
                    ->click('.fixed.inset-0 .confirmar-talla')
                    ->screenshot($testId . '_11_confirm_add_to_cart')

                    // Registrar logs después de hacer clic en el botón
                    ->pause(1000);

                // Recolectar logs del navegador
                $consoleLogs = $browser->driver->manage()->getLog('browser');
                $networkLogs = $browser->driver->manage()->getLog('performance');
                $jsErrors = $this->extractJavaScriptErrors($consoleLogs);

                $browser->pause(10000) // Espera prolongada para dar tiempo a la petición AJAX
                    ->screenshot($testId . '_12_after_modal_handling')

                    // 14. Usar el icono del carrito para navegar al carrito
                    ->click('svg[viewBox="0 0 24 24"][stroke-width="1.5"][d*="M15.75 10.5V6"]')
                    ->waitForLocation('/cart')
                    ->screenshot($testId . '_13_cart_page')

                    // 15. Verificar que estamos en la página del carrito
                    ->assertPathIs('/cart')
                    ->assertSee('Tu Carrito')
                    ->assertDontSee('Tu carrito está vacío')
                    ->screenshot($testId . '_14_cart_with_items')

                    // 16. Hacer clic en el botón "Proceder al pago"
                    ->waitFor('button[type="submit"]', 10)
                    ->screenshot($testId . '_15_before_checkout')
                    ->press('PROCEDER AL PAGO')
                    ->screenshot($testId . '_16_after_checkout')

                    // 17. Verificar que ya no estamos en la página del carrito
                    ->waitUntilMissing('button[type="submit"]', 20)
                    ->assertPathIsNot('/cart')
                    ->screenshot($testId . '_17_checkout_complete');
            });
        } catch (Throwable $e) {
            // En caso de error, guardar los logs
            $this->saveLogs($consoleLogs, $networkLogs, $jsErrors);
            throw $e; // Re-lanzar la excepción para que el test siga fallando normalmente
        }
    }

    /**
     * Extrae errores de JavaScript de los logs de la consola
     */
    protected function extractJavaScriptErrors(array $logs): array
    {
        $errors = [];

        foreach ($logs as $log) {
            if (isset($log['level']) && $log['level'] === 'SEVERE') {
                $errors[] = $log;
            }
        }

        return $errors;
    }

    /**
     * Guarda los logs del navegador en archivos
     */
    protected function saveLogs(array $consoleLogs, array $networkLogs, array $jsErrors): void
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $logDir = storage_path('logs/browser-logs');

        if (!File::exists($logDir)) {
            File::makeDirectory($logDir, 0755, true);
        }

        // Guardar logs de consola
        $consoleLogPath = $logDir . "/console_{$timestamp}.log";
        File::put($consoleLogPath, json_encode($consoleLogs, JSON_PRETTY_PRINT));

        // Guardar logs de red
        $networkLogPath = $logDir . "/network_{$timestamp}.log";
        File::put($networkLogPath, json_encode($networkLogs, JSON_PRETTY_PRINT));

        // Guardar errores JavaScript
        $jsErrorPath = $logDir . "/js_errors_{$timestamp}.log";
        File::put($jsErrorPath, json_encode($jsErrors, JSON_PRETTY_PRINT));

        echo "\n[BROWSER LOGS] Guardados en: {$consoleLogPath}" . PHP_EOL;
        echo "[NETWORK LOGS] Guardados en: {$networkLogPath}" . PHP_EOL;
        echo "[JS ERRORS] Guardados en: {$jsErrorPath}" . PHP_EOL;
    }
}
