<?php

/**
 * A controller for everything concerning articles
 */

class ArticleManagementController extends Controller
{

    /**
     * Submit an article. Gets parameters from $_POST
     * returns nothing
     */
    public function submitArticle()
    {
        if (isset($_POST['title']) && isset($_POST['content']) && isset($_POST['type']) && isset($_POST['additional_authors']) && isset($_POST['cover_image'])) {
            $additional_authors = array_filter(explode(';', $_POST['additional_authors']));
            if ($_POST['type'] == 'column article') {
                if (!isset($_POST['column_name'])) {
                    throw new Exception('Need column name.');
                }
                $this->_model->submitArticle($_POST['title'], $_POST['content'], $_POST['type'], $additional_authors, $_POST['cover_image'], $_POST['column_name'], null);
            } else if ($_POST['type'] == 'review') {
                if (!isset($_POST['review_score'])) {
                    throw new Exception('Need review score.');
                }
                $this->_model->submitArticle($_POST['title'], $_POST['content'], $_POST['type'], $additional_authors, $_POST['cover_image'], null, $_POST['review_score']);
            } else {
                $this->_model->submitArticle($_POST['title'], $_POST['content'], $_POST['type'], $additional_authors, $_POST['cover_image'], null, null);
            }
        } else {
            echo 'stuff not set';
            var_dump($_POST);
        }
    }

    /**
     * Update attributes in a content piece. Cannot change type. Gets parameters from $_POST
     * returns nothing
     */
    public function ammendArticle()
    {
        if (isset($_POST['title']) && isset($_POST['content']) && isset($_POST['authors']) && isset($_POST['cover_image'])) {
            $review_score = isset($_POST['review_score']) ? $_POST['review_score'] : null;
            $column_name = isset($_POST['column_name']) ? $_POST['column_name'] : null;
            $authors = array_filter(explode(';', $_POST['authors']));
            $this->_model->update_article($_POST['title'], $_POST['content'], $authors, $_POST['cover_image'], $_GET['a_id'], $review_score, $column_name);
        } else {
            echo 'stuff not set';
            var_dump($_POST);
        }
    }

    /**
     * Change the status on an article. Gets parameters from $_POST
     * returns nothing
     */
    public function changeArticleStatus()
    {
        if (isset($_POST['a_id']) && isset($_POST['new_status'])) {
            $this->_model->update_article_status($_POST['a_id'], $_POST['new_status']);
        } else {
            echo 'stuff not set';
            var_dump($_POST);
        }
    }

    /**
     * Add a comment from the current user to a given article. Gets parameters from $_POST
     * returns nothing
     */
    public function addComment()
    {
        if (isset($_POST['a_id']) && isset($_POST['comment'])) {
            $this->_model->addComment($_POST['a_id'], $_POST['comment']);
        } else {
            echo 'stuff not set';
            var_dump($_POST);
        }
    }

    /**
     * Highlight an article. Gets parameters from $_POST
     * returns nothing
     */
    public function highlightArticle()
    {
        if (isset($_POST['a_id'])) {
            $this->_model->highlightArticle($_POST['a_id']);
        } else {
            echo 'stuff not set';
            var_dump($_POST);
        }
    }

    /**
     * Dislike an article as the current user. Gets parameters from $_POST
     * returns nothing
     */
    public function dislikeArticle()
    {
        if (isset($_POST['a_id'])) {
            $this->_model->dislikeArticle($this->_model->get_logged_in_user(), $_POST['a_id']);
        }
    }

    /**
     * Like an article as the current user. Gets parameters from $_POST
     * returns nothing
     */
    public function likeArticle()
    {
        if (isset($_POST['a_id'])) {
            $this->_model->likeArticle($this->_model->get_logged_in_user(), $_POST['a_id']);
        }
    }
}
