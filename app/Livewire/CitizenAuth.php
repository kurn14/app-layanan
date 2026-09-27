<?php

namespace App\Livewire;

use App\Models\District;
use App\Models\User;
use App\Models\Village;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Masuk / Daftar Akun Warga — SAPA SOSIAL')]
class CitizenAuth extends Component
{
    public string $mode = 'login'; // 'login' or 'register'

    // Login Form
    public string $login_identifier = ''; // email or NIK

    public string $login_password = '';

    public bool $remember = false;

    // Register Form
    public string $name = '';

    public string $nik = '';

    public string $phone = '';

    public ?int $district_id = null;

    public ?int $village_id = null;

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $agree = false;

    public function mount(string $mode = 'login'): void
    {
        if (Auth::check()) {
            $this->redirect(route('akun'), navigate: true);

            return;
        }

        $this->mode = in_array($mode, ['login', 'register']) ? $mode : 'login';
    }

    public function switchMode(string $newMode): void
    {
        $this->mode = $newMode;
        $this->resetErrorBag();
    }

    public function updatedDistrictId(): void
    {
        $this->village_id = null;
    }

    public function login(): void
    {
        $this->validate([
            'login_identifier' => 'required|string',
            'login_password' => 'required|string',
        ], [
            'login_identifier.required' => 'Email atau NIK wajib diisi.',
            'login_password.required' => 'Kata sandi wajib diisi.',
        ]);

        $field = filter_var($this->login_identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'nik';

        if (Auth::attempt([$field => trim($this->login_identifier), 'password' => $this->login_password], $this->remember)) {
            session()->regenerate();
            session()->flash('success', 'Selamat datang kembali, '.Auth::user()->name);
            $this->redirectIntended(route('akun'), navigate: true);

            return;
        }

        $this->addError('login_identifier', 'Email/NIK atau kata sandi yang Anda masukkan tidak sesuai.');
    }

    public function register(): void
    {
        $this->validate([
            'name' => 'required|string|min:3|max:100',
            'nik' => 'required|digits:16|unique:users,nik',
            'phone' => 'required|string|min:9|max:20',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'agree' => 'accepted',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nik.digits' => 'NIK harus berupa 16 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar dalam sistem.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'agree.accepted' => 'Anda harus menyetujui ketentuan layanan.',
        ]);

        $user = User::create([
            'name' => $this->name,
            'nik' => $this->nik,
            'phone' => $this->phone,
            'district_id' => $this->district_id,
            'village_id' => $this->village_id,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'is_active' => true,
        ]);

        Auth::login($user);
        session()->flash('success', 'Akun berhasil dibuat! Selamat datang di SAPA SOSIAL.');
        $this->redirect(route('akun'), navigate: true);
    }

    public function render(): View
    {
        $districts = District::orderBy('name')->get();
        $villages = $this->district_id
            ? Village::where('district_id', $this->district_id)->orderBy('name')->get()
            : collect();

        return view('livewire.citizen-auth', [
            'districts' => $districts,
            'villages' => $villages,
        ]);
    }
}
