<?php get_header(); ?>

<div class="flex flex-col min-h-screen">
    <main class="flex-grow pt-[25px] lg:pt-[50px] pb-[75px] md:pb-[25px]">
        <div class="container mx-auto px-4 text-center lg:w-2/3">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article class="prose max-w-none">
                        <div class="mb-8">
                            <h1 class="text-2xl lg:text-4xl font-extrabold mb-4"><?php the_title(); ?></h1>
                        </div>
                        <div class="text-justify text-gray-700 text-md md:text-xl leading-relaxed content-area">
                            <?php the_content(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php get_footer(); ?>
</div>

<style>
    .content-area a {
        color: #2563eb !important; 
        text-decoration: underline !important;
    }

    .content-area ul {
        list-style-type: disc; 
        margin-left: 1.5rem; 
        margin-bottom: 1rem;
    }

    .content-area ol {
        list-style-type: decimal; 
        margin-left: 1.5rem;
        margin-bottom: 1rem; 
    }

    .content-area li {
        margin-bottom: 0.5rem; 
    }    
</style>
