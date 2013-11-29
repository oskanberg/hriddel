<?php

class UserManagementView extends View
{
    private $template = 'user_management.tpl';
    private $title = 'Manage Users';

    public function display()
    {
        $data = array(
            'title' => $this->title,
            'view_specific_template' => $this->template,
            'show_error' => false,
            'show_login' => true,
            'show_result_text' => false
        );
        
        $users = $this->_model->get_users_array();
        //$subscribers = array();
        //$writers = array();
        //$publishers = array();
        //$editors = array();
        //foreach ($users as $user)
        //{
        //    switch ($user->type)
        //    {
        //        case 'subscriber':
        //            $subscribers[] = $user;
        //            break;
        //        case 'writer':
        //            $writers[] = $user;
        //            break;
        //        case 'subscriber':
        //            $publishers[] = $user;
        //            break;
        //        case 'editor':
        //            $editors[] = $user;
        //            break;
        //    }
        //}
        //$data['user_map']['subscribers'] = $subscribers;
        //$data['user_map']['writers'] = $writers;
        //$data['user_map']['publishers'] = $publishers;
        //$data['user_map']['editors'] = $editors;
        $data['users'] = $users;
        include_once(TEMPLATE_PATH . 'base_template.tpl');
    }
}

?>