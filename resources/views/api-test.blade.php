<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>API Test - iCorp Interview</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-8">
<div class="max-w-4xl mx-auto px-4">
    <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">iCorp API Test</h1>
        <p class="text-gray-600 mb-6">Test API bilan ishlash vazifasi</p>

        <!-- Bosqichlar ko'rsatkichi -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div id="step1-circle" class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">1</div>
                        <div class="flex-1 h-1 bg-gray-300 mx-2"></div>
                    </div>
                    <p class="text-sm mt-2 text-gray-600">POST so'rov</p>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div id="step2-circle" class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white font-bold">2</div>
                        <div class="flex-1 h-1 bg-gray-300 mx-2"></div>
                    </div>
                    <p class="text-sm mt-2 text-gray-600">Webhook</p>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div id="step3-circle" class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white font-bold">3</div>
                        <div class="flex-1 h-1 bg-gray-300 mx-2"></div>
                    </div>
                    <p class="text-sm mt-2 text-gray-600">Birlashtirish</p>
                </div>
                <div class="flex-1">
                    <div class="flex items-center">
                        <div id="step4-circle" class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white font-bold">4</div>
                    </div>
                    <p class="text-sm mt-2 text-gray-600">GET so'rov</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Webhook URL (ngrok URL)</label>
                <input
                    type="text"
                    id="webhookUrl"
                    placeholder="https://abcd1234.ngrok.io/api-test/webhook"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                <p class="text-xs text-gray-500 mt-1">Masalan: https://YOUR-NGROK-ID.ngrok.io/api-test/webhook</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Xabar (msg)</label>
                <input
                    type="text"
                    id="message"
                    value="Test message from Laravel"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <button
                onclick="startProcess()"
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200"
            >
                Jarayonni Boshlash (POST so'rov)
            </button>

            <div class="flex gap-4">
                <button
                    onclick="checkStatus()"
                    class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200"
                >
                    Status Tekshirish
                </button>
                <button
                    onclick="getFinalMessage()"
                    id="finalBtn"
                    class="flex-1 bg-purple-500 hover:bg-purple-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 disabled:opacity-50"
                    disabled
                >
                    Final Xabarni Olish (GET so'rov)
                </button>
            </div>
        </div>

        <!-- Natijalar -->
        <div id="results" class="mt-8 space-y-4 hidden">
            <h2 class="text-xl font-bold text-gray-800">Natijalar:</h2>

            <div id="codePart1Box" class="bg-blue-50 border border-blue-200 rounded-lg p-4 hidden">
                <h3 class="font-semibold text-blue-800 mb-2">✓ Kod Qismi 1 (POST dan):</h3>
                <code id="codePart1" class="text-sm text-gray-700 break-all"></code>
            </div>

            <div id="codePart2Box" class="bg-green-50 border border-green-200 rounded-lg p-4 hidden">
                <h3 class="font-semibold text-green-800 mb-2">✓ Kod Qismi 2 (Webhook dan):</h3>
                <code id="codePart2" class="text-sm text-gray-700 break-all"></code>
            </div>

            <div id="fullCodeBox" class="bg-purple-50 border border-purple-200 rounded-lg p-4 hidden">
                <h3 class="font-semibold text-purple-800 mb-2">✓ To'liq Kod (Birlashtirilgan):</h3>
                <code id="fullCode" class="text-sm text-gray-700 break-all"></code>
            </div>

            <div id="finalMessageBox" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 hidden">
                <h3 class="font-semibold text-yellow-800 mb-2">🎉 Final Xabar (GET dan):</h3>
                <div id="finalMessage" class="text-lg font-bold text-gray-800"></div>
            </div>
        </div>

        <!-- Log -->
        <div id="logBox" class="mt-8 bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h3 class="font-semibold text-gray-800 mb-2">Log:</h3>
            <div id="log" class="text-sm text-gray-600 space-y-1 font-mono"></div>
        </div>
    </div>
</div>

<script>
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    function addLog(message, type = 'info') {
        const log = document.getElementById('log');
        const time = new Date().toLocaleTimeString('uz-UZ');
        const colors = {
            info: 'text-blue-600',
            success: 'text-green-600',
            error: 'text-red-600',
            warning: 'text-yellow-600'
        };
        log.innerHTML += `<div class="${colors[type]}">[${time}] ${message}</div>`;
        log.scrollTop = log.scrollHeight;
    }

    function updateStep(step) {
        for (let i = 1; i <= 4; i++) {
            const circle = document.getElementById(`step${i}-circle`);
            if (i <= step) {
                circle.classList.remove('bg-gray-300');
                circle.classList.add('bg-green-500');
            }
        }
    }

    async function startProcess() {
        const webhookUrl = document.getElementById('webhookUrl').value;
        const message = document.getElementById('message').value;

        if (!webhookUrl) {
            alert('Webhook URL kiriting!');
            return;
        }

        addLog('POST so\'rov yuborilmoqda...', 'info');
        document.getElementById('results').classList.remove('hidden');

        try {
            const response = await fetch('/api-test/start', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ webhook_url: webhookUrl, message: message })
            });

            const data = await response.json();

            if (data.success) {
                addLog('✓ POST so\'rov muvaffaqiyatli!', 'success');
                addLog(`Kod qismi 1 olindi: ${data.code_part_1}`, 'success');

                document.getElementById('codePart1').textContent = data.code_part_1;
                document.getElementById('codePart1Box').classList.remove('hidden');
                updateStep(1);

                addLog('Webhook kutilmoqda... (ikkinchi kod qismi uchun)', 'info');

                // Auto-check status har 2 sekundda
                const interval = setInterval(async () => {
                    const statusData = await checkStatus(true);
                    if (statusData && statusData.has_part_2) {
                        clearInterval(interval);
                        addLog('✓ Webhook qabul qilindi!', 'success');
                    }
                }, 2000);

            } else {
                addLog('✗ Xatolik: ' + data.message, 'error');
            }
        } catch (error) {
            addLog('✗ Xatolik: ' + error.message, 'error');
        }
    }

    async function checkStatus(silent = false) {
        if (!silent) addLog('Status tekshirilmoqda...', 'info');

        try {
            const response = await fetch('/api-test/status');
            const data = await response.json();

            if (data.has_part_1 && !document.getElementById('codePart1Box').classList.contains('hidden')) {
                // Already shown
            } else if (data.has_part_1) {
                document.getElementById('codePart1').textContent = data.code_part_1;
                document.getElementById('codePart1Box').classList.remove('hidden');
                updateStep(1);
            }

            if (data.has_part_2) {
                document.getElementById('codePart2').textContent = data.code_part_2;
                document.getElementById('codePart2Box').classList.remove('hidden');
                updateStep(2);
                updateStep(3);
                document.getElementById('finalBtn').disabled = false;

                if (!silent) {
                    addLog('✓ Ikkala kod qismi ham tayyor!', 'success');
                    addLog('Endi "Final Xabarni Olish" tugmasini bosing', 'warning');
                }
            } else if (!silent) {
                addLog('Webhook hali kelgani yo\'q. Kutilmoqda...', 'warning');
            }

            document.getElementById('results').classList.remove('hidden');
            return data;

        } catch (error) {
            if (!silent) addLog('✗ Xatolik: ' + error.message, 'error');
        }
    }

    async function getFinalMessage() {
        addLog('GET so\'rov yuborilmoqda...', 'info');

        try {
            const response = await fetch('/api-test/final');
            const data = await response.json();

            if (data.success) {
                addLog('✓ GET so\'rov muvaffaqiyatli!', 'success');
                addLog(`Final xabar olindi: ${data.final_message}`, 'success');

                document.getElementById('fullCode').textContent = data.full_code;
                document.getElementById('fullCodeBox').classList.remove('hidden');

                document.getElementById('finalMessage').textContent = data.final_message;
                document.getElementById('finalMessageBox').classList.remove('hidden');

                updateStep(4);
                addLog('🎉 Jarayon tugadi!', 'success');
            } else {
                addLog('✗ Xatolik: ' + data.message, 'error');
            }
        } catch (error) {
            addLog('✗ Xatolik: ' + error.message, 'error');
        }
    }

    // Sahifa yuklanganda log qo'shish
    window.onload = function() {
        addLog('Sahifa tayyor. Webhook URL kiriting va jarayonni boshlang.', 'info');
    };
</script>
</body>
</html>
