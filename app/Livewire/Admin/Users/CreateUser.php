<?php

namespace App\Livewire\Admin\Users;

use Faker\Provider\Image;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\UserDetail;

class CreateUser extends Component
{
    use WithFileUploads;

    public $name;
    public $email;
    public $password;
    public $user_type = '';
    public $address;
    public $phone;
    public $client_reference;
    public $work_address;
    public $payment_address;
    public $aval;
    //public $pictures = [];

    public $picture_ine, $picture_domicilio, $picture_foto;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'user_type' => 'required|in:1,2',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:14',
            'client_reference' => 'required_if:user_type,2|string|nullable',
            'work_address' => 'required_if:user_type,2|string|nullable',
            'payment_address' => 'required_if:user_type,2|string|nullable',
            'aval' => 'required_if:user_type,2|string|nullable',
            //'pictures.*' => 'image|max:1024', // Máximo 1MB por imagen
            'picture_ine'         => 'nullable|image|max:1024',
            'picture_domicilio'   => 'nullable|image|max:1024',
            'picture_foto'        => 'nullable|image|max:1024',
        ];
    }

    public function updatedUserType()
    {
        if ($this->user_type != 2) {
            $this->address = null;
            $this->phone = null;
            $this->client_reference = null;
            $this->work_address = null;
            $this->payment_address = null;
            $this->aval = null;
            $this->picture_ine = null;
            $this->picture_domicilio = null;
            $this->picture_foto = null;
        }
    }

    public function save()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt(1234567890), //bcrypt($this->password),
            'user_type' => $this->user_type,
        ]);

        if ($this->user_type == 2) {
            $userDetail = UserDetail::create([
                'user_id' => $user->id,
                'address' => $this->address,
                'phone' => $this->phone,
                'client_reference' => $this->client_reference,
                'work_address' => $this->work_address,
                'payment_address' => $this->payment_address,
                'aval' => $this->aval,
            ]);

             if (!empty($this->picture_ine)) {
                $userDetail->picture_ine = $this->picture_ine->store("users/{$user->id}", 'public');
            }
            if ($this->picture_domicilio) {
                $userDetail->picture_domicilio = $this->picture_domicilio->store("users/{$user->id}", 'public');
            }
            if ($this->picture_foto) {
                $userDetail->picture_foto = $this->picture_foto->store("users/{$user->id}", 'public');               
            }
            $userDetail->save();
        }

        session()->flash('success', 'Usuario creado exitosamente.');
        $this->reset();
    }

    public function render()
    {
        return view('livewire.admin.users.create-user');
    }
}
