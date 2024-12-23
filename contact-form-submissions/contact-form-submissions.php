<?php
/*
Plugin Name: Contact Form Submissions
Description: Handles contact form submissions and displays them in the admin panel.
Version: 1.1
Author: APZmedia
*/

// Existing code for table creation and form submission handling...

// Add admin menu
function contact_form_submissions_menu() {
    add_menu_page(
        'Contact Form Submissions',
        'Contact Submissions',
        'manage_options',
        'contact-form-submissions',
        'display_contact_form_submissions',
        'dashicons-email-alt',
        30
    );
}
add_action('admin_menu', 'contact_form_submissions_menu');

// Display submissions in admin panel
function display_contact_form_submissions() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_form_submissions';
    $submissions = $wpdb->get_results("SELECT * FROM $table_name ORDER BY time DESC");

    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Contact Form Submissions</h1>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <div class="postbox">
                            <div class="inside">
                                <table class="wp-list-table widefat fixed striped submissions-table">
                                    <thead>
                                        <tr>
                                            <th class="manage-column">Time</th>
                                            <th class="manage-column">Name</th>
                                            <th class="manage-column">Email</th>
                                            <th class="manage-column">Message</th>
                                            <th class="manage-column">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($submissions as $submission) : ?>
                                            <tr id="submission-<?php echo $submission->id; ?>">
                                                <td><?php echo esc_html($submission->time); ?></td>
                                                <td><?php echo esc_html($submission->name); ?></td>
                                                <td><?php echo esc_html($submission->email); ?></td>
                                                <td><?php echo esc_html($submission->message); ?></td>
                                                <td>
                                                    <button class="button button-small delete-submission" data-id="<?php echo $submission->id; ?>">
                                                        Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .submissions-table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }
        .submissions-table th,
        .submissions-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        .submissions-table th {
            background-color: #f8fafc;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }
        .submissions-table tr:hover {
            background-color: #f1f5f9;
        }
        .delete-submission {
            background-color: #ef4444 !important;
            color: white !important;
            border: none !important;
            padding: 4px 8px !important;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .delete-submission:hover {
            background-color: #dc2626 !important;
        }
    </style>

    <script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.delete-submission').on('click', function() {
            var button = $(this);
            var submissionId = button.data('id');
            
            if (confirm('Are you sure you want to delete this submission?')) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'delete_submission',
                        submission_id: submissionId,
                        security: '<?php echo wp_create_nonce("delete_submission_nonce"); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#submission-' + submissionId).fadeOut(300, function() { $(this).remove(); });
                        } else {
                            alert('Error deleting submission. Please try again.');
                        }
                    },
                    error: function() {
                        alert('Error deleting submission. Please try again.');
                    }
                });
            }
        });
    });
    </script>
    <?php
}

// Handle submission deletion
function delete_submission() {
    check_ajax_referer('delete_submission_nonce', 'security');

    if (!current_user_can('manage_options')) {
        wp_send_json_error('You do not have permission to delete submissions.');
        return;
    }

    $submission_id = intval($_POST['submission_id']);

    global $wpdb;
    $table_name = $wpdb->prefix . 'contact_form_submissions';

    $result = $wpdb->delete(
        $table_name,
        array('id' => $submission_id),
        array('%d')
    );

    if ($result) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Failed to delete the submission.');
    }
}
add_action('wp_ajax_delete_submission', 'delete_submission');

