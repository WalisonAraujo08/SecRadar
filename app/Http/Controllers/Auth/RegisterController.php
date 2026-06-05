<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\MonitoredEmail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showForm()
    {
        // Captura código de referral da URL
        $refCode = request('ref');
        return view('auth.register', compact('refCode'));
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name'     => strip_tags(trim($request->name)),
            'email'    => strtolower(trim($request->email)),
            'cpf'      => preg_replace('/\D/', '', $request->cpf ?? ''),
            'phone'    => $request->phone ? preg_replace('/[^\d\+]/', '', $request->phone) : null,
            'password' => Hash::make($request->password),
        ]);

        // Vincula referral se veio com código
        if ($request->ref_code) {
            $referrer = User::where('referral_code', $request->ref_code)
                ->where('is_partner', true)
                ->first();

            if ($referrer) {
                DB::table('referrals')->insert([
                    'referrer_id'     => $referrer->id,
                    'referred_id'     => $user->id,
                    'commission_rate' => \App\Services\CommissionService::getRate(
                        DB::table('referrals')->where('referrer_id', $referrer->id)->where('status','active')->count()
                    ),
                    'status'          => 'active',
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }

        Auth::login($user);
        return redirect()->route('subscription.index')
            ->with('success', 'Conta criada! Ative sua assinatura para começar.');
    }
}
