<?php

namespace App\Livewire;

use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Settings extends Component
{
    // profile
    public $name = '';

    public $email = '';

    public $phone = '';

    // company
    public $company_name = '';

    public $document = '';

    public $address = '';

    public $city = '';

    public $state = '';

    public $country = '';

    public $postal_code = '';

    // financial
    public $currency = 'BRL';

    public $bank_details = '';

    public $payment_methods = '';

    // notifications
    public $notify_email = true;

    public $notify_browser = true;

    // security
    public $current_password = '';

    public $new_password = '';

    public $new_password_confirmation = '';

    public function mount()
    {
        $user = Auth::user();
        $profile = $user->profile;
        $this->name = $user->name;
        $this->email = $user->email;
        if ($profile) {
            $this->phone = $profile->phone;
            $this->company_name = $profile->company_name;
            $this->document = $profile->document;
            $this->address = $profile->address;
            $this->city = $profile->city;
            $this->state = $profile->state;
            $this->country = $profile->country;
            $this->postal_code = $profile->postal_code;
            $this->currency = $profile->currency;
            $this->bank_details = $profile->preferences['bank'] ?? '';
            $this->payment_methods = $profile->preferences['payment_methods'] ?? '';
            $this->notify_email = $profile->preferences['notify_email'] ?? true;
            $this->notify_browser = $profile->preferences['notify_browser'] ?? true;
        }
    }

    public function saveProfile()
    {
        $user = Auth::user();
        $this->validate([
            'name' => 'required|min:2|max:120',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|max:40',
        ]);
        $user->update(['name' => $this->name, 'email' => $this->email]);
        $this->updateProfile(['phone' => $this->phone]);
        $this->dispatch('toast', message: 'Perfil atualizado.', type: 'success');
    }

    public function saveCompany()
    {
        $this->validate([
            'company_name' => 'nullable|max:120',
            'document' => 'nullable|max:40',
            'address' => 'nullable|max:255',
            'city' => 'nullable|max:80',
            'state' => 'nullable|max:40',
            'country' => 'nullable|max:80',
            'postal_code' => 'nullable|max:20',
        ]);
        $this->updateProfile([
            'company_name' => $this->company_name,
            'document' => $this->document,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'postal_code' => $this->postal_code,
        ]);
        $this->dispatch('toast', message: 'Empresa atualizada.', type: 'success');
    }

    public function saveFinancial()
    {
        $this->validate([
            'currency' => 'required|in:BRL,USD,EUR',
            'bank_details' => 'nullable',
            'payment_methods' => 'nullable',
        ]);
        $profile = $this->updateProfile(['currency' => $this->currency]);
        $prefs = $profile->preferences ?? [];
        $prefs['bank'] = $this->bank_details;
        $prefs['payment_methods'] = $this->payment_methods;
        $profile->update(['preferences' => $prefs]);
        $this->dispatch('toast', message: 'Configurações financeiras salvas.', type: 'success');
    }

    public function saveNotifications()
    {
        $profile = $this->updateProfile([]);
        $prefs = $profile->preferences ?? [];
        $prefs['notify_email'] = $this->notify_email;
        $prefs['notify_browser'] = $this->notify_browser;
        $profile->update(['preferences' => $prefs]);
        $this->dispatch('toast', message: 'Preferências de notificação salvas.', type: 'success');
    }

    public function changePassword()
    {
        $user = Auth::user();
        $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', Password::defaults(), 'confirmed'],
        ]);
        $user->update(['password' => Hash::make($this->new_password)]);
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->dispatch('toast', message: 'Senha alterada.', type: 'success');
    }

    public function logoutOtherDevices()
    {
        $this->validate(['current_password' => ['required', 'current_password']]);
        Auth::logoutOtherDevices($this->current_password);
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        $this->dispatch('toast', message: 'Sessões encerradas.', type: 'success');
    }

    protected function updateProfile(array $data)
    {
        $profile = Auth::user()->profile ?? Profile::create(['user_id' => Auth::id()]);
        $profile->update($data);

        return $profile;
    }

    public function render()
    {
        return view('livewire.settings', [
            'currencies' => Profile::CURRENCIES,
        ])->title('Configurações');
    }
}
