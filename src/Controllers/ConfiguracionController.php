<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Core\Flash;
use App\Core\Paginator;
use App\Models\ConfiguracionModel;
use App\Models\UserModel;

class ConfiguracionController {
    private ConfiguracionModel $configModel;
    private UserModel $userModel;

    public function __construct() {
        if (!Auth::check() || !Auth::user()['is_superuser']) {
            Flash::error('No tienes permiso para acceder a esta sección');
            Response::redirect('dashboard');
        }
        $this->configModel = new ConfiguracionModel();
        $this->userModel = new UserModel();
    }

    public function sistema() {
        $config = $this->configModel->getConfig();

        if (Request::isPost()) {
            try {
                $this->configModel->updateConfig([
                    'nombre_empresa' => Request::post('nombre_empresa'),
                    'nombre_comercial' => Request::post('nombre_comercial'),
                    'ruc_empresa' => Request::post('ruc_empresa'),
                    'direccion_empresa' => Request::post('direccion_empresa'),
                    'telefono_empresa' => Request::post('telefono_empresa'),
                    'email_empresa' => Request::post('email_empresa'),
                    'sitio_web' => Request::post('sitio_web'),
                    'itbms_porcentaje' => Request::post('itbms_porcentaje'),
                    'moneda_default' => Request::post('moneda_default'),
                    'prefijo_factura' => Request::post('prefijo_factura'),
                    'terminos_pago_default' => Request::post('terminos_pago_default'),
                    'nota_factura' => Request::post('nota_factura'),
                    'mensaje_agradecimiento' => Request::post('mensaje_agradecimiento'),
                    'pie_pagina_factura' => Request::post('pie_pagina_factura'),
                    'whatsapp_path' => Request::post('whatsapp_path')
                ]);
                Flash::success('Configuración actualizada exitosamente');
                Response::redirect('configuracion');
            } catch (\Exception $e) {
                Flash::error('Error al actualizar: ' . $e->getMessage());
            }
        }

        View::render('configuracion/sistema', ['config' => $config]);
    }

    public function usuarios() {
        $usuarios = $this->userModel->all('date_joined', 'DESC');
        View::render('configuracion/usuarios', ['usuarios' => $usuarios]);
    }

    public function usuarioCrear() {
        if (Request::isPost()) {
            $username = Request::post('username');
            
            if ($this->userModel->where('username', $username)) {
                Flash::error("El usuario {$username} ya existe");
                Response::redirect('configuracion/usuarios/crear');
            }

            try {
                $this->userModel->createDjangoUser([
                    'username' => $username,
                    'email' => Request::post('email'),
                    'password' => Request::post('password'),
                    'first_name' => Request::post('first_name'),
                    'last_name' => Request::post('last_name'),
                    'is_superuser' => Request::post('is_superuser') ? 1 : 0,
                    'is_active' => Request::post('is_active') ? 1 : 0
                ]);
                
                Flash::success("Usuario {$username} creado exitosamente");
                Response::redirect('configuracion/usuarios');
            } catch (\Exception $e) {
                Flash::error('Error: ' . $e->getMessage());
            }
        }

        View::render('configuracion/usuario_form', ['modo' => 'crear']);
    }

    public function usuarioEditar($id) {
        $usuario = $this->userModel->find($id);
        if (!$usuario) Response::redirect('configuracion/usuarios');

        if (Request::isPost()) {
            try {
                $this->userModel->updateDjangoUser($id, [
                    'email' => Request::post('email'),
                    'first_name' => Request::post('first_name'),
                    'last_name' => Request::post('last_name'),
                    'is_superuser' => Request::post('is_superuser') ? 1 : 0,
                    'is_active' => Request::post('is_active') ? 1 : 0,
                    'password' => Request::post('password') // Opcional
                ]);
                
                Flash::success("Usuario actualizado exitosamente");
                Response::redirect('configuracion/usuarios');
            } catch (\Exception $e) {
                Flash::error('Error: ' . $e->getMessage());
            }
        }

        View::render('configuracion/usuario_form', ['modo' => 'editar', 'usuario' => $usuario]);
    }

    public function usuarioEliminar($id) {
        if (!Request::isPost()) Response::redirect('configuracion/usuarios');

        $usuario = $this->userModel->find($id);
        if (!$usuario) Response::redirect('configuracion/usuarios');

        if ($usuario['is_superuser'] == 1 && count($this->userModel->where('is_superuser', 1)) <= 1) {
            Flash::error('No se puede eliminar el último superusuario');
            Response::redirect('configuracion/usuarios');
        }

        if ($id == Auth::id()) {
            Flash::error('No puedes eliminarte a ti mismo');
            Response::redirect('configuracion/usuarios');
        }

        $this->userModel->delete($id);
        Flash::success("Usuario eliminado exitosamente");
        Response::redirect('configuracion/usuarios');
    }

    public function usuarioToggle($id) {
        if (!Request::isPost()) Response::redirect('configuracion/usuarios');

        $usuario = $this->userModel->find($id);
        if ($id == Auth::id()) {
            Flash::error('No puedes desactivarte a ti mismo');
        } else {
            $nuevoEstado = $usuario['is_active'] ? 0 : 1;
            $this->userModel->update($id, ['is_active' => $nuevoEstado]);
            $estadoStr = $nuevoEstado ? 'activado' : 'desactivado';
            Flash::success("Usuario {$estadoStr} exitosamente");
        }

        Response::redirect('configuracion/usuarios');
    }

    public function roles() {
        // Dummy, as Django groups are more complex to map without the many-to-many tables.
        View::render('configuracion/roles', ['roles' => []]);
    }
}
