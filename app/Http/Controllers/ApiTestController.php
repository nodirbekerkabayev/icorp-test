<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ApiTestController extends Controller
{
    private $apiUrl = 'https://test.icorp.uz/interview.php';

    // Web sahifa - jarayonni boshlash
    public function index()
    {
        return view('api-test');
    }

    // 1-qadam: POST so'rov yuborish
    public function startProcess(Request $request)
    {
        try {
            $webhookUrl = $request->input('webhook_url');
            $message = $request->input('message', 'Test message from Laravel');

            // Cache tozalash (yangi test uchun)
            Cache::forget('code_part_1');
            Cache::forget('code_part_2');
            Cache::forget('final_message');

            // POST so'rov yuborish
            $response = Http::post($this->apiUrl, [
                'msg' => $message,
                'url' => $webhookUrl
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $codePart1 = $data['code'] ?? $data['part1'] ?? $response->body();

                // Birinchi kod qismini saqlash
                Cache::put('code_part_1', $codePart1, now()->addMinutes(10));

                Log::info('Code Part 1 received: ' . $codePart1);

                return response()->json([
                    'success' => true,
                    'message' => 'POST so\'rov muvaffaqiyatli yuborildi',
                    'code_part_1' => $codePart1,
                    'webhook_url' => $webhookUrl,
                    'response' => $data
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'API dan javob kelmadi',
                'status' => $response->status()
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error in startProcess: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    // 2-qadam: Webhook - ikkinchi kod qismini qabul qilish
    public function webhook(Request $request)
    {
        try {
            Log::info('Webhook received:', $request->all());

            $codePart2 = $request->input('code') ??
                $request->input('part2') ??
                $request->getContent();

            if ($codePart2) {
                // Ikkinchi kod qismini saqlash
                Cache::put('code_part_2', $codePart2, now()->addMinutes(10));

                Log::info('Code Part 2 received: ' . $codePart2);

                return response()->json([
                    'success' => true,
                    'message' => 'Webhook qabul qilindi'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Kod qismi topilmadi'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error in webhook: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    // 3-qadam: Kodlarni tekshirish
    public function checkStatus()
    {
        $codePart1 = Cache::get('code_part_1');
        $codePart2 = Cache::get('code_part_2');

        return response()->json([
            'code_part_1' => $codePart1,
            'code_part_2' => $codePart2,
            'has_part_1' => !empty($codePart1),
            'has_part_2' => !empty($codePart2),
            'ready_for_final' => !empty($codePart1) && !empty($codePart2)
        ]);
    }

    // 4-qadam: GET so'rov yuborish va yakuniy xabarni olish
    public function getFinalMessage()
    {
        try {
            $codePart1 = Cache::get('code_part_1');
            $codePart2 = Cache::get('code_part_2');

            if (empty($codePart1) || empty($codePart2)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ikkala kod qismi ham tayyor emas'
                ], 400);
            }

            // Kodlarni birlashtirish
            $fullCode = $codePart1 . $codePart2;

            Log::info('Full code: ' . $fullCode);

            // GET so'rov yuborish
            $response = Http::get($this->apiUrl, [
                'code' => $fullCode
            ]);

            if ($response->successful()) {
                $finalMessage = $response->body();

                Cache::put('final_message', $finalMessage, now()->addMinutes(10));

                Log::info('Final message received: ' . $finalMessage);

                return response()->json([
                    'success' => true,
                    'code_part_1' => $codePart1,
                    'code_part_2' => $codePart2,
                    'full_code' => $fullCode,
                    'final_message' => $finalMessage
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Final message olinmadi',
                'status' => $response->status()
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error in getFinalMessage: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }
}
