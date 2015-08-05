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
            'show_result_text' => false,
        );

        $users = $this->_model->get_users_array();
        $data['users'] = $users;
        include_once TEMPLATE_PATH . 'base_template.tpl';
    }
}
