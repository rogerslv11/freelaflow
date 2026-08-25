<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Profile;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.minimal')]
class Onboarding extends Component
{
    public $step = 1;

    public $companyName = '';

    public $freelancerType = '';

    public $clientName = '';

    public $clientEmail = '';

    public $projectName = '';

    public $currency = 'BRL';

    protected $rules = [
        'companyName' => 'required|min:2|max:120',
        'freelancerType' => 'required',
        'clientName' => 'required|min:2|max:120',
        'clientEmail' => 'nullable|email',
        'projectName' => 'required|min:2|max:120',
        'currency' => 'required|in:BRL,USD,EUR',
    ];

    public function next()
    {
        $fieldMap = [
            1 => ['companyName'],
            2 => ['freelancerType'],
            3 => ['clientName', 'clientEmail'],
            4 => ['projectName'],
            5 => ['currency'],
        ];
        $this->validateOnlyStep($this->step);
        if ($this->step < 5) {
            $this->step++;
        } else {
            $this->finish();
        }
    }

    public function prev()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    protected function validateOnlyStep($step)
    {
        $fields = [
            1 => ['companyName' => $this->rules['companyName']],
            2 => ['freelancerType' => $this->rules['freelancerType']],
            3 => ['clientName' => $this->rules['clientName'], 'clientEmail' => $this->rules['clientEmail']],
            4 => ['projectName' => $this->rules['projectName']],
            5 => ['currency' => $this->rules['currency']],
        ];
        $this->validate($fields[$step]);
    }

    public function finish()
    {
        $this->validate();

        $user = Auth::user();
        $profile = $user->profile ?? new Profile(['user_id' => $user->id]);
        $profile->company_name = $this->companyName;
        $profile->currency = $this->currency;
        $profile->preferences = array_merge($profile->preferences ?? [], ['type' => $this->freelancerType]);
        $profile->onboarded = true;
        $profile->save();

        $client = Client::create([
            'user_id' => $user->id,
            'name' => $this->clientName,
            'email' => $this->clientEmail,
            'status' => 'active',
        ]);

        Project::create([
            'user_id' => $user->id,
            'client_id' => $client->id,
            'name' => $this->projectName,
            'status' => 'planning',
            'priority' => 'medium',
        ]);

        return $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.onboarding', [
            'types' => [
                'Desenvolvedor', 'Designer', 'Copywriter', 'Social Media', 'Fotógrafo',
                'Videomaker', 'Consultor', 'Marketing', 'Programador', 'Outro',
            ],
            'currencies' => Profile::CURRENCIES,
        ]);
    }
}
