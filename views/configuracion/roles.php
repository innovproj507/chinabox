<?php $page_title = 'Roles y Permisos'; $page_subtitle = 'Control de niveles de acceso del sistema'; ?>

<div class="px-6 py-4 max-w-6xl mx-auto">
    
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="<?= $baseUrl ?>configuracion" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-cogs mr-2"></i> Parámetros
            </a>
            <a href="<?= $baseUrl ?>configuracion/usuarios" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-users-cog mr-2"></i> Usuarios
            </a>
            <a href="<?= $baseUrl ?>configuracion/roles" class="border-inventory text-inventory whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-shield-alt mr-2"></i> Accesos / Roles
            </a>
        </nav>
    </div>

    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg shadow-sm">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mt-1"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-bold text-yellow-800">Sección en Desarrollo</h3>
                <div class="mt-1 text-sm text-yellow-700 border-yellow-200">
                    <p>La integración con los modelos `auth_group` y `auth_permission` de Django nativo requiere una gestión más compleja en el módulo PHP por lo cual está reservado para una versión futura.</p>
                    <p class="font-bold mt-2">Actualmente la seguridad base recae en la bandera `is_superuser` en la tabla de usuarios.</p>
                </div>
            </div>
        </div>
    </div>

</div>
