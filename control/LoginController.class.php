<?php

class LoginController extends Controller
{
    public function login($username)
    {
        $user = $this->model->get_user_by_username($username);
        if (!is_null($user))
        {
            $_SESSION['u_id'] = $user->u_id;
            return 
        }
    }
}

?>