
<!-- begin reviews.tpl -->
<section class="content_box restrict_and_center pad">
    <h3 style="margin: 2px;">All Articles</h3>
    <?php 
    foreach ($data['reviews'] as $review)
    {
        echo '<a href="/?view_article&a_id=' . $review->get_id() . '">';
        echo '<div class="fiveply-content-box"';
        echo 'style="background-image:url(' . $review->cover_image . ')">';
        echo '<p>' . $review->title . '</p>';
        echo '</div>';
        echo '</a>';
    }
    ?>
</section>
<!-- end reviews.tpl -->
