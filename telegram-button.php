<?php
/*
Plugin Name: Telegram Button
Description: Adds a floating Telegram button to the site. Admins can set the username from settings.
Version: 1.0
Author: Sasha Zimin
Author URI: https://zimin.dev
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: telegram-button-zimindev
Domain Path: /languages
*/

// Prevent direct access to the file for security
if (!defined('ABSPATH')) exit;

// Register the admin settings menu under "Settings" in WordPress dashboard
function tb_register_settings_menu() {
    add_options_page(
        'Telegram Button Settings',  // Page title
        'Telegram Button',           // Menu title
        'manage_options',            // Capability required
        'telegram-button',           // Menu slug
        'tb_settings_page'           // Callback function
    );
}
add_action('admin_menu', 'tb_register_settings_menu');

// Output the HTML for the Telegram Button settings page
function tb_settings_page() {
    ?>
    <div class="wrap">
        <h1>Telegram Button Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('tb_settings_group');
            do_settings_sections('tb_settings_group');
            ?>
            <table class="form-table">
                <tr valign="top">
                <th scope="row">Telegram Username</th>
                <td>
                    <input 
                        type="text" 
                        name="tb_username" 
                        value="<?php echo esc_attr(get_option('tb_username')); ?>" 
                        placeholder="e.g. yourusername (without @)" 
                    />
                    <p class="description">Enter your Telegram username without the @ symbol</p>
                </td>
                </tr>
                <tr valign="top">
                <th scope="row">Button Position</th>
                <td>
                    <select name="tb_button_position">
                        <option value="right" <?php selected(get_option('tb_button_position'), 'right'); ?>>Right</option>
                        <option value="left" <?php selected(get_option('tb_button_position'), 'left'); ?>>Left</option>
                    </select>
                </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register the settings in WordPress
function tb_register_settings() {
    register_setting(
        'tb_settings_group',
        'tb_username',
        'sanitize_text_field'
    );
    register_setting(
        'tb_settings_group',
        'tb_button_position'
    );
}
add_action('admin_init', 'tb_register_settings');

// Add the floating Telegram button to the frontend footer
function tb_add_telegram_button() {
    $username = sanitize_text_field(get_option('tb_username'));
    $position = get_option('tb_button_position', 'right');
    
    if (!$username) return;

    $position_style = $position === 'left' ? 'left: 20px; right: auto;' : 'right: 20px; left: auto;';
    
    echo '
    <a href="https://t.me/' . esc_attr($username) . '" target="_blank" class="tb-float" title="Message on Telegram">
        <img src="https://img.icons8.com/color/48/000000/telegram-app--v1.png" alt="Telegram">
    </a>
    <style>
        .tb-float {
            position: fixed;
            width: 60px;
            height: 60px;
            bottom: 20px;
            ' . $position_style . '
            background-color: #0088cc;
            color: #FFF;
            border-radius: 50px;
            text-align: center;
            font-size: 30px;
            box-shadow: 2px 2px 3px #999;
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
        }
        .tb-float:hover {
            transform: scale(1.1);
            background-color: #0077b5;
        }
        .tb-float img {
            width: 32px;
            height: 32px;
        }
    </style>
    ';
}
add_action('wp_footer', 'tb_add_telegram_button');