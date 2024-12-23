<?php get_header(); ?>

<div class="flex flex-col min-h-screen">
    <main class="flex-grow pt-[25px] lg:pt-[50px] pb-[75px] md:pb-[25px]">
        <div class="container mx-auto px-4">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <article class="prose max-w-none">
                        <div class="mb-8">
                            <h1 class="text-2xl lg:text-4xl font-extrabold mb-4"><?php the_title(); ?></h1>
                        </div>
                        <div class="content-area content-heading content-paragraph content-list content-links">
                            <?php the_content(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php endif; ?>

            <?php
            // Check if this is the contact page
            if (is_page('contact')) :
            ?>
                <div class="mt-12">
                    <h2 class="text-2xl font-bold mb-6">Contact Me</h2>
                    <form id="contact-form" class="max-w-lg mx-auto" method="post">
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                            <input type="text" id="name" name="name" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                            <input type="email" id="email" name="email" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        <div class="mb-6">
                            <label for="message" class="block text-gray-700 text-sm font-bold mb-2">Message</label>
                            <textarea id="message" name="message" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline h-32"></textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Send
                            </button>
                        </div>
                    </form>
                    <div id="form-message" class="mt-4 text-center hidden"></div>
                </div>

                <script>
var my_ajax_object = <?php echo json_encode(array('ajax_url' => admin_url('admin-ajax.php'))); ?>;
</script>

                <script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contact-form');
    const formMessage = document.getElementById('form-message');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append('action', 'submit_contact_form');
        formData.append('security', '<?php echo wp_create_nonce("submit-contact-form"); ?>');

        fetch(my_ajax_object.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                formMessage.textContent = 'Thank you for your message. I will get back to you soon.';
                formMessage.classList.remove('text-red-500');
                formMessage.classList.add('text-green-500');
                form.reset();
            } else {
                formMessage.textContent = 'There was an error submitting your message. Please try again.';
                formMessage.classList.remove('text-green-500');
                formMessage.classList.add('text-red-500');
            }
            formMessage.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            formMessage.textContent = 'There was an error submitting your message. Please try again.';
            formMessage.classList.remove('text-green-500');
            formMessage.classList.add('text-red-500');
            formMessage.classList.remove('hidden');
        });
    });
});
</script>
            <?php endif; ?>
        </div>
    </main>

    <?php get_footer(); ?>
</div>

<style>
/* General Gutenberg Block Styling */
.content-area {
    margin-bottom: 1.5rem;
}

/* Headings */
.content-heading h1,
h1 {
    font-size: 2.25rem;
    font-weight: 800; 
    margin-bottom: 1rem;
    margin-top: 2rem;
}

.content-heading h2,
h2 {
    font-size: 1.875rem;
    font-weight: 700; 
    margin-bottom: 1rem;
    margin-top: 2rem;
}

.content-heading h3,
h3 {
    font-size: 1.5rem;
    font-weight: 700; 
    margin-bottom: 1rem;
    margin-top: 2rem;
}

.content-heading h4,
h4 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    margin-top: 2rem;
}

.content-heading h5,
h5 {
    font-size: 1rem; 
    font-weight: 500; 
    margin-bottom: 1rem;
    margin-top: 2rem;
}

.content-heading h6,
h6 {
    font-size: 0.75rem;
    font-weight: 500; 
    margin-bottom: 1rem;
    margin-top: 2rem;
}

/* Paragraphs */
.content-paragraph p {
    font-size: 1rem; 
    color: #4a5568; 
    line-height: 1.75;
}

/* Lists */
.content-list ul {
    list-style: disc;
    padding-left: 1.5rem;
    margin-bottom: 1.5rem;
    font-size: 1rem; 
    color: #4a5568; 
}

.content-list li {
    list-style: disc;
    padding-left: 1.5rem;
    margin-bottom: 1.5rem;
    font-size: 1rem; 
    color: #4a5568; 
}

/* Links */
.content-links a {
    color: #2563eb;
    text-decoration: underline;
}

</style>

