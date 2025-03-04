<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-NLTD79WT');
    </script>
    <!-- End Google Tag Manager -->
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php wp_title('|', true, 'right'); ?><?php bloginfo('name'); ?></title> 
    <?php wp_head(); ?>
</head>
<body <?php body_class('bg-white'); ?>>

    <!-- Responsive Header -->
    <header class="bg-icgeb-blue top-0 left-0 w-full z-50 flex flex-col pb-4 justify-end 
        <?php if (is_front_page() || is_home()) : ?> 
            min-h-[200px] md:min-h-[250px] 
        <?php else : ?>
            min-h-[120px] md:min-h-[150px]
        <?php endif; ?>">

        <div class="container mx-auto px-4 flex flex-col h-full relative">
            <!-- Title and Logo -->
            <div class="flex items-center space-x-4">
                <img 
                    src="<?php echo esc_url(get_template_directory_uri() . '/monitoring_gen_drive_research._vactor_file_for_web2.png'); ?>" 
                    alt="Monitoring Gene Drive Research Logo" 
                    class="object-cover
                    <?php if (is_front_page() || is_home()) : ?> 
                        h-[100px] w-[100px] md:h-[250px] md:w-[250px]
                    <?php else : ?>
                        h-[100px] h-[100px]
                    <?php endif; ?>"
                />
                <h1 class="text-white font-bold text-left transition-all 
                    <?php if (is_front_page() || is_home()) : ?>
                        text-4xl md:text-5xl lg:text-6xl pt-8 pb-8 md:pb-16
                    <?php else : ?>
                        text-2xl md:text-3xl lg:text-4xl pt-4 pb-4 md:pb-8
                    <?php endif; ?>">
                    Monitoring Gene Drives
                </h1>
            </div>

            <!-- Mobile Navbar Toggle Button -->
            <button id="mobileNavToggle" class="fixed top-16 right-4 bg-white text-icgeb-blue w-10 h-10 p-2 rounded-md shadow-md focus:outline-none z-50 md:hidden flex items-center justify-center"
                aria-label="Toggle Navigation">
                <svg id="menuIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
                <svg id="closeIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Navigation Menu -->
            <nav id="mainNav" 
                class="hidden fixed inset-0 bg-icgeb-blue text-white flex flex-col justify-center items-center gap-8 z-40 transform transition-transform duration-300 translate-x-full md:translate-x-0 md:static md:flex-row md:justify-start md:items-center md:gap-6">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary-menu', // Use the registered menu location
                    'container' => false,              // Remove the <div> wrapper
                    'menu_class' => 'flex flex-col md:flex-row justify-center items-center space-y-4 md:space-y-0 md:space-x-8 text-lg uppercase font-medium', // Tailwind styling for the <ul>
                    'fallback_cb' => false,            // Disable fallback to pages if no menu is set
                    'depth' => 1,                      // Limit depth to 1 for a simple navbar
                ));
                ?>
            </nav>
        </div>
    </header>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'icgeb-blue': '#0066b3',
                        'icgeb-hover': '#6EC1FF'
                    }           
                }
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleButton = document.getElementById('mobileNavToggle');
            const mainNav = document.getElementById('mainNav');
            const menuIcon = document.getElementById('menuIcon');
            const closeIcon = document.getElementById('closeIcon');

            // Toggle mobile menu
            toggleButton.addEventListener('click', function () {
                const isNavOpen = mainNav.classList.contains('translate-x-0');
                
                if (isNavOpen) {
                    mainNav.classList.remove('translate-x-0');
                    mainNav.classList.add('translate-x-full');
                    menuIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                } else {
                    mainNav.classList.remove('translate-x-full', 'hidden');
                    mainNav.classList.add('translate-x-0');
                    menuIcon.classList.add('hidden');
                    closeIcon.classList.remove('hidden');
                }
            });

            // Ensure menu visibility for medium/large devices
            const handleResize = () => {
                if (window.innerWidth >= 768) {
                    mainNav.classList.remove('hidden', 'translate-x-full');
                    mainNav.classList.add('translate-x-0');
                    menuIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                } else {
                    mainNav.classList.add('hidden', 'translate-x-full');
                    menuIcon.classList.remove('hidden');
                    closeIcon.classList.add('hidden');
                }
            };

            window.addEventListener('resize', handleResize);
            handleResize(); // Check on page load
        });
    </script>
