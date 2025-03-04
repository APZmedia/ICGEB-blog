<?php get_header(); ?>

<div class="flex flex-col min-h-screen">
    <main class="flex-grow pt-[30px] md:pt-[170px] lg:pt-[120px] pb-[80px]">
        <div class="<?php echo is_home() ? 'container mx-auto px-4 lg:w-3/4' : 'container mx-auto px-4 lg:w-full'; ?>">

            <?php if (is_search()) : ?>
                <?php get_template_part('search'); ?> <!-- Loads search.php -->
            
            <?php elseif (is_home() || is_front_page()) : ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-16">
                    <?php
                    $homepage_posts = new WP_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 4, // Display max 4 posts
                    ));

                    if ($homepage_posts->have_posts()) :
                        while ($homepage_posts->have_posts()) : $homepage_posts->the_post();
                    ?>
                            <article class="bg-white shadow-xl rounded-lg p-6 hover:shadow-lg transition-shadow">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="mb-4">
                                            <?php the_post_thumbnail('medium', ['class' => 'w-full h-auto rounded-md']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <h3 class="text-2xl font-bold text-icgeb-blue hover:text-icgeb-hover mb-2">
                                        <?php echo wordwrap(get_the_title(), 52, '<br/>'); ?>
                                    </h3>
                                    <p class="text-sm text-gray-500 mb-4">
                                        Published on <?php the_time('F j, Y'); ?> by <?php the_author(); ?>
                                    </p>
                                    <p class="text-gray-600">
                                        <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                    </p>
                                </a>
                            </article>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo '<p class="text-center">No posts found.</p>';
                    endif;
                    ?>
                </div>
            <?php else : ?>
                <!-- Default behavior for other pages -->
                <?php if (have_posts()) : ?>
                    <div class="grid gap-12">
                        <?php while (have_posts()) : the_post(); ?>
                            <article class="prose max-w-none">
                                <div class="text-sm italic text-icgeb-blue">
                                    by <?php the_author(); ?>
                                </div>
                                <h2 class="text-3xl font-bold text-icgeb-blue mt-2">
                                    <a href="<?php the_permalink(); ?>" class="hover:text-icgeb-hover transition-colors">
                                        <?php the_title(); ?>
                                    </a>
                                </h2>
                                <div class="text-gray-600 mt-2">
                                    <?php the_excerpt(); ?>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    </div>
                <?php else : ?>
                    <!-- No posts found message -->
                    <div class="text-center mt-20">
                        <h2 class="text-4xl font-bold text-icgeb-blue mb-4">No page found</h2>
                        <p class="text-lg text-gray-600">
                            It seems we can’t find what you’re looking for. Please try using the search bar next time.
                        </p>
                        <div class="mt-8">
                            <a href="<?php echo home_url(); ?>" class="bg-icgeb-blue text-white px-6 py-3 rounded-md text-lg hover:bg-icgeb-hover transition-colors">
                                Return to Homepage
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php get_footer(); ?>
</div>
