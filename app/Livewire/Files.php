<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\File;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str as StrHelper;
use Livewire\Component;
use Livewire\WithFileUploads;

class Files extends Component
{
    use WithFileUploads;

    public $search = '';

    public $folder = 'root';

    public $file = null;

    public $client_id = '';

    public $project_id = '';

    public $folderName = 'root';

    protected function rules()
    {
        return [
            'file' => 'required|file|max:20480',
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'folderName' => 'required',
        ];
    }

    public function getRowsProperty()
    {
        return File::with('client', 'project')
            ->when($this->search, fn ($q) => $q->where('original_name', 'like', "%{$this->search}%"))
            ->where('folder', $this->folder)
            ->latest()
            ->paginate(15);
    }

    public function getFoldersProperty()
    {
        return File::where('user_id', Auth::id())
            ->distinct()
            ->pluck('folder')
            ->filter()
            ->values()
            ->toArray();
    }

    public function upload()
    {
        $this->validate();
        $name = StrHelper::random(20).'.'.$this->file->getClientOriginalExtension();
        $path = $this->file->storeAs('uploads', $name, 'local');
        File::create([
            'user_id' => Auth::id(),
            'client_id' => $this->client_id ?: null,
            'project_id' => $this->project_id ?: null,
            'name' => $name,
            'original_name' => $this->file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $this->file->getMimeType(),
            'extension' => $this->file->getClientOriginalExtension(),
            'size' => $this->file->getSize(),
            'folder' => $this->folderName ?: 'root',
            'uploaded_by' => Auth::user()->name,
        ]);
        $this->reset(['file', 'client_id', 'project_id', 'folderName']);
        $this->dispatch('modal-close');
        $this->dispatch('toast', message: 'Arquivo enviado.', type: 'success');
    }

    public function delete($id)
    {
        $file = File::findOrFail($id);
        Storage::disk('local')->delete($file->path);
        $file->delete();
        $this->dispatch('toast', message: 'Arquivo excluído.', type: 'success');
    }

    public function setFolder($folder)
    {
        $this->folder = $folder;
    }

    public function render()
    {
        return view('livewire.files', [
            'rows' => $this->rows,
            'folders' => $this->folders,
            'clients' => Client::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
        ])->title('Arquivos');
    }
}
