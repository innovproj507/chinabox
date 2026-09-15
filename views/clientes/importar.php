<?php $page_title = 'Importar Clientes'; ?>

<div class="px-6 py-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-file-import mr-2 text-inventory"></i>Importar desde Excel
                </h3>
                <a href="<?= $baseUrl ?>clientes" class="text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-arrow-left mr-1"></i>Volver al listado
                </a>
            </div>
            
            <div class="p-6">
                <div class="mb-8 p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r">
                    <h4 class="text-blue-800 font-bold mb-2">Instrucciones:</h4>
                    <ul class="text-blue-700 text-sm list-disc list-inside space-y-1">
                        <li>Suba un archivo Excel (.xlsx o .xls).</li>
                        <li>El sistema utilizará el <strong>Código</strong> del cliente para identificar si ya existe.</li>
                        <li>Si el código existe, la información del cliente será <strong>actualizada</strong>.</li>
                        <li>Si el código no existe, se creará un <strong>nuevo cliente</strong>.</li>
                        <li>Las columnas sugeridas son: Código, Nombre, Apellido, Email, Teléfono, RUC, Grupo, Dirección, Ciudad, País.</li>
                    </ul>
                </div>

                <form action="<?= $baseUrl ?>clientes-importar" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-10 text-center hover:border-inventory transition-colors cursor-pointer" onclick="document.getElementById('excel_file').click()">
                        <input type="file" name="excel_file" id="excel_file" class="hidden" accept=".xlsx, .xls" required>
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 mb-1 font-medium">Haga clic para seleccionar o arrastre su archivo Excel</p>
                        <p class="text-gray-400 text-xs text-uppercase">XLSX, XLS (Máx. 5MB)</p>
                        <div id="file_name" class="mt-4 text-inventory font-bold hidden"></div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-inventory hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold shadow-sm transition duration-200">
                            Procesar Importación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('excel_file').addEventListener('change', function(e) {
    const fileName = e.target.files[0] ? e.target.files[0].name : '';
    const nameDiv = document.getElementById('file_name');
    if (fileName) {
        nameDiv.textContent = 'Archivo seleccionado: ' + fileName;
        nameDiv.classList.remove('hidden');
    } else {
        nameDiv.classList.add('hidden');
    }
});
</script>
