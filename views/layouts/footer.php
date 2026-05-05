<?php
/**
 * ╔══════════════════════════════════════════════╗
 * ║  SystemCOFF 360 — Layout: Footer            ║
 * ║  Ruta: views/layouts/fouter.php             ║
 * ╚══════════════════════════════════════════════╝
 *
 * Uso: include al final de cada vista del dashboard
 *   <?php require_once '../layouts/fouter.php'; ?>
 */
?>

<!-- ↑ Fin del contenido de cada vista ↑ -->
</main>
</div><!-- end flex wrapper -->

<!-- FOOTER DEL SISTEMA -->
<footer class="border-t py-3 px-6 flex items-center justify-between flex-wrap gap-2"
        style="background:#030f08;border-color:rgba(34,197,94,.08)">
    <p class="text-[10px]" style="color:#2a4a35">
        &copy; <?= date('Y') ?>
        <strong style="color:#22c55e">SystemCOFF 360</strong>
        — Sistema de Gestión de Fincas Cafeteras.
        Desarrollado por <strong style="color:#22c55e">JD Solutions</strong>
        · SENA Ficha 3230026
    </p>
    <p class="text-[10px]" style="color:#2a4a35">
        Versión 2.0
        · Finca Los Guácimos, Tesalia – Huila
        · <?= date('d/m/Y H:i') ?>
    </p>
</footer>

</body>
</html>
