<!-- COFFY ASSISTANT WIDGET PRO (CONTEXT-AWARE) — SYSTEMCOFF 360 -->
<?php
// Pasamos el rol y el estado de sesión a JS de forma segura
$userRole = $_SESSION['usuario']['rol'] ?? 'publico';
$isLoggedIn = isset($_SESSION['usuario']);
?>

<div id="coffy-widget" style="position: fixed; bottom: 24px; right: 24px; z-index: 999999; font-family: 'Plus Jakarta Sans', sans-serif;">
    
    <button id="coffy-btn" class="relative w-16 h-16 bg-gradient-to-br from-green-600 to-emerald-800 text-white rounded-full shadow-[0_8px_30px_rgb(5,150,105,0.4)] flex items-center justify-center transition-all duration-500 hover:scale-110 active:scale-95 group border-2 border-white/20">
        <div class="absolute inset-0 rounded-full bg-green-400 animate-ping opacity-20 group-hover:opacity-40"></div>
        <i class="fas fa-robot text-2xl group-hover:rotate-12 transition-transform duration-300"></i>
    </button>

    <div id="coffy-window" class="absolute bottom-20 right-0 w-[380px] md:w-[420px] h-[600px] bg-white/95 backdrop-blur-xl rounded-[32px] shadow-[0_20px_60px_rgba(0,0,0,0.15)] border border-white/40 flex flex-col overflow-hidden transition-all duration-500 cubic-bezier(0.16, 1, 0.3, 1) scale-0 origin-bottom-right opacity-0 pointer-events-none">
        
        <div class="bg-gradient-to-r from-[#064e3b] via-[#065f46] to-[#059669] p-6 text-white relative">
            <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
            <div class="relative flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center border border-white/20 backdrop-blur-md shadow-inner">
                            <i class="fas fa-robot text-xl"></i>
                        </div>
                        <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-emerald-400 border-2 border-[#064e3b] rounded-full"></span>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-base tracking-tight">Coffy AI <span class="text-[9px] bg-emerald-400/20 text-emerald-300 px-2 py-0.5 rounded-full ml-1">v2.1</span></h4>
                        <p class="text-[10px] text-emerald-200/80 uppercase tracking-[0.2em] font-black" id="coffy-status">Contexto: <?= ucfirst($userRole) ?></p>
                    </div>
                </div>
                <button id="close-coffy" class="w-10 h-10 flex items-center justify-center hover:bg-white/10 rounded-2xl transition-all duration-300">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <div id="coffy-messages" class="flex-1 p-6 overflow-y-auto space-y-6 bg-[radial-gradient(#f1f5f9_1px,transparent_1px)] [background-size:20px_20px]"></div>

        <div id="coffy-typing" class="px-6 py-2 hidden">
            <div class="flex items-center gap-2 mb-2">
                <div class="flex gap-1">
                    <div class="w-1.5 h-1.5 bg-green-400 rounded-full animate-bounce"></div>
                    <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-bounce [animation-delay:0.2s]"></div>
                    <div class="w-1.5 h-1.5 bg-green-600 rounded-full animate-bounce [animation-delay:0.4s]"></div>
                </div>
                <span class="text-[9px] text-gray-400 font-bold uppercase tracking-widest" id="typing-text">Analizando...</span>
            </div>
        </div>

        <div class="p-6 bg-white border-t border-gray-100">
            <div id="coffy-tags" class="flex gap-2 overflow-x-auto no-scrollbar mb-4 pb-1"></div>
            <div class="relative group">
                <input type="text" id="coffy-input" placeholder="Pregunta algo sobre este módulo..." 
                    class="w-full bg-gray-50 border-2 border-gray-100 focus:border-green-500/50 focus:bg-white rounded-2xl pl-5 pr-14 py-4 text-sm font-medium text-gray-800 transition-all duration-300 outline-none shadow-sm">
                <button id="coffy-send" class="absolute right-2 top-1/2 -translate-y-1/2 w-11 h-11 bg-green-600 hover:bg-green-700 text-white rounded-xl shadow-lg shadow-green-200 flex items-center justify-center transition-all duration-300 active:scale-90">
                    <i class="fas fa-paper-plane text-sm"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    #coffy-window.open { transform: scale(1) !important; opacity: 1 !important; pointer-events: auto !important; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .msg-bot-style { background: white; border: 1px solid rgba(22, 163, 74, 0.1); padding: 1rem; border-radius: 1.25rem; border-top-left-radius: 0; font-size: 0.85rem; color: #334155; max-width: 85%; box-shadow: 0 4px 15px rgba(0,0,0,0.03); line-height: 1.6; }
    .msg-user-style { background: linear-gradient(135deg, #064e3b, #15803d); color: white; padding: 0.85rem 1.25rem; border-radius: 1.25rem; border-bottom-right-radius: 0; font-size: 0.85rem; font-weight: 500; max-width: 80%; margin-left: auto; box-shadow: 0 8px 20px rgba(6, 78, 59, 0.15); }
    .tag-style { white-space: nowrap; background: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; padding: 0.5rem 1rem; border-radius: 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; transition: all 0.2s ease; }
    .tag-style:hover { background: #15803d; color: white; transform: translateY(-2px); }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-in { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>

<script>
    (function() {
        const btn = document.getElementById('coffy-btn');
        const chatWindow = document.getElementById('coffy-window');
        const close = document.getElementById('close-coffy');
        const messages = document.getElementById('coffy-messages');
        const input = document.getElementById('coffy-input');
        const sendBtn = document.getElementById('coffy-send');
        const tagsContainer = document.getElementById('coffy-tags');
        const typing = document.getElementById('coffy-typing');
        const typingText = document.getElementById('typing-text');

        // Datos de sesión inyectados
        const ROLE = "<?= $userRole ?>";
        const LOGGED = <?= $isLoggedIn ? 'true' : 'false' ?>;
        const PATH = window.location.pathname.toLowerCase();

        // Base de conocimientos por contexto
        const knowledge = {
            publico: {
                welcome: "¡Hola! Bienvenido a SystemCOFF 360. Soy Coffy. ¿Deseas saber qué es el sistema o cómo adquirirlo? (Funciones de administración ocultas por seguridad)",
                tags: ["¿Qué es SystemCOFF?", "¿Cómo contactar?", "Ver Módulos"],
                responses: {
                    "que es": "Es un ecosistema digital para fincas cafeteras que optimiza la producción y administración.",
                    "modulos": "Tenemos Lotes, Cosechas, Inventarios y más. Pero los datos reales solo son visibles para usuarios registrados.",
                    "reportes": "⚠️ Por seguridad, la generación de reportes financieros solo está disponible para Administradores autenticados."
                }
            },
            administrador: {
                welcome: "Saludos, Administrador. Estoy listo para procesar reportes, gestionar el personal y auditar el inventario. ¿Qué informe necesitas hoy?",
                tags: ["Generar Reporte Financiero", "Estado de Inventario", "Auditar Tareas"],
                responses: {
                    "reporte": "✅ Generando balance del trimestre... Se han analizado las ventas de café y los gastos en insumos. Puedes ver el detalle en el módulo de Reportes.",
                    "inventario": "Detecto 3 insumos próximos a stock mínimo. ¿Quieres que redacte una orden de compra?",
                    "usuarios": "Puedes gestionar los roles y activar/desactivar cuentas desde el panel de Usuarios."
                }
            },
            trabajador: {
                welcome: "Hola. Tienes tareas pendientes para hoy. No olvides subir tus evidencias fotográficas.",
                tags: ["Ver mis Tareas", "Subir Evidencia", "Contactar Admin"],
                responses: {
                    "tareas": "Tu tarea actual es: 'Recolección Lote 1'. Recuerda marcarla como finalizada al terminar.",
                    "evidencia": "Puedes subir hasta 3 fotos por tarea desde este panel.",
                    "reportes": "⚠️ No tienes permisos para generar reportes financieros. Contacta a tu administrador."
                }
            }
        };

        // Identificar módulo específico para ayuda personalizada (info, pregunta y tags)
        function getModuleHelp() {
            if (PATH.includes('inventario')) return {
                info: "Estás en **Inventario**. Aquí puedes registrar entradas/salidas de insumos y controlar stock.",
                question: "¿Necesitas registrar una entrada, registrar una salida o ver el estado de stock?",
                tags: ["Registrar Entrada","Registrar Salida","Ver Stocks"]
            };
            if (PATH.includes('lotes')) return {
                info: "Estás en **Lotes**. Aquí mapeas y gestionas parcelas por variedad y estado.",
                question: "¿Quieres crear un nuevo lote, ver detalles de un lote o actualizar su estado?",
                tags: ["Crear Lote","Ver Lotes","Actualizar Estado"]
            };
            if (PATH.includes('usuarios')) return {
                info: "Estás en **Usuarios**. Aquí gestionas cuentas y permisos.",
                question: "¿Deseas crear un usuario, modificar roles o revisar accesos?",
                tags: ["Crear Usuario","Modificar Roles","Revisar Accesos"]
            };
            return null;
        }

        function initChat() {
            const ctx = knowledge[ROLE] || knowledge.publico;
            messages.innerHTML = '';
            addBotMsg(ctx.welcome);

            const moduleHelp = getModuleHelp();
            if (moduleHelp) {
                // Mostrar información del módulo y luego una pregunta proactiva con etiquetas relacionadas
                setTimeout(() => addBotMsg(moduleHelp.info), 600);
                setTimeout(() => {
                    addBotMsg(moduleHelp.question);
                    updateTags(moduleHelp.tags || ctx.tags);
                }, 1200);
            } else {
                updateTags(ctx.tags);
            }
        }

        function updateTags(tags) {
            tags = tags || [];
            tagsContainer.innerHTML = tags.map(tag => `<button onclick="coffyAsk('${tag}')" class="tag-style">${tag}</button>`).join('');
        }

        btn.addEventListener('click', () => {
            if (!chatWindow.classList.contains('open')) initChat();
            chatWindow.classList.toggle('open');
        });

        close.addEventListener('click', () => chatWindow.classList.remove('open'));
        sendBtn.addEventListener('click', handleUserInput);
        input.addEventListener('keypress', (e) => { if(e.key === 'Enter') handleUserInput(); });

        function handleUserInput() {
            const text = input.value.trim();
            if (!text) return;
            input.value = '';
            addUserMsg(text);
            processContextBot(text);
        }

        async function processContextBot(userInput) {
            const q = userInput.toLowerCase();
            const ctx = knowledge[ROLE] || knowledge.publico;
            typing.classList.remove('hidden');
            scrollChat();

            setTimeout(async () => {
                typing.classList.add('hidden');
                
                // 1. Verificar Seguridad de Reportes
                if (q.includes('reporte') && ROLE !== 'administrador') {
                    addBotMsg("🛑 **Acceso Restringido**: Lo siento, la generación de reportes y balances financieros es una función exclusiva del rol Administrador.");
                    return;
                }

                // 2. Buscar en base local por contexto
                let response = null;
                for (let key in ctx.responses) {
                    if (q.includes(key)) {
                        response = ctx.responses[key];
                        break;
                    }
                }

                // 3. Fallback Online o Redacción
                if (response) {
                    addBotMsg(response);
                } else if (q.includes('buscar:')) {
                    const term = q.replace('buscar:', '').trim();
                    typingText.innerText = `Buscando: ${term}...`;
                    typing.classList.remove('hidden');
                    try {
                        const res = await fetch(`https://es.wikipedia.org/api/rest_v1/page/summary/${encodeURIComponent(term)}`);
                        const data = await res.json();
                        typing.classList.add('hidden');
                        addBotMsg(data.extract || "No encontré info online, pero he guardado tu consulta.");
                    } catch (e) { addBotMsg("Error de conexión."); }
                } else {
                    // Enviar la consulta al endpoint local para búsqueda en la documentación y vistas
                    try {
                        typingText.innerText = `Buscando en la documentación...`;
                        typing.classList.remove('hidden');
                        const res = await fetch('/assistant_search.php?q=' + encodeURIComponent(q));
                        const data = await res.json();
                        typing.classList.add('hidden');
                        if (data && data.ok && Array.isArray(data.matches) && data.matches.length) {
                            addBotMsg("He encontrado la siguiente información relevante:");
                            data.matches.forEach(m => {
                                const short = m.snippet.length > 300 ? m.snippet.substring(0,300) + '...' : m.snippet;
                                addBotMsg(`<strong>${m.file}</strong>: ${short}`);
                            });
                            addBotMsg("¿Te ayudo a redactar una acción basada en esto o quieres más detalles?");
                        } else {
                            addBotMsg("No encontré coincidencias locales. ¿Quieres que busque en la web o que redacte una respuesta basada en la descripción?");
                        }
                    } catch (e) {
                        typing.classList.add('hidden');
                        addBotMsg("Error al consultar la documentación local. Intenta de nuevo más tarde.");
                    }
                }
            }, 1200);
        }

        function addUserMsg(t) { const div = document.createElement('div'); div.className = 'flex justify-end mb-4 animate-in'; div.innerHTML = `<div class="msg-user-style">${t}</div>`; messages.appendChild(div); scrollChat(); }
        function addBotMsg(t) { const div = document.createElement('div'); div.className = 'flex items-start gap-3 mb-4 animate-in'; div.innerHTML = `<div class="w-9 h-9 bg-gradient-to-br from-green-700 to-emerald-900 rounded-xl flex items-center justify-center text-white text-xs shadow-lg flex-shrink-0"><i class="fas fa-robot"></i></div><div class="msg-bot-style">${formatText(t)}</div>`; messages.appendChild(div); scrollChat(); }
        function scrollChat() { messages.scrollTop = messages.scrollHeight; }
        function formatText(t) { return t.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>').replace(/•/g, '●').replace(/🛑/g, '❌'); }
        window.coffyAsk = function(t) { addUserMsg(t); processContextBot(t); };
    })();
</script>
