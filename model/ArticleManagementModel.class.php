<?php

class ArticleManagementModel extends Model
{
    private $submit_attempted = false;
    private $generic_article_mapper;
    private $user_mapper;
    private $comment_mapper;

    public function __construct()
    {
        Model::__construct();
        $this->generic_article_mapper = new GenericArticleMapper($this->_database_connection);
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
            'content' => $content,
            'authors' => $authors,
            'title' => $title,
            'type' => $type,
            'status' => 'submitted',
            'cover_image' => $cover_image,
            'column_name' => $column_name,
            'review_score' => $review_score
        );
        $article = $this->generic_article_mapper->create_new($data);
        $this->generic_article_mapper->save($article);
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
        return $this->generic_article_mapper->get_all();
    }

    public function update_article_status($a_id, $new_status)
    {
        $article = $this->generic_article_mapper->find_by_id($a_id);
        $article->status = $new_status;
        $this->generic_article_mapper->update($article);
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
        return $this->generic_article_mapper->find_by_id($article_id);
    }

    public function get_article_comments($article_id)
    {
        return $this->comment_mapper->find_all_by_article_id($article_id);
    }

    public function add_comment($article_id, $comment)
    {
        $data = array(
            'content' => $comment,
            'username' => $this->get_logged_in_username(),
            'a_id' => $article_id
        );
        $comment = $this->comment_mapper->create_new($data);
        $this->comment_mapper->save($comment);
    }

    public function update_article($title, $content, $authors, $cover_image, $article_id, $review_score, $column_name)
    {
        $this->submit_attempted = true;
        $article = $this->generic_article_mapper->find_by_id($article_id);
        $article->title = $title;
        $article->content = $content;
        $article->cover_image = $cover_image;
        if (!is_null($review_score))
        {
            $article->review_score = $review_score;
        } else if (!is_null($column_name)) {
            $article->column_name = $column_name;
        }
        $this->generic_article_mapper->update($article);
        $this->generic_article_mapper->add_editor($article->get_id(), $this->get_logged_in_user());

        $previous_authors = $article->authors;
        foreach ($authors as $author)
        {
            $new = true;
            foreach ($previous_authors as $previous_author)
            {
                if ($previous_author->get_id() == $author)
                {
                    $new = false;
                }
            }
            if ($new)
            {
                $this->generic_article_mapper->add_author($author, $article->get_id());
            }
        }
    }

    public function get_article_editors($article)
    {
        return $this->generic_article_mapper->get_article_editors($article->get_id());
    }

    public function highlight_article($article_id)
    {
        $article = $this->generic_article_mapper->find_by_id($article_id);
        $this->generic_article_mapper->highlight($article);
    }

    public function get_highlighted_articles($limit)
    {
        return $this->generic_article_mapper->get_recent_highlighted_articles($limit);
    }
    
    public function can_user_like_dislike()
    {
        if ($this->is_user_logged_in())
        {
            return true;
        } else {
            return false;
        }
    }

    public function like_article($user, $article_id)
    {
        $liked_articles = $this->generic_article_mapper->get_all_articles_with_impression_by_user($user, 'like');
        foreach ($liked_articles as $liked_article)
        {
            if ($liked_article->get_id() == $article_id)
            {
                $this->_record_error('you have already liked this article');
                return null;
            }
        }
        $this->generic_article_mapper->store_impression($user, $article_id, 'like');
    }

    public function dislike_article($user, $article_id)
    {
        $disliked_articles = $this->generic_article_mapper->get_all_articles_with_impression_by_user($user, 'dislike');
        foreach ($disliked_articles as $disliked_article)
        {
            if ($disliked_article->get_id() == $article_id)
            {
                $this->_record_error('you have already disliked this article');
                return null;
            }
        }
        $this->generic_article_mapper->store_impression($user, $article_id, 'dislike');
    }

    public function has_current_user_liked_article($article)
    {
        $liked_articles = $this->generic_article_mapper->get_all_articles_with_impression_by_user($this->get_logged_in_user(), 'like');
        foreach ($liked_articles as $liked_article)
        {
            if ($liked_article->get_id() == $article->get_id())
            {
                return true;
            }
        }
        // none of the likes was on this article
        return false;
    }

    public function has_current_user_disliked_article($article)
    {
        $disliked_articles = $this->generic_article_mapper->get_all_articles_with_impression_by_user($this->get_logged_in_user(), 'dislike');
        foreach ($disliked_articles as $disliked_article)
        {
            if ($disliked_article->get_id() == $article->get_id())
            {
                return true;
            }
        }
        // none of the dislikes was on this article
        return false;
    }

    public function get_most_liked($limit)
    {
        return $this->generic_article_mapper->get_most_liked($limit);
    }

    public function get_recent_articles($limit)
    {
        $article_mapper = new ArticleMapper($this->_database_connection);
        return $article_mapper->get_recent($limit);
    }

    public function get_recent_reviews($limit)
    {
        $review_mapper = new ReviewMapper($this->_database_connection);
        return $review_mapper->get_recent($limit);
    }

    public function get_recent_column_articles($limit)
    {
        $column_article_mapper = new ColumnArticleMapper($this->_database_connection);
        return $column_article_mapper->get_recent($limit);
    }
}

?>