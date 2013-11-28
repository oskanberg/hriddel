<?php

class ArticleManagementController extends Controller
{
    public function submit_article()
    {
        if (isset($_POST['title']) && isset($_POST['content']) && isset($_POST['type']) && isset($_POST['additional_authors']) && isset($_POST['cover_image']))
        {
            if (isset($_POST['type']) == 'column_article')
            {
                if (!isset($_POST['column_name']))
                {
                    throw new Exception('Need column name.');
                }
                $this->_model->submit_article($_POST['title'], $_POST['content'], $_POST['type'], $_POST['additional_authors'], $_POST['cover_image'], $_POST['column_name']);
            } else {
                $this->_model->submit_article($_POST['title'], $_POST['content'], $_POST['type'], $_POST['additional_authors'], $_POST['cover_image']);
            }
        } else {
            echo 'stuff not set';
            var_dump($_POST);
        }
    }
}

?>