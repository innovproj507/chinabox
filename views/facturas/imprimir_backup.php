<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <title>Recibo <?= $factura['codigo_factura'] ?></title>
</head>

<body class="bg-gray-100 p-8 flex justify-center print:p-0 print:bg-white">

    <!-- Dimensions: 19.5cm x 12.5cm -->
    <div id="invoice-content"
        class="w-[19.5cm] h-[12.5cm] bg-white border-2 border-black flex text-[9px] font-sans overflow-hidden leading-tight relative mx-auto my-4 shadow-lg">

        <!-- Botones flotantes (No se imprimen) -->
        <div class="absolute top-2 right-2 flex gap-2 no-print z-50 overflow-visible" style="margin-right: -150px;">
            <?php if (!isset($autoPrintBrowser) || $autoPrintBrowser): ?>
            <button onclick="window.print()" class="bg-blue-600 text-white px-3 py-1 rounded text-xs shadow hover:bg-blue-700">Imprimir</button>
            <?php else: ?>
            <span class="bg-green-100 text-green-800 px-3 py-1 rounded text-xs shadow border border-green-300 font-bold">✓ Impreso</span>
            <?php endif; ?>
            <button onclick="window.close()" class="bg-gray-500 text-white px-3 py-1 rounded text-xs shadow hover:bg-gray-600">Cerrar</button>
        </div>

        <style>
            @media print {
                .no-print { display: none !important; }
                body { background: white; padding: 0.5cm !important; }
                #invoice-content { 
                    border: 2px solid black !important; 
                    margin: 0 !important;
                    box-shadow: none !important;
                }
            }
        </style>

        <!-- LEFT COLUMN -> 14.6cm -->
        <div class="w-[14.6cm] flex flex-col border-r-2 border-black relative">

            <!-- HEADER: Logo & Address -->
            <div class="flex h-16 border-b-2 border-black">
                <!-- Logo -->
                <div class="w-1/2 p-1 flex items-center justify-center">
                    <?php if ($logo_base64): ?>
                    <img src="data:image/png;base64,<?= $logo_base64 ?>" alt="ChinaBox" class="max-h-12 max-w-full">
                    <?php else: ?>
                    <h1 class="text-2xl font-bold text-red-600">China B<span class="text-blue-700">o</span>x</h1>
                    <?php endif; ?>
                </div>
                <!-- Address -->
                <div class="w-1/2 p-1 text-[8px] leading-none flex flex-col justify-center">
                    <p>Dirección: Plaza 100, El Ingenio, Ave. La Paz</p>
                    <p>Cel / WhatsApp: 507 6768-3586</p>
                    <p>WeChat: ChinaBox507</p>
                    <p>Ruc. 155665594-2-2018-587207</p>
                </div>
            </div>

            <!-- INVOICE NUMBER ROW -->
            <div class="border-b-2 border-black p-1 bg-gray-100 font-bold flex items-center h-6">
                <span class="mr-2 text-[10px]">RECIBO</span>
                <span class="text-red-600 text-[10px]"><?= $factura['codigo_factura'] ?></span>
            </div>

            <!-- CLIENT INFO GRID -->
            <div class="border-b-2 border-black text-[9px]">
                <!-- Row 1 -->
                <div class="flex border-b border-black h-5">
                    <div class="w-16 font-bold p-1 border-r border-black flex items-center">Código:</div>
                    <div class="w-1/2 p-1 font-bold text-center border-r border-black flex items-center justify-center">
                        <?= $cliente['codigo'] ?></div>
                    <div class="w-16 font-bold p-1 border-r border-black flex items-center">Teléfono:</div>
                    <div class="flex-1 p-1 flex items-center"><?= $cliente['telefono'] ?></div>
                </div>
                <!-- Row 2 -->
                <div class="flex border-b border-black h-5">
                    <div class="w-16 font-bold p-1 border-r border-black flex items-center">Nombre:</div>
                    <div class="w-1/2 p-1 border-r border-black flex items-center uppercase truncate"><?= $cliente['nombre'] ?> <?= $cliente['apellido'] ?? '' ?></div>
                    <div class="w-16 font-bold p-1 border-r border-black flex items-center">RUC:</div>
                    <div class="flex-1 p-1 flex items-center text-right justify-end pr-2"><?= $cliente['ruc'] ?>
                    </div>
                </div>
                <!-- Row 3 -->
                <div class="flex h-5">
                    <div class="w-16 font-bold p-1 border-r border-black flex items-center italic">Envio</div>
                    <div class="w-1/2 p-1 border-r border-black flex items-center">USA - PTY (Aerea)</div>
                    <div class="w-16 font-bold p-1 border-r border-black flex items-center">Fecha:</div>
                    <div class="flex-1 p-1 flex items-center"><?= date('d/m/Y', strtotime($factura['fecha_emision'])) ?></div>
                </div>
            </div>

            <!-- ITEMS TABLE HEADER -->
            <div class="flex border-b border-black font-bold h-6 items-center text-center text-[8px]">
                <div class="w-[9.3cm] border-r border-black h-full flex items-center justify-center">Guia</div>
                <div class="w-[1.7cm] border-r border-black h-full flex items-center justify-center leading-tight">Peso
                    /<br>Volumen</div>
                <div class="w-[1.8cm] border-r border-black h-full flex items-center justify-center">Precio x LB</div>
                <div class="w-[1.8cm] h-full flex items-center justify-center">Monto</div>
            </div>

            <!-- ITEMS TABLE BODY (FLEX GROW) -->
            <div class="flex-grow flex flex-col relative overflow-hidden">
                <!-- Vertical Lines for columns -->
                <div class="absolute inset-0 flex pointer-events-none">
                    <div class="w-[9.3cm] border-r border-black"></div>
                    <div class="w-[1.7cm] border-r border-black"></div>
                    <div class="w-[1.8cm] border-r border-black"></div>
                    <div class="flex-1"></div>
                </div>

                <!-- Reference Code at Bottom Left of Guia Column -->
                <div class="absolute bottom-0 left-0 p-1 font-bold z-20 bg-white">
                    <?= $factura['observaciones'] ?: '' ?>
                </div>

                <!-- Items Rows -->
                <?php foreach ($detalles as $detalle): ?>
                <div class="flex text-center h-5 z-10 relative items-center text-[8px]">
                    <div class="w-[9.3cm] px-1 text-left font-mono truncate"><?= $detalle['guia'] ?></div>
                    <div class="w-[1.7cm] font-bold"><?= $detalle['cantidad'] ?></div>
                    <div class="w-[1.8cm]"><?= number_format($detalle['precio_unitario'], 2) ?></div>
                    <div class="w-[1.8cm] font-bold"><?= number_format($detalle['subtotal'], 2) ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- FOOTER ROW -->
            <div class="h-8 border-t-2 border-black flex font-bold items-center text-[8px]">
                <div class="flex-1 pl-1 flex flex-col justify-center leading-none">
                    <div class="italic font-normal"><?= date('d/m/Y h:i') ?></div>
                </div>
                <div class="w-[1.8cm] text-right pr-2">TOTAL</div>
                <div class="w-[1.8cm] text-center text-sm border-l border-black h-full flex items-center justify-center"><?= number_format($factura['total'], 2) ?></div>
            </div>

            <!-- FISCAL NOTE -->
            <div class="absolute -bottom-5 left-2 text-[7px] text-gray-500 w-[19cm]">
                * Este comprobante no constituye factura fiscal. Para su elaboración, favor enviar RUC y correo electrónico.
            </div>

        </div>

        <!-- RIGHT COLUMN -> 4.9cm -->
        <div class="w-[4.9cm] flex flex-col">

            <!-- INVOICE NUMBER HEADER -->
            <div class="h-8 border-b-2 border-black bg-gray-200 flex flex-col justify-center px-1 font-bold">
                <div class="text-[8px]">Recibo #</div>
                <div class="text-red-600 text-[10px]"><?= $factura['codigo_factura'] ?></div>
            </div>

            <!-- PAYMENT METHODS -->
            <div class="p-2 space-y-2 font-bold text-[9px]">
                <div class="flex items-center">
                    <div class="w-3 h-3 border border-black mr-1 flex items-center justify-center">
                        <?php if (strtoupper($factura['metodo_pago']) == 'EFECTIVO'): ?>
                        <div class="w-2 h-2 bg-black"></div>
                        <?php endif; ?>
                    </div>
                    Efectivo
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 border border-black mr-1 flex items-center justify-center">
                        <?php if (strtoupper($factura['metodo_pago']) == 'TARJETA'): ?>
                        <div class="w-2 h-2 bg-black"></div>
                        <?php endif; ?>
                    </div>
                    Tarjeta
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 border border-black mr-1 flex items-center justify-center">
                        <?php if (strtoupper($factura['metodo_pago']) == 'TRANSFERENCIA'): ?>
                        <div class="w-2 h-2 bg-black"></div>
                        <?php endif; ?>
                    </div>
                    Transferencia
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 border border-black mr-1 flex items-center justify-center">
                        <?php if (strtoupper($factura['metodo_pago']) == 'YAPPY'): ?>
                        <div class="w-2 h-2 bg-black"></div>
                        <?php endif; ?>
                    </div>
                    Yappy
                </div>
            </div>

            <!-- SPACER & SIGNATURE -->
            <div class="mt-auto px-1 pb-4 text-center">
                <div class="text-2xl font-bold text-gray-200 tracking-widest uppercase">FIRMA</div>
            </div>

            <!-- EMPTY BOTTOM BOX (Matches left footer height) -->
            <div class="h-8 border-t-2 border-black"></div>

        </div>

    </div>

    <script>
        window.onload = function () {
            // Check for download query param or specific PDF path
            const urlParams = new URLSearchParams(window.location.search);
            // Check if URL has download=true or we want to trigger PDF
            const shouldDownload = urlParams.get('download') === 'true';

            if (shouldDownload) {
                // Generate PDF
                const element = document.getElementById('invoice-content');
                const opt = {
                    margin: 0,
                    filename: 'factura_<?= $factura['codigo_factura'] ?>.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, useCORS: true },
                    jsPDF: { unit: 'in', format: [7, 4], orientation: 'landscape' }
                };

                html2pdf().set(opt).from(element).toPdf().get('pdf').then(function (pdf) {
                    window.open(pdf.output('bloburl'), '_blank');
                });
                // html2pdf().set(opt).from(element).save();
            } else {
                // Default behavior: Print dialog si no se imprimió en el server
                <?php if (!isset($autoPrintBrowser) || $autoPrintBrowser): ?>
                window.print();
                <?php endif; ?>
            }
        };
    </script>
</body>

</html>
