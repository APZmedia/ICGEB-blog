<?php if (!is_404()) : ?>
    <footer class="bg-icgeb-blue w-full z-10 min-h-[80px] relative">
        <div class="container mx-auto px-4 py-4">
            <!-- Upper Footer Section -->
            <div class="flex flex-col gap-5 px-6 md:px-16">
                <!-- Title Section -->
                <div class="text-white text-xl md:text-2xl font-bold text-center md:ml-10">
                    Monitoring Gene Drives
                </div>

                <!-- Icons and Descriptions Section -->
                <div class="flex flex-col items-center md:items-center gap-6 md:ml-10">
                    <!-- First Image with Description -->
                    <div class="flex flex-col items-center md:flex-row md:items-center md:gap-5">
                        <p class="text-white text-xs text-center md:text-left md:text-lg mt-2 font-semibold pb-3 md:pb-0">
                            Implemented by
                        </p>
                        <img 
                            src="<?php echo esc_url(get_template_directory_uri() . '/ICGEB-white-logo.png'); ?>" 
                            alt="Monitoring Gene Drive Research Logo" 
                            class="h-auto w-80"
                        />
                    </div>

                    <!-- Second Image with Description -->
                    <div class="flex flex-col items-center md:flex-row md:items-center md:gap-5">
                        <p class="text-white text-xs text-center md:text-left md:text-lg mt-2 lg:whitespace-nowrap font-semibold pb-5 md:pb-0">
                            This project is supported by a grant from Open Philanthropy&apos;s Innovation Policy Program
                        </p>
                        <img 
                            src="<?php echo esc_url(get_template_directory_uri() . '/OP_GrayLogo+BlackType.png'); ?>" 
                            alt="Open Philanthropy Logo" 
                            class="h-auto w-28"
                        />
                    </div>
                </div>
            </div>


            <!-- Bottom Footer Section -->
            <div class="mt-6 border-t border-[#4E95CA] pt-4">
                <div class="flex flex-wrap justify-center items-center space-x-4 text-[#4E95CA] text-sm">
                    <div class="flex items-center space-x-2">
                        <span>©</span>
                        <span id="currentYear"></span>
                        <span>ICGEB</span>
                    </div>

                    <div class="hidden sm:block border-l border-[#4E95CA] h-5 mx-2"></div>
                    
                    <a href="<?php bloginfo('rss2_url'); ?>" class="hover:text-icgeb-hover transition-colors">
                        RSS
                    </a>


                    <div class="hidden sm:block border-l border-[#4E95CA] h-5 mx-2"></div>

                    <a href="/privacy-policy" class="hover:text-icgeb-hover transition-colors">
                        Privacy Policy
                    </a>

                    <div class="hidden sm:block border-l border-[#4E95CA] h-5 mx-2"></div>

                    <div>Designed by APZmedia</div>
                </div>
            </div>
        </div>
    </footer>
<?php endif; ?>

<?php wp_footer(); ?>

<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NLTD79WT" height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->

<script>
    // Set current year dynamically
    document.getElementById('currentYear').textContent = new Date().getFullYear();
</script>

</body>
</html>
