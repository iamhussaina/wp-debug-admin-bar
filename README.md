# Admin Bar Debug for WordPress

A WordPress module to add essential debug information (template file, conditional tags, main query) to the admin bar for easy frontend debugging.

This module is intended to be bundled with a theme. It is visible only to users with the `manage_options` capability (typically Administrators).

## Features

* **Current Template:** Displays the name of the template file currently rendering the page (e.g., `page.php`, `single.php`).
* **Active Conditionals:** Lists all standard WordPress conditional tags that evaluate to `true` for the current view (e.g., `is_page()`, `is_singular()`).
* **Main Query Vars:** Dumps all non-empty public query variables from the global `$wp_query` object, showing what WordPress is querying for.

## Installation

This module is designed to be included directly within a WordPress theme.

1.  **Copy the Folder:**
    Download or clone this repository. Place the entire `wp-debug-admin-bar` folder into your active theme's directory.

    The structure should look like this:
    ```
    /wp-content/themes/your-theme/
    ├── wp-debug-admin-bar/
    │   ├── class-hussainas-admin-bar-debugger.php
    │   └── debug-admin-bar.php
    ├── functions.php
    └── ...
    ```

2.  **Include in `functions.php`:**
    Open your theme's `functions.php` file and add the following line to load the module:

    ```php
    // Load the Admin Bar Debug Module
    require_once( get_template_directory() . '/wp-debug-admin-bar/debug-admin-bar.php' );
    ```

That's it. Once included, the "Hussainas Debug" menu will automatically appear in the admin bar for Administrators when viewing the site.

## How It Works

The module uses an OOP approach to prevent conflicts.

1.  `debug-admin-bar.php` acts as the loader. It includes the main class and hooks into the WordPress `init` action.
2.  `class-hussainas-admin-bar-debugger.php` contains all the logic.
    * It uses a Singleton pattern (`init()` method) to ensure it's only loaded once.
    * The constructor hooks into `admin_bar_menu` with a late priority (999).
    * The main callback `hussainas_add_debug_menu` first checks for `manage_options` capability.
    * It then adds the parent menu and calls private helper methods (`hussainas_add_template_node`, `hussainas_add_conditionals_node`, etc.) to build the sub-menus.
    * Global objects like `$template` and `$wp_query` are used to source the debug information.
