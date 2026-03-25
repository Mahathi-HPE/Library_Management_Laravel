<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MemberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AuthController extends Controller
{
    public function __construct(private readonly MemberService $memberService)
    {
    }

    public function login()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.login');
    }

    public function register()
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.register');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::findByUsernameWithRole($data['username']);

        if (!$user) {
            return back()->withErrors(['username' => 'Invalid credentials.'])->onlyInput('username');
        }

        $storedPassword = (string) $user->Password;

        // Support legacy plain-text seeded passwords without crashing bcrypt verification.
        $validPassword = hash_equals($storedPassword, $data['password']);

        if (!$validPassword) {
            try {
                $validPassword = Hash::check($data['password'], $storedPassword);
            } catch (RuntimeException) {
                $validPassword = false;
            }
        }

        if (!$validPassword || !in_array($user->RName, ['User', 'Admin'], true)) {
            return back()->withErrors(['username' => 'Invalid credentials.'])->onlyInput('username');
        }

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('role', $user->RName);

        if ($user->RName === 'User') {
            $member = $this->memberService->findByUserId((int) $user->Uid);

            if (!$member) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors(['username' => 'No member record found.'])->onlyInput('username');
            }

            $request->session()->put('mid', (int) $member->Mid);
            $request->session()->put('mem_name', $member->MemName);
        }

        return $this->redirectByRole();
    }

    public function storeRegistration(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:Users,Username'],
            'password' => ['required', 'string', 'min:6'],
            'location' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:Members,MemEmail'],
        ]);

        User::createMemberUser($data);

        return redirect()->route('auth.login')->with('message', 'Registration successful. You can now log in as a member.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login');
    }

    private function redirectByRole(): RedirectResponse
    {
        $role = (string) session('role', 'User');

        return $role === 'Admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('member.dashboard');
    }
}
