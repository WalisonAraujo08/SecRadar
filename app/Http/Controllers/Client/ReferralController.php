<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->is_partner) {
            return view('client.partner-request', compact('user'));
        }

        if ($user->partner_status === 'pending') {
            return view('client.partner-pending', compact('user'));
        }

        if ($user->partner_status === 'rejected') {
            return view('client.partner-request', compact('user'))->with('rejected', true);
        }

        if (!$user->referral_code) {
            $code = strtoupper(Str::random(8));
            DB::table('users')->where('id', $user->id)->update(['referral_code' => $code]);
            $user->referral_code = $code;
        }

        $referrals = DB::table('referrals')
            ->join('users', 'users.id', '=', 'referrals.referred_id')
            ->where('referrals.referrer_id', $user->id)
            ->select('users.name', 'users.email', 'referrals.status', 'referrals.total_earned', 'referrals.created_at')
            ->get();

        $activeCount    = $referrals->where('status', 'active')->count();
        $totalEarned    = DB::table('referrals')->where('referrer_id', $user->id)->sum('total_earned');
        $pendingPayment = DB::table('referral_payments')->where('referrer_id', $user->id)->where('status', 'pending')->sum('amount');

        $currentLevel = CommissionService::getLevel($activeCount);
        $nextLevel    = CommissionService::getNextLevel($activeCount);
        $progressPct  = $nextLevel
            ? min(100, round(($activeCount - $currentLevel['min']) / ($nextLevel['min'] - $currentLevel['min']) * 100))
            : 100;

        return view('client.referral', compact(
            'user', 'referrals', 'totalEarned', 'pendingPayment',
            'currentLevel', 'nextLevel', 'activeCount', 'progressPct'
        ));
    }

    public function requestPartnership()
    {
        $user = Auth::user();
        DB::table('users')->where('id', $user->id)->update([
            'is_partner'           => false,
            'partner_status'       => 'pending',
            'partner_requested_at' => now(),
        ]);
        return back()->with('success', 'Solicitação enviada! Aguarde a aprovação em até 24h.');
    }

    public function updatePix(Request $request)
    {
        $request->validate(['pix_key' => 'required|string|max:255']);
        DB::table('users')->where('id', Auth::id())->update(['pix_key' => $request->pix_key]);
        return back()->with('success', 'Chave PIX salva!');
    }
}
