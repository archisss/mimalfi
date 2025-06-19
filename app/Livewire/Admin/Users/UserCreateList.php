<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\UserDetail;

class UserCreateList extends Component
{
    use WithPagination;

    public $search = '';
    public $userTypeFilter = '';

    protected $queryString = ['search', 'userTypeFilter'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingUserTypeFilter()
    {
        $this->resetPage();
    }

    public function cleansearch(){
        $this->search='';
    }

    public function render()
    {
        $users = User::with('userDetail')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->when($this->userTypeFilter !== '', function ($query) {
                $query->where('user_type', $this->userTypeFilter);
            })
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.admin.users.user-create-list', [
            'users' => $users,
        ]);
    }
}
