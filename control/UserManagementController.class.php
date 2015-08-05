<?php

/**
 * A controller for everything concerning user management
 */
class UserManagementController extends Controller
{
    /**
     * Authenticate the username. Gets parameters from $_POST
     * returns nothing
     */
    public function authenticate()
    {
        if (isset($_POST['username'])) {
            $this->_model->authenticate_username($_POST['username']);
        }
    }

    /**
     * end the session for the current user
     * returns nothing
     */
    public function logout()
    {
        session_destroy();
        unset($_SESSION['username']);
    }

    /**
     * Register a new user. Gets parameters from $_POST
     * returns nothing
     */
    public function registerUser()
    {
        if (isset($_POST['username']) && isset($_POST['name'])) {
            $this->_model->registerUser($_POST['username'], $_POST['name']);
        }
    }

    /**
     * Change the type of multiple users. Gets parameters from $_POST
     * returns nothing
     */
    public function changeTypeMultiple()
    {
        if (isset($_POST['users']) && isset($_POST['type'])) {
            $this->_model->changeTypeMultiple($_POST['users'], $_POST['type']);
        }
    }
}
