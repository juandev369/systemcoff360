<?php
session_start();

// Si no hay sesión, se asume acceso público (aunque la página asistente suele estar protegida, 
// mantenemos la lógica de seguridad por si se accede desde fuera)
$isLoggedIn = isset($_SESSION['usuario']);
$userRole = $_SESSION['usuario']['rol'] ?? 'publico';
$userName = $_SESSION['usuario']['nombre'] ?? 'Invitado';

if (!$isLoggedIn) {
    header('Location: ../usuarios/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Centro de Inteligencia — SystemCOFF 360</title>
    <link rel="shortcut icon" type="image/png" href="../../img/ico.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        .sidebar { background: linear-gradient(180deg, #052e16 0%, #064e3b 60%, #022c22 100%); }
        .glass-chat { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.4); }
        .chat-bubble-bot { background: #ffffff; border: 1px solid rgba(22, 163, 74, 0.1); border-radius: 24px 24px 24px 4px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); color: #334155; }
        .chat-bubble-user { background: linear-gradient(135deg, #064e3b, #15803d); border-radius: 24px 24px 4px 24px; box-shadow: 0 10px 25px rgba(6, 78, 59, 0.15); color: white; }
        .typing-dot { width: 8px; height: 8px; background: #16a34a; border-radius: 50%; display: inline-block; animation: bounce 1.4s infinite ease-in-out both; }
        .typing-dot:nth-child(1) { animation-delay: -0.32s; }
        .typing-dot:nth-child(2) { animation-delay: -0.16s; }
        @keyframes bounce { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .animate-in { animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="min-h-screen bg-[#fcfdfc]">

<div class="flex h-screen overflow-hidden">

    <!-- SIDEBAR -->
    <aside class="sidebar w-80 hidden lg:flex flex-col text-white p-8 flex-shrink-0">
        <div class="flex items-center gap-4 mb-12">
            <div class="w-12 h-12 bg-white/10 backdrop-blur-xl rounded-2xl flex items-center justify-center border border-white/20 shadow-2xl">
                <i class="fas fa-seedling text-green-400 text-xl"></i>
            </div>
            <div>
                <h1 class="font-black text-xl tracking-tight leading-none">SystemCOFF <span class="text-green-400">360</span></h1>
                <p class="text-green-400/60 text-[10px] font-bold uppercase tracking-[0.2em] mt-1">Gestión Inteligente</p>
            </div>
        </div>

        <nav class="space-y-3 flex-1">
            <?php if ($userRole === 'administrador'): ?>
                <a href="admin.php" class="flex items-center gap-4 px-5 py-4 rounded-2xl hover:bg-white/5 transition-all group">
                    <i class="fas fa-chart-pie w-5 text-green-400 group-hover:scale-110 transition"></i> Dashboard
                </a>
            <?php else: ?>
                <a href="trabajador.php" class="flex items-center gap-4 px-5 py-4 rounded-2xl hover:bg-white/5 transition-all group">
                    <i class="fas fa-grid-2 w-5 text-green-400 group-hover:scale-110 transition"></i> Mi Inicio
                </a>
            <?php endif; ?>
            <a href="asistente.php" class="flex items-center gap-4 px-5 py-4 rounded-2xl bg-white/10 text-white shadow-xl border border-white/10 group">
                <i class="fas fa-robot w-5 text-green-400"></i> <span class="text-sm font-black">Asistente AI PRO</span>
            </a>
        </nav>
        <a href="../../usuarios/logout.php" class="flex items-center justify-center gap-3 px-6 py-4 rounded-2xl bg-red-500/10 text-red-400 font-bold border border-red-500/20"><i class="fas fa-power-off"></i> Salir</a>
    </aside>

    <!-- MAIN AREA -->
    <main class="flex-1 flex flex-col p-6 md:p-10 bg-[#f1f5f9]/30">
        <header class="flex items-center justify-between mb-8">
            <div class="animate-in">
                <h1 class="text-4xl font-black text-slate-900 tracking-tight">Centro de Inteligencia</h1>
                <p class="text-slate-500 text-sm font-medium">Coffy AI: Modo <?= ucfirst($userRole) ?> Activado.</p>
            </div>
            <div class="flex items-center gap-4 bg-white px-6 py-3 rounded-2xl shadow-sm border border-slate-100">
                <div class="text-right"><p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Usuario</p><p class="text-sm font-bold text-slate-700"><?= $userName ?></p></div>
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-green-600"><i class="fas fa-user"></i></div>
            </div>
        </header>

        <div class="flex-1 glass-chat rounded-[48px] shadow-2xl flex flex-col overflow-hidden border border-white/50">
            <div id="chatMessages" class="flex-1 p-8 md:p-12 overflow-y-auto space-y-8 scroll-smooth"></div>
            
            <div class="p-10 bg-white/50 border-t border-white/20">
                <div id="quickTags" class="flex justify-center gap-3 mb-6 overflow-x-auto no-scrollbar"></div>
                <div class="max-w-4xl mx-auto relative group">
                    <input type="text" id="chatInput" placeholder="Escribe tu consulta aquí..." class="w-full bg-white border-2 border-slate-100 focus:border-green-500 rounded-[30px] pl-8 pr-20 py-6 text-base font-semibold text-slate-800 transition-all outline-none shadow-xl">
                    <button id="sendBtn" class="absolute right-3 top-1/2 -translate-y-1/2 w-14 h-14 bg-green-600 text-white rounded-[20px] shadow-lg flex items-center justify-center transition-all active:scale-90"><i class="fas fa-paper-plane text-xl"></i></button>
                </div>
            </div>
        </div>
    </main>

</div>

<script>
    const chatMessages = document.getElementById('chatMessages');
    const chatInput = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendBtn');
    const quickTags = document.getElementById('quickTags');

    const ROLE = "<?= $userRole ?>";
    const NAME = "<?= $userName ?>";

    const knowledge = {
        administrador: {
            welcome: `¡Bienvenido, Administrador ${NAME}! Tengo acceso total a los balances y auditorías de la finca. ¿Qué reporte deseas generar o qué módulo auditamos hoy?`,
            tags: ["Reporte Financiero", "Auditoría Inventario", "Estado Usuarios"],
            responses: {
                "reporte": "✅ **Balance Consolidado**: He analizado las últimas ventas de café. La rentabilidad del trimestre ha subido un 15%. ¿Deseas el desglose por lote?",
                "auditoria": "He detectado que el stock de fertilizantes en el inventario no coincide con las últimas salidas. Sugiero revisar el registro de Insumos.",
                "usuarios": "Actualmente hay 5 trabajadores activos en campo y 2 cuentas administrativas."
            }
        },
        trabajador: {
            welcome: `Hola ${NAME}. Estoy enfocado en tus tareas y reportes de campo. ¿En qué labor te ayudo hoy?`,
            tags: ["Ver mis Tareas", "Reportar Avance", "Ayuda Técnica"],
            responses: {
                "tareas": "Tienes asignada la labor de 'Desyerbe' en el Lote 3. Fecha límite: mañana.",
                "avance": "Puedes subir la foto de tu avance desde el módulo de tareas. Yo la notificaré al administrador inmediatamente.",
                "reporte": "🛑 **Acceso Restringido**: Los reportes financieros y de gestión avanzada solo pueden ser generados por el Administrador."
            }
        },
        publico: {
            welcome: "Hola. Para acceder a las funciones inteligentes de SystemCOFF 360 debes iniciar sesión. ¿Te ayudo con información general?",
            tags: ["Sobre el Sistema", "Contacto", "Login"],
            responses: {
                "reporte": "🛑 **Seguridad**: Los datos de reportes están protegidos. Inicia sesión como administrador para ver esta información."
            }
        }
    };

    function addMessage(text, isUser = false) {
        const div = document.createElement('div');
        div.className = `flex ${isUser ? 'justify-end' : 'items-start'} gap-5 animate-in mb-8`;
        div.innerHTML = isUser 
            ? `<div class="chat-bubble-user p-6 text-sm max-w-[75%] font-bold">${text}</div>`
            : `<div class="w-12 h-12 bg-green-700 rounded-2xl flex items-center justify-center text-white flex-shrink-0 shadow-2xl"><i class="fas fa-robot"></i></div><div class="chat-bubble-bot p-6 text-sm max-w-[75%] leading-relaxed">${formatText(text)}</div>`;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function formatText(t) { return t.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>').replace(/•/g, '●').replace(/🛑/g, '❌'); }

    async function processMessage(query) {
        const q = query.toLowerCase();
        const ctx = knowledge[ROLE] || knowledge.publico;
        
        // Simular pensamiento
        const typing = document.createElement('div');
        typing.className = 'flex items-start gap-5 mb-8';
        typing.innerHTML = `<div class="w-12 h-12 bg-green-700 rounded-2xl flex items-center justify-center text-white"><i class="fas fa-robot"></i></div><div class="chat-bubble-bot p-5 flex gap-1"><span class="typing-dot"></span><span class="typing-dot"></span><span class="typing-dot"></span></div>`;
        chatMessages.appendChild(typing);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        setTimeout(async () => {
            typing.remove();
            
            // Verificación de seguridad para reportes
            if (q.includes('reporte') && ROLE !== 'administrador') {
                addMessage("🛑 **Seguridad**: No tienes permisos para generar reportes financieros. Estos datos son confidenciales y solo accesibles para el Administrador.");
                return;
            }

            // Lógica Online
            if (q.includes('buscar:')) {
                const term = q.replace('buscar:', '').trim();
                try {
                    const res = await fetch(`https://es.wikipedia.org/api/rest_v1/page/summary/${encodeURIComponent(term)}`);
                    const data = await res.json();
                    addMessage(data.extract || "No encontré información externa relevante.");
                } catch (e) { addMessage("Error al conectar con la red."); }
                return;
            }

            // Lógica Redacción
            if (q.includes('redactar:')) {
                addMessage(`**Borrador Generado:**\n\nEste es un documento profesional redactado automáticamente sobre **${q.replace('redactar:', '').trim()}**. Para formalizarlo, por favor revisa los datos en el módulo correspondiente.`);
                return;
            }

            // Respuestas por contexto
            let found = false;
            for (let key in ctx.responses) {
                if (q.includes(key)) {
                    addMessage(ctx.responses[key]);
                    found = true;
                    break;
                }
            }

            if (!found) addMessage("Entendido. Estoy procesando tu solicitud dentro de tu rol de " + ROLE + ". ¿Deseas que busque algo más específico?");
        }, 1200);
    }

    function init() {
        const ctx = knowledge[ROLE] || knowledge.publico;
        addMessage(ctx.welcome);
        quickTags.innerHTML = ctx.tags.map(tag => `<button onclick="quickAction('${tag}')" class="bg-white border border-slate-200 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-500 hover:bg-green-600 hover:text-white transition shadow-sm">${tag}</button>`).join('');
    }

    sendBtn.addEventListener('click', () => { const v = chatInput.value; if(v){ addMessage(v, true); chatInput.value=''; processMessage(v); } });
    chatInput.addEventListener('keypress', (e) => { if(e.key === 'Enter') sendBtn.click(); });
    function quickAction(t) { chatInput.value = t; sendBtn.click(); }
    init();
</script>

</body>
</html>
