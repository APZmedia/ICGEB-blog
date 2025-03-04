<?php
/**
 * The template for displaying search results pages
 *
 * @package icgeb-blog
 */

get_header();
?>

<div class="flex flex-col min-h-screen">
    <main class="flex-grow container mx-auto px-4 py-8">
        <h1 class="text-3xl text-center font-bold mt-10 mb-8">Search Results</h1>

        <form role="search" method="get" class="search-form mb-8 mt-10" action="<?php echo esc_url(home_url('/')); ?>">
            <div class="flex">
                <input 
                    type="search" 
                    class="flex-grow px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                    placeholder="Search..." 
                    value="<?php echo get_search_query(); ?>" 
                    name="s" 
                />
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 ml-2"
                >
                    Search
                </button>
            </div>
        </form>

        <?php
        // Determine if the user is actively searching
        $search_query = get_search_query();
        $is_active_search = isset($_GET['s']);

        if (!$is_active_search || $search_query === '') : ?>
            <p class="text-center text-gray-700">Enter a term to start searching.</p>
        <?php elseif ($is_active_search && have_posts()) : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('bg-white rounded-lg shadow-md overflow-hidden'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="aspect-w-16 aspect-h-9">
                                <?php the_post_thumbnail('medium', ['class' => 'object-cover w-full h-full']); ?>
                            </div>
                        <?php endif; ?>
                        <div class="p-4">
                            <h2 class="text-xl font-semibold mb-2">
                                <a href="<?php the_permalink(); ?>" class="text-blue-600 hover:text-blue-800">
                                    <?php the_title(); ?>
                                </a>
                            </h2>
                            <div class="text-sm text-gray-600 mb-2">
                                <?php
                                echo get_the_date();

                                // Display categories
                                $categories = get_the_category();
                                if ($categories) {
                                    $category_links = [];
                                    foreach ($categories as $category) {
                                        $category_links[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                                    }
                                    echo ' | ' . implode(', ', $category_links);
                                }
                                ?>
                            </div>
                            <div class="text-gray-700">
                                <?php the_excerpt(); ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => __('Previous', 'your-theme-text-domain'),
                'next_text' => __('Next', 'your-theme-text-domain'),
                'class' => 'mt-8',
            )); ?>
        <?php elseif ($is_active_search) : ?>
            <p class="text-center text-gray-700">No results found. Try another search term.</p>
        <?php endif; ?>
    </main>

    <?php get_footer(); ?>
</div>
