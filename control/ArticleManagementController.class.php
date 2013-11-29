<?php

class ArticleManagementController extends Controller
{
    public function submit_article()
    {
        if (isset($_POST['title']) && isset($_POST['content']) && isset($_POST['type']) && isset($_POST['additional_authors']) && isset($_POST['cover_image']))
        {
            if ($_POST['type'] == 'column_article')
            {
                if (!isset($_POST['column_name']))
                {
                    throw new Exception('Need column name.');
                }
                $this->_model->submit_article($_POST['title'], $_POST['content'], $_POST['type'], $_POST['additional_authors'], $_POST['cover_image'], $_POST['column_name'], null);
            } else if ($_POST['type'] == 'review') {
                if (!isset($_POST['review_score']))
                {
                    throw new Exception('Need review score.');
                }
                $this->_model->submit_article($_POST['title'], $_POST['content'], $_POST['type'], $_POST['additional_authors'], $_POST['cover_image'], null, $_POST['review_score']);
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