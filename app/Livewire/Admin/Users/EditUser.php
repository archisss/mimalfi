<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use App\Models\User;
use App\Models\UserDetail;

class EditUser extends Component
{
    public $user_id;
    public $name;
    public $email;
    public $user_type;
    public $cellphone;
    public $phone;
    public $work_address;
    public $payment_address;

    public function mount($user_id)
    {
        $this->user_id = $user_id;
        $user = User::with('userDetail')->findOrFail($user_id);

        $this->name = $user->name;
        $this->email = $user->email;
        $this->user_type = $user->user_type;
        $this->cellphone = $user->cellphone;
        $this->phone = $user->phone;
        $this->work_address = optional($user->userDetail)->work_address;
        $this->payment_address = optional($user->userDetail)->payment_address;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'user_type' => 'required|in:1,2',
            'cellphone' => 'nullable|string',
            'phone' => 'nullable|string',
            'work_address' => 'nullable|string',
            'payment_address' => 'nullable|string',
        ]);

        $user = User::findOrFail($this->user_id);
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'user_type' => $this->user_type,
            'cellphone' => $this->cellphone,
            'phone' => $this->phone,
        ]);

        $user->userDetail()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'work_address' => $this->work_address,
                'payment_address' => $this->payment_address,
            ]
        );

        session()->flash('success', 'Usuario actualizado correctamente.');
        return redirect()->route('admin.users.user.create.list');
    }

    public function render()
    {
        return view('livewire.admin.users.edit-user');
    }
}
