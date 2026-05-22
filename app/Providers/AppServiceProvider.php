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
use Illuminate\Support\ServiceProvider;

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
    }

    protected function registerPolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
