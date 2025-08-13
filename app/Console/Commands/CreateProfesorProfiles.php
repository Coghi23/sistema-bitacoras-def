<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Profesor;

class CreateProfesorProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profesor:sync-profiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza perfiles de profesor para usuarios con rol profesor';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Buscando usuarios con rol profesor sin perfil...');
        
        // Obtener usuarios con rol profesor que no tienen perfil de profesor
        $usuariosSinPerfil = User::role('profesor')
            ->whereDoesntHave('profesor')
            ->get();
        
        if ($usuariosSinPerfil->isEmpty()) {
            $this->info('✅ Todos los usuarios con rol profesor ya tienen perfil.');
            return 0;
        }
        
        $this->info("📋 Encontrados {$usuariosSinPerfil->count()} usuarios sin perfil:");
        
        foreach ($usuariosSinPerfil as $usuario) {
            $this->line("   - {$usuario->name} ({$usuario->email})");
        }
        
        if ($this->confirm('¿Deseas crear perfiles de profesor para estos usuarios?')) {
            $creados = 0;
            
            foreach ($usuariosSinPerfil as $usuario) {
                try {
                    Profesor::create([
                        'usuario_id' => $usuario->id
                    ]);
                    
                    $this->info("✅ Perfil creado para: {$usuario->name}");
                    $creados++;
                    
                } catch (\Exception $e) {
                    $this->error("❌ Error creando perfil para {$usuario->name}: {$e->getMessage()}");
                }
            }
            
            $this->info("🎉 Proceso completado. {$creados} perfiles creados.");
        } else {
            $this->info('❌ Proceso cancelado.');
        }
        
        return 0;
    }
}
