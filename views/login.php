<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ChinaBox</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { inventory: "#0EA5E9" }
                }
            }
        };
    </script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

<div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">
    <div class="text-center mb-8">
        <img src="<?= rtrim($_ENV['APP_URL'] ?? '', '/') ?>/public/images/logo_chinabox.png" alt="Logo ChinaBox" class="mx-auto mb-4">
        <p class="text-gray-500 mt-2">Ingresa tus credenciales para continuar</p>
    </div>

    <?php if (!empty($flash_messages)): ?>
        <?php foreach ($flash_messages as $msg): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?= $msg['message'] ?></span>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <form method="POST" action="<?= $_ENV['APP_URL'] ?? '/' ?>">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Usuario</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-inventory" id="username" name="username" type="text" placeholder="Usuario" required>
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Contraseña</label>
            <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-inventory" id="password" name="password" type="password" placeholder="******************" required>
        </div>
        
        <div class="flex items-center justify-between">
            <button class="bg-inventory hover:bg-blue-600 w-full text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-200" type="submit">
                Iniciar Sesión
            </button>
        </div>
    </form>
</div>

</body>
</html>
