<?php  

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class LoginService
{       
    public function tryLoginOrFail($dataUser){

        if(!Auth::attempt($dataUser))
                return false;

	return true;
    }

}
