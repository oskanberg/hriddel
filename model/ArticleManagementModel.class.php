<?php

class ArticleManagementModel extends Model
{
    private $submit_attempted = false;
    private $column_article_mapper;
    private $review_mapper;
    private $user_mapper;
    private $comment_mapper;

    public function __construct()
    {
        Model::__construct();
        $this->article_mapper = new ArticleMapper($this->_database_connection);
        $this->column_article_mapper = new ColumnArticleMapper($this->_database_connection);
        $this->review_mapper = new ReviewMapper($this->_database_connection);
        $this->user_mapper = new UserMapper($this->_database_connection);
        $this->comment_mapper = new CommentMapper($this->_database_connection);
    }
    
    public function submit_article($title, $content, $type, $additional_authors, $cover_image, $column_name, $review_score)
    {
        // so we can query it from the view later
        $this->submit_attempted = true;
        /* $filename = $this->handle_file_upload();
        if (is_null($filename))
        {
            // image uploading failed.
            // the relevant error(s) will already have been recorded
            return null;
        } */

        $authors = array();
        $authors[] = $this->get_logged_in_user();
        foreach ($additional_authors as $username)
        {
            $authors[] = $this->user_mapper->find_by_id($username);
        }
        $data = array(
            'contents' => $content,
            'authors' => $authors,
            'title' => $title,
            'type' => $type,
            'status' => 'submitted',
            'cover_image' => $cover_image
        );
        if (!is_null($column_name))
        {
            // it's a column article
            $data['column_name'] = $column_name;
            $column_name = $this->column_article_mapper->create_new($data);
            $this->column_article_mapper->save($column_name);
        } else if (!is_null($review_score)) {
            // it's a reivew
            $data['review_score'] = $review_score;
            $new_review = $this->review_mapper->create_new($data);
            $this->review_mapper->save($new_review);
        } else {
            // it's a regular article
            $data['column_name'] = $column_name;
            $new_article = $this->article_mapper->create_new($data);
            $this->article_mapper->save($new_article);
        }
    }

    /*
        Turns out this function is redundant: cover image is now just a link to external site
    */
    public function handle_file_upload()
    {
        if ($_FILES['cover_image']['error'] > 0)
        {
            $this->_record_error($_FILES['cover_image']['error']);
            return null;
        }
        $permitted_types = array(
            'image/gif',
            'image/jpeg',
            'image/jpg',
            'image/pjpeg',
            'image/x-png',
            'image/png'
        );
        if (!in_array($_FILES['cover_image']['type'], $permitted_types))
        {
            $this->_record_error('The supplied image was an unpermitted type (' . $_FILES['cover_image']['type'] . '). Try another.');
            return null;
        }
        $permitted_extensions = array(
            'jpg',
            'jpeg',
            'png',
            'gif'
        );
        $split_name = explode('.', $_FILES['cover_image']['name']);
        $extension = end($split_name);
        if (!in_array($extension, $permitted_extensions))
        {
            $this->_record_error('The supplied image has an unpermitted file extension (' . $extension . ').');
            return null;
        }
        if ($_FILES['cover_image']['size'] > 50000)
        {
            $this->_record_error('The supplied image was too large. Please upload a smaller image.');
            return null;
        }
        $file_hash = hash_file('sha1', $_FILES['cover_image']['tmp_name']);
        $new_filename = $file_hash . '.' . $extension;
        move_uploaded_file($_FILES['cover_image']['tmp_name'], ROOTPATH . '/images/' . $new_filename);
        return $new_filename;
    }

    public function has_submit_been_attempted()
    {
        return $this->submit_attempted;
    }

    /*
    * return array of articles that the current user
    * has permissions to manage
    */
    public function get_articles_array_restricted()
    {
        if ($this->is_user_logged_in())
        {
            $user_type = $this->get_logged_in_user()->type;
            if ($user_type == 'subscriber')
            {
                return array();
            } else if ($user_type == 'writer') {
                // only return articles by the writer
                $all = $this->get_articles_array();
                $mine = array();
                foreach ($all as $article)
                {
                    foreach ($article->authors as $author)
                    {
                        if ($author->username == $this->get_logged_in_username())
                        {
                            $mine[] = $article;
                        }
                    }
                }
                return $mine;
            } else {
                // publishers and editors can see all articles
                return $this->get_articles_array();
            }
        } else {
            return array();
        }
    }

    public function get_articles_array()
    {
        $articles = array_merge(
            $this->column_article_mapper->get_all(),
            $this->review_mapper->get_all(),
            $this->article_mapper->get_all()
        );
        return $articles;
    }

    public function update_article_status($a_id, $new_status)
    {
        $mappers = array($this->article_mapper, $this->review_mapper, $this->column_article_mapper);
        $successful_mapper = null;
        $article = null;
        foreach ($mappers as $mapper)
        {
            $article = $mapper->find_by_id($a_id);
            if (!is_null($article))
            {
                $successful_mapper = $mapper;
                break;
            }
        }
        if (is_null($article))
        {
            $this->_record_error('No such article (id ' . $a_id . ') was found');
            return null;
        }
        $article->status = $new_status;
        $successful_mapper->update($article);
    }

    public function get_all_possible_authors()
    {
        $users = $this->user_mapper->get_all();
        $possible_authors = array();
        foreach ($users as $user)
        {
            if ($user->type != 'subscriber')
            {
                $possible_authors[] = $user;
            }
        }
        return $possible_authors;
    }

    public function get_article_by_id($article_id)
    {
        return $this->article_mapper->find_by_id($article_id);
    }

    public function get_article_comments($article_id)
    {
        return $this->comment_mapper->find_all_by_article_id($article_id);
    }
}

?>