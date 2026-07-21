<?php

namespace App\Http\Controllers\Overrides;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PurchaseVerificationController extends Controller
{
    public function verifyPurchase(): RedirectResponse
    {
        return redirect()->route('login');
    }

    public function purchaseVerified(Request $request): RedirectResponse
    {
        return redirect()->route('login');
    }

    public function hideReviewModal(string $type): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'code' => '000',
            'messages' => 'Thank you',
        ]);
    }

    public function down(string $hash): JsonResponse
    {
        return response()->json(['status' => 'ignored']);
    }

    public function up(string $hash): JsonResponse
    {
        return response()->json(['status' => 'ignored']);
    }
}
