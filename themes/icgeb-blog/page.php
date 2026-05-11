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
            if (is_page('contact')) : // You can also use is_page(ID_OF_CONTACT_PAGE) or is_page('contact-us-slug')
            ?>
            <iframe width="500" height="750" src="https://8cdeefec.sibforms.com/serve/MUIFALunIra9ygO86n6dog28-pMBkyOgKJEvtij-gM8lDImuh0BYx0q30hiGZnurpyzchv7liMHfQSi8Du2WlIOPunHa-3luN5rJqcbyq4al8tmZUMPIAP5ySRKUMbCzInpmxtiZv2X9QaHOz16dWBvDLuN0O9lQ8sRv8FYvm5tLHf8ybueWzbIymKPakB5jfeY-_0XSFo7vUX9h" frameborder="0" scrolling="auto" allowfullscreen style="display: block;margin-left: auto;margin-right: auto;max-width: 100%;"></iframe>
                <div class="mt-12">
                    <h2 class="text-2xl font-bold mb-6">Contact Us</h2>
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
                        <div class="mb-6">
                            <input type="checkbox" id="gdpr" name="gdpr" required>
                            <label for="gdpr" class="text-gray-700 text-sm">I accept the privacy policy and the processing of my personal data</label>
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
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.getElementById('contact-form');
                        const formMessage = document.getElementById('form-message');

                        // Check if the localized script object from functions.php exists
                        if (typeof icgeb_contact_form_ajax === 'undefined') {
                            console.error('AJAX object (icgeb_contact_form_ajax) not found. Check wp_localize_script in functions.php.');
                            if(formMessage) { // Check if formMessage element exists before using it
                                formMessage.textContent = 'Form submission error. Please contact support (AJAX object missing).';
                                formMessage.classList.add('text-red-500');
                                formMessage.classList.remove('hidden');
                            }
                            return; // Stop if object is missing
                        }

                        if (form) { // Ensure the form element exists
                            form.addEventListener('submit', function(e) {
                                e.preventDefault();

                                const formData = new FormData(form);
                                formData.append('action', 'submit_contact_form');
                                // Use the nonce and ajax_url from the localized object
                                formData.append('security', icgeb_contact_form_ajax.nonce);

                                fetch(icgeb_contact_form_ajax.ajax_url, {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (formMessage) { // Check if formMessage element exists
                                        if (data.success) {
                                            formMessage.textContent = data.data.message || 'Thank you for your message. It has been sent.';
                                            formMessage.classList.remove('text-red-500');
                                            formMessage.classList.add('text-green-500');
                                            form.reset();
                                        } else {
                                            formMessage.textContent = data.data.message || 'There was an error submitting your message. Please try again.';
                                            formMessage.classList.remove('text-green-500');
                                            formMessage.classList.add('text-red-500');
                                        }
                                        formMessage.classList.remove('hidden');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    if (formMessage) { // Check if formMessage element exists
                                        formMessage.textContent = 'A network error occurred. Please try again.';
                                        formMessage.classList.remove('text-green-500');
                                        formMessage.classList.add('text-red-500');
                                        formMessage.classList.remove('hidden');
                                    }
                                });
                            });
                        }
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
h1 { /* Styles global H1 also */
    font-size: 2.25rem; /* Tailwind text-4xl */
    font-weight: 800; /* Tailwind font-extrabold */
    margin-bottom: 1rem; /* Tailwind mb-4 */
    margin-top: 2rem; /* Tailwind mt-8 */
}

.content-heading h2,
h2 { /* Styles global H2 also */
    font-size: 1.875rem; /* Tailwind text-3xl */
    font-weight: 700; /* Tailwind font-bold */
    margin-bottom: 1rem; /* Tailwind mb-4 */
    margin-top: 2rem; /* Tailwind mt-8 */
}

.content-heading h3,
h3 { /* Styles global H3 also */
    font-size: 1.5rem; /* Tailwind text-2xl */
    font-weight: 700; /* Tailwind font-bold */
    margin-bottom: 1rem; /* Tailwind mb-4 */
    margin-top: 2rem; /* Tailwind mt-8 */
}

.content-heading h4,
h4 { /* Styles global H4 also */
    font-size: 1.25rem; /* Tailwind text-xl */
    font-weight: 600; /* Tailwind font-semibold */
    margin-bottom: 1rem; /* Tailwind mb-4 */
    margin-top: 2rem; /* Tailwind mt-8 */
}

.content-heading h5,
h5 { /* Styles global H5 also */
    font-size: 1rem; /* Tailwind text-base */
    font-weight: 500; /* Tailwind font-medium */
    margin-bottom: 1rem; /* Tailwind mb-4 */
    margin-top: 2rem; /* Tailwind mt-8 */
}

.content-heading h6,
h6 { /* Styles global H6 also */
    font-size: 0.75rem; /* Tailwind text-xs */
    font-weight: 500; /* Tailwind font-medium */
    margin-bottom: 1rem; /* Tailwind mb-4 */
    margin-top: 2rem; /* Tailwind mt-8 */
}

/* Paragraphs */
.content-paragraph p {
    font-size: 1rem; /* Tailwind text-base */
    color: #4a5568; /* Tailwind gray-700 */
    line-height: 1.75; /* Tailwind leading-relaxed */
    margin-bottom: 1rem; /* Add consistent bottom margin */
}

/* Lists */
.content-list ul, .content-list ol {
    list-style-position: inside; /* Keeps bullets/numbers inside padding */
    padding-left: 1.5rem; /* Tailwind pl-6 */
    margin-bottom: 1.5rem; /* Tailwind mb-6 */
    font-size: 1rem; /* Tailwind text-base */
    color: #4a5568; /* Tailwind gray-700 */
}
.content-list ul { list-style-type: disc; }
.content-list ol { list-style-type: decimal; }

.content-list li {
    /* Removed padding-left from here as it's handled by ul/ol padding */
    margin-bottom: 0.5rem; /* Tailwind mb-2, space between list items */
    /* font-size and color inherited from ul/ol */
}

/* Links */
.content-links a {
    color: #2563eb; /* Tailwind blue-600 */
    text-decoration: underline;
}
.content-links a:hover {
    color: #1d4ed8; /* Tailwind blue-700 or your hover color */
    text-decoration: none;
}

</style>