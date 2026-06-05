<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PartnersController extends Controller
{
    public function index()
    {
        $pending  = User::where('partner_status', 'pending')->get();
        $approved = User::where('partner_status', 'approved')->where('is_partner', true)->withCount('referrals')->get();

        return view('admin.partners', compact('pending', 'approved'));
    }

    public function approve(int $id)
    {
        $user = User::findOrFail($id);
        $code = strtoupper(Str::random(8));

        DB::table('users')->where('id', $id)->update([
            'is_partner'     => true,
            'partner_status' => 'approved',
            'referral_code'  => $code,
        ]);

        // TODO: enviar e-mail de aprovação

        return back()->with('success', "Parceiro {$user->name} aprovado!");
    }

    public function reject(int $id)
    {
        $user = User::findOrFail($id);
        DB::table('users')->where('id', $id)->update([
            'is_partner'     => false,
            'partner_status' => 'rejected',
        ]);

        return back()->with('success', "Solicitação de {$user->name} recusada.");
    }
}
