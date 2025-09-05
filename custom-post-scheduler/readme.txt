=== Custom Post Scheduler ===
Contributors: mrdipesh
Tags: Create custom post type, Create custom taxonomies, Automate Post Status, Automation sets post status as scheduled and publish, Automatic sets post status as expired
Requires at least: 4.7
Tested up to: 6.8
Stable tag: 1.0.1
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create custom post type and automate your post between scheduled-published-expired.

== Description ==
Custom Post Scheduler helps you **create custom post types and taxonomies** while also automating post statuses.  
With this plugin, you can easily manage your posts by automatically transitioning them between:

- **Scheduled**
- **Published**
- **Expired**

It simplifies content management by ensuring your posts automatically update their status based on your configuration.

= Features =
* Create unlimited **custom post types**.
* Add and manage **custom taxonomies**.
*- Automate post status transitions:
  - Scheduled → Published → Expired.
* Works seamlessly with WordPress core.
* Lightweight and easy to configure.

= Usage =

* Only Custom post types or Taxonomies created from this plugin will have access to this features.
* Go to **Post Type > Add/Edit > CPS Dates**.
* Provide **Applicable From** to make the post available to public.
* Provide **Expiry Date** to make the post unavailable or displays EXPIRED banner to public.
* Save and let the plugin handle your post transitions automatically.

== Installation ==

From your WordPress dashboard

1. **Visit** Plugins > Add New
2. **Search** for "Custom Post Scheduler"
3. **Install and Activate** Custom Post Scheduler from your Plugins page
4. **Click** on the new menu item "CPS" and create your first custom post type or taxonomy.
5. **Read** the documentation to [get started](https://www.advancedcustomfields.com/resources/getting-started-with-acf/?utm_source=wordpress.org&utm_medium=free%20plugin%20listing&utm_campaign=ACF%20Website)

== Frequently Asked Questions ==

= How to use this plugin?
Go to **Post Type > Add/Edit > CPS Dates**.

= How does the scheduler work? =
The scheduler allows you to automatically publish posts at a specified date and time. 
1. When creating or editing a post, you can set a future date/time for it to be published.
2. The plugin saves this schedule as post meta (or in a custom table for performance).
3. WordPress cron (WP-Cron) checks periodically for posts that are due to be published.
4. When the scheduled time arrives, the plugin changes the post status from 'Scheduled' to 'Published', making it live automatically.
This eliminates the need to manually publish posts and ensures content goes live exactly when you want it.

= What kind of support do you provide? =
We provide support primarily through our GitHub repository. If you encounter any bugs, issues, or have feature requests, please open an issue at:
https://github.com/mrdipesh1/wordpress-plugin
We monitor GitHub regularly and will respond to issues as quickly as possible.

== Changelog ==

= 1.0.1 =
*Release Date 5th September 2025*
- Initial release with custom post type creation.
- Added custom taxonomy support.
- Automated post status: Scheduled → Published → Expired.