
<!-- begin columns.tpl -->
<?php
foreach ($data['column_names'] as $column_name)
{
    echo '<section class="content_box restrict_and_center pad">';
    echo '<h3 style="margin: 2px;">' . $column_name . '</h3>';
    foreach ($data['column_articles'] as $column_article)
    {
        if ($column_article->column_name == $column_name)
        {
            echo '<a href="/?view_article&a_id=' . $column_article->get_id() . '">';
            echo '<div class="fiveply-content-box"';
            echo 'style="background-image:url(' . $column_article->cover_image . ')">';
            echo '<p>' . $column_article->title . '</p>';
            echo '</div>';
            echo '</a>';
        }
    }
    echo '</section><div class="separator"></div>';
}
?>
<!-- end columns.tpl -->
