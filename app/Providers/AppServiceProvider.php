<?php

namespace App\Providers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Usuario;
use App\Models\Venta;
use App\Policies\CategoriaPolicy;
use App\Policies\ProductoPolicy;
use App\Policies\UsuarioPolicy;
use App\Policies\VentaPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Usuario::class => UsuarioPolicy::class,
        Producto::class => ProductoPolicy::class,
        Categoria::class => CategoriaPolicy::class,
        Venta::class => VentaPolicy::class,
    ];

    public function register(): void
    {
        // Forzar HTTPS en producción
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('es-administrador', fn (Usuario $user) => $user->esAdministrador());
        Gate::define('es-gerente', fn (Usuario $user) => $user->esGerente());
        Gate::define('es-cliente', fn (Usuario $user) => $user->esCliente());

        // EXTENSIÓN PARA ENVIAR CORREOS MEDIANTE LA API HTTP DE BREVO (PUERTO 443)
        Mail::extend('brevo', function (array $config) {
            return new class extends \Symfony\Component\Mailer\Transport\AbstractTransport {
                protected function doSend(\Symfony\Component\Mailer\SentMessage $message): void
                {
                    // Obtenemos el mensaje original compatible con Symfony Mailer
                    $symfonyMessage = $message->getOriginalMessage();
                    
                    // Extraemos el cuerpo del mensaje de forma segura
                    $htmlBody = $symfonyMessage->getHtmlBody();
                    $textBody = $symfonyMessage->getTextBody();

                    $client = new Client();
                    $client->post('https://api.brevo.com/v3/smtp/email', [
                        'headers' => [
                            'api-key' => env('BREVO_API_KEY'),
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json',
                        ],
                        'json' => [
                            'sender' => [
                                'name' => env('MAIL_FROM_NAME', 'MecaMensajeria'),
                                'email' => env('MAIL_FROM_ADDRESS')
                            ],
                            'to' => collect($symfonyMessage->getTo())->map(function ($address) {
                                return ['email' => $address->getAddress()];
                            })->values()->toArray(),
                            'subject' => $symfonyMessage->getSubject(),
                            'htmlContent' => $htmlBody ?? $textBody ?? 'Tu código de verificación 2FA',
                        ],
                    ]);
                }

                public function __toString(): string
                {
                    return 'brevo';
                }
            };
        });
    }

    protected function registerPolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}