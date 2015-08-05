<?php

/*
 * I deliberately don't have getters and setters
 * in this case they would only be single-line no-logic operations
 */
class Article extends AbstractObject
{
    /**
     * the actual content of the article
     */
    public $content;

    /**
     * array of User objects that represent the authors of the piece
     */
    public $authors;

    /**
     * title of the piece
     */
    public $title;

    /**
     * the publish date of the article
     */
    public $date;

    /**
     * the type of article ('review', 'column article', 'article')
     */
    public $type;

    /**
     * the type of article ('submitted', 'under review', etc)
     */
    public $status;

    /**
     * a link to the cover image of the article
     */
    public $cover_image;

    /**
     * an array of the editors that have edited this article, may be null
     */
    public $editors;

    /**
     * boolean whether or not this article has been highlighted
     */
    public $highlighted = false;
}
