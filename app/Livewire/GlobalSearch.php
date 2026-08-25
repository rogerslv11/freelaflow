<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Contract;
use App\Models\File;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Task;
use Livewire\Component;

class GlobalSearch extends Component
{
    public $query = '';

    public $open = false;

    public function getResultsProperty()
    {
        if (strlen($this->query) < 2) {
            return [];
        }
        $q = $this->query;
        $limit = 5;

        $clients = Client::where('name', 'like', "%{$q}%")->orWhere('company', 'like', "%{$q}%")->take($limit)->get()
            ->map(fn ($m) => ['label' => $m->name, 'sub' => $m->company ?? 'Cliente', 'route' => route('clients.show', $m)]);
        $projects = Project::where('name', 'like', "%{$q}%")->take($limit)->get()
            ->map(fn ($m) => ['label' => $m->name, 'sub' => 'Projeto', 'route' => route('projects.show', $m)]);
        $tasks = Task::where('title', 'like', "%{$q}%")->take($limit)->get()
            ->map(fn ($m) => ['label' => $m->title, 'sub' => 'Tarefa', 'route' => route('tasks.index')]);
        $proposals = Proposal::where('title', 'like', "%{$q}%")->take($limit)->get()
            ->map(fn ($m) => ['label' => $m->title, 'sub' => 'Proposta', 'route' => route('proposals.show', $m)]);
        $invoices = Invoice::where('number', 'like', "%{$q}%")->take($limit)->get()
            ->map(fn ($m) => ['label' => $m->number ?? ('#'.$m->id), 'sub' => 'Fatura', 'route' => route('invoices.show', $m)]);
        $contracts = Contract::where('title', 'like', "%{$q}%")->take($limit)->get()
            ->map(fn ($m) => ['label' => $m->title, 'sub' => 'Contrato', 'route' => route('contracts.show', $m)]);
        $files = File::where('original_name', 'like', "%{$q}%")->take($limit)->get()
            ->map(fn ($m) => ['label' => $m->original_name, 'sub' => 'Arquivo', 'route' => route('files.index')]);

        $groups = [
            'Clientes' => $clients,
            'Projetos' => $projects,
            'Tarefas' => $tasks,
            'Propostas' => $proposals,
            'Faturas' => $invoices,
            'Contratos' => $contracts,
            'Arquivos' => $files,
        ];

        return collect($groups)->filter(fn ($items) => $items->isNotEmpty())->toArray();
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}
