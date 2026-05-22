<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectRequirementTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_responds_successfully(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_invalid_login_credentials_show_validation_error(): void
    {
        $this->post(route('login.post'), [
            'correo' => 'correo@incorrecto.com',
            'clave' => 'incorrecto',
        ])->assertSessionHasErrors('correo');

        $this->assertGuest();
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $usuario = Usuario::factory()->create();

        $this->actingAs($usuario)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_authorized_user_can_store_product_in_database(): void
    {
        Storage::fake('public');

        $vendedor = Usuario::factory()->vendedor()->create();
        $categoria = Categoria::factory()->create();

        $this->actingAs($vendedor)
            ->post(route('productos.store'), [
                'nombre' => 'Teclado mecanico',
                'descripcion' => 'Teclado para pruebas automatizadas',
                'precio' => 500,
                'existencia' => 12,
                'vendedor_id' => $vendedor->id,
                'categorias' => [$categoria->id],
                'fotos' => [
                    UploadedFile::fake()->create('teclado.jpg', 10, 'image/jpeg'),
                ],
            ])
            ->assertRedirect(route('productos.index'));

        $this->assertDatabaseHas('productos', [
            'nombre' => 'Teclado mecanico',
            'precio' => 500,
            'usuario_id' => $vendedor->id,
        ]);

        $producto = Producto::where('nombre', 'Teclado mecanico')->firstOrFail();

        $this->assertDatabaseHas('categoria_producto', [
            'producto_id' => $producto->id,
            'categoria_id' => $categoria->id,
        ]);
    }
}
