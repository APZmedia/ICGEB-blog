<?php if (!is_404()) : ?>
    <footer class="bg-icgeb-blue w-full z-50 min-h-[80px] relative">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="text-white text-md lg:text-2xl font-bold">Monitoring Gene Drives</div>
                <div class="flex space-x-6">
                    <a href="/contact" class="text-white hover:text-icgeb-hover transition-colors">Contact</a>
                    <a href="<?php bloginfo('rss2_url'); ?>" class="text-white hover:text-icgeb-hover transition-colors">RSS</a>
                    <a href="/legal" class="text-white hover:text-icgeb-hover transition-colors">Legal</a>
                    <a href="mailto:<?php echo get_option('admin_email'); ?>" class="text-white hover:text-icgeb-hover transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </a>
                </div>
            </div>
            <div class="text-center text-[#4E95CA] text-xl mt-2">Designed by APZmedia</div>
        </div>
    </footer>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
