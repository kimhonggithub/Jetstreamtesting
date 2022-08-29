<?php

namespace App\Http\Livewire\Admin\Users;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithPagination;

class ListUsers extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $state=[];
    // public $email;
    // public $phone;
    // public $name;
    public $showEditModal=false;
    public $user;
    public $useridBeingRemoved;

    public function addNew(){
      //use state to store default value
      $this->state=[];
      // $this->name='';
      // $this->email='';
      // $this->phone='';
      $this->showEditModal= false;
      $this->dispatchBrowserEvent('show-form');

    }
    public function CreateUser(){
      $validatedData = Validator::make($this -> state, [
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed',

      ])->validate();
    
      $validatedData['password']= bcrypt($validatedData['password']);

      User::create($validatedData);
     
      //session()->flash('message','user added successfully!');

      $this->dispatchBrowserEvent('hide-form',['message'=> 'User added successfully!']);

 
      
    }
    //when clicking on edit btn
    public function edit(User $user){
      $this->showEditModal=true;
      $this->user = $user;
      $this->state = $user->toArray();
      $this->dispatchBrowserEvent('show-form');
    }

    public function UpdateUser(){
      $validatedData = Validator::make($this -> state, [
        'name' => 'required',
        'email' => 'required|email|unique:users,email,'.$this->user->id,
        'password' => 'sometimes|confirmed',

      ])->validate();
      if(!empty($validatedData['password'])){
        $validatedData['password']= bcrypt($validatedData['password']);
      }
      $this->user->update($validatedData);
      //session()->flash('message','user added successfully!');

      $this->dispatchBrowserEvent('hide-form',['message'=> 'User Updated Successfully!']);

      
    }
    public function confirmUserRemoval($userId){
      $this->useridBeingRemoved = $userId;
      $this->dispatchBrowserEvent('show-delete');
    }
    public function deleteUser(){
      // $post = User::find($this->useridBeingRemoved);

      //   if(is_null($post)){
      //     return abort(404);
      //   }

      //   dd($post);
      //code above can be use as findorFail 
      $user = User::findOrFail($this->useridBeingRemoved);
      $user->delete();
      $this->dispatchBrowserEvent('hide-delete',['message'=> 'User Deleted Successfully!']);
    }
    public function render()
    {
        $users = User::latest()->paginate(5);
        return view('livewire.admin.users.list-users',[
          'users' => $users,
        ]);
    }
   
   
}