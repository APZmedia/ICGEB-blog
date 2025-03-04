<?php
/**
 * Plugin Name: My PDF Download Plugin
 * Description: Generates a PDF of current post content using dompdf (without Composer).
 * Version: 1.7
 * Author: APZmedia
 */

// 1. Load dompdf manually
require_once __DIR__ . '/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * 2. When ?download_pdf=1&post_id=..., generate a PDF for that post
 */
function my_pdf_download_handler() {
    // Only proceed if our URL parameters are present
    if ( isset($_GET['download_pdf']) && '1' === $_GET['download_pdf'] && ! empty($_GET['post_id']) ) {
        
        $post_id = absint($_GET['post_id']);
        $post    = get_post($post_id);
        if ( ! $post ) {
            wp_die('Invalid post.');
        }

        // Gather post info
        $author_id        = $post->post_author;
        $author_firstname = get_the_author_meta('first_name', $author_id) ?: 'FirstName';
        $author_lastname  = get_the_author_meta('last_name',  $author_id) ?: 'LastName';
        $author_fullname  = $author_firstname . ' ' . $author_lastname;
        $publication_date = get_the_date('F j, Y', $post);
        $title            = $post->post_title;
        $url              = trailingslashit(get_permalink($post_id)) . 'release/1/';

        // Get the content (allowing images, shortcodes, etc.)
        $content = apply_filters('the_content', $post->post_content);

        // Limit images to 350px wide and center them
        $content = preg_replace(
            '/<img([^>]+)>/i',
            '<img$1 style="max-width:350px; height:auto; display:block; margin:auto;">',
            $content
        );

        // 3. Construct the PDF filename without truncating
        // Remove only the few forbidden characters for Windows & cross-platform
        $filename = $author_lastname . ', ' . $author_firstname . ' (' . $publication_date . '). ' . $title . '.pdf';
        // Strip out invalid filename characters: < > : " / \ | ? *
        $filename = preg_replace('/[<>:"\/\\\\|?*]/', '', $filename);
        // Remove control chars
        $filename = preg_replace('/[\x00-\x1F]/', '', $filename);
        // Trim stray dots/spaces
        $filename = trim($filename, ". ");

        // 4. Setup dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // allow remote images
        $dompdf = new Dompdf($options);

        // 5. Build the HTML

        // (A) Cover Page: fully centered, large fonts
        // First page: occupies full page, then break
        $html = '
        <div style="text-align: center; font-family: Arial, sans-serif; height:100vh; 
                    display:flex; flex-direction:column; justify-content:center; align-items:center; 
                    padding:50px; margin:0;">
            <h1 style="color:#0066B3; font-size:48px; font-weight:900; margin-bottom:20px;">'
                . esc_html($title) . 
            '</h1>
            <h2 style="font-size:28px; color:#333; font-weight:700; margin-bottom:30px;">
                Monitoring Gene Drives
            </h2>
            <p style="font-size:18px; color:#555; margin-bottom:10px;">
                <strong>Published on:</strong> ' . esc_html($publication_date) . '
            </p>
            <p style="font-size:18px; color:#555; margin-bottom:10px;">
                <strong>Author:</strong> ' . esc_html($author_fullname) . '
            </p>
            <p style="font-size:18px; color:#555; margin-bottom:10px;">
                <strong>URL:</strong> 
                <a href="' . esc_url($url) . '" style="color:#0066B3;">' . esc_html($url) . '</a>
            </p>
        </div>
        <div style="page-break-after:always;"></div>';

        // (B) Content Pages
        // We'll add some margin for readability
        $html .= '
        <div style="font-family: Arial, sans-serif; font-size:14px; line-height:1.6; color:#333; margin:40px;">
            ' . $content . '
        </div>';

        // 6. Insert script for custom headers & footers (from p.2 onward)
        $html .= '
        <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->get_font("Arial", "normal");

            // For every page except the first, place header & footer
            if ($PAGE_NUM > 1) {
                // HEADER: Post Title (left), Monitoring Gene Drives (right)
                $pdf->page_text(50, 40, "' . addslashes($title) . '", $font, 10, [0,0,0]);
                $pdf->page_text(450, 40, "Monitoring Gene Drives", $font, 10, [0,0,0]);

                // FOOTER: horizontal line + page number
                // Draw a line from x=50 to x=550 at y=810
                $pdf->line(50, 810, 550, 810, [0,0,0], 1);
                // Show "Page X of Y" at y=820, centered horizontally
                $pdf->page_text(270, 820, "Page {PAGE_NUM} of {PAGE_COUNT}", $font, 10, [0,0,0]);
            }
        }
        </script>
        ';

        // 7. Load into dompdf, set paper, render
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // 8. Output PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $dompdf->output();
        exit;
    }
}
add_action('init', 'my_pdf_download_handler');
