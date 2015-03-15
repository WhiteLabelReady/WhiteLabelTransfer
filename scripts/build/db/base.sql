-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 18, 2013 at 03:39 AM
-- Server version: 5.1.69
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `wtc_demo`
--

-- --------------------------------------------------------

--
-- Table structure for table `wetransfer_banned_email`
--

CREATE TABLE IF NOT EXISTS `wetransfer_banned_email` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` text COLLATE utf8_unicode_ci NOT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `wetransfer_banned_ip`
--

CREATE TABLE IF NOT EXISTS `wetransfer_banned_ip` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'IP or IP range',
  `comment` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `wetransfer_languages`
--

CREATE TABLE IF NOT EXISTS `wetransfer_languages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `iso_3166_1` varchar(5) COLLATE utf8_unicode_ci NOT NULL COMMENT 'http://en.wikipedia.org/wiki/ISO_3166-1',
  `iso_639` varchar(2) COLLATE utf8_unicode_ci NOT NULL COMMENT 'locale',
  `friendly_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `native_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `wetransfer_languages`
--

INSERT INTO `wetransfer_languages` (`id`, `iso_3166_1`, `iso_639`, `friendly_name`, `native_name`) VALUES
(1, 'en-us', 'en', 'English (U.S.)', 'English'),
(2, 'de-de', 'de', 'German', 'Deutsch');

-- --------------------------------------------------------

--
-- Table structure for table `wetransfer_logs_download`
--

CREATE TABLE IF NOT EXISTS `wetransfer_logs_download` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `upload_id` int(10) unsigned NOT NULL,
  `recipient_id` int(10) unsigned DEFAULT NULL,
  `date` int(10) unsigned NOT NULL,
  `ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `upload_id` (`upload_id`),
  KEY `recipient_id` (`recipient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `wetransfer_phrases`
--

CREATE TABLE IF NOT EXISTS `wetransfer_phrases` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `language_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `text` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `language_id_2` (`language_id`,`name`),
  KEY `language_id` (`language_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=79 ;

--
-- Dumping data for table `wetransfer_phrases`
--

INSERT INTO `wetransfer_phrases` (`id`, `language_id`, `name`, `text`) VALUES
(1, 1, 'language', 'Language'),
(2, 2, 'language', 'Sprache'),
(5, 1, 'upload', 'Upload'),
(6, 2, 'upload', 'Hochladen'),
(7, 1, 'add_files', 'Add Files'),
(8, 2, 'add_files', 'Dateien hinzufügen'),
(9, 1, 'to', 'To'),
(10, 2, 'to', 'An'),
(13, 1, 'from', 'From'),
(14, 2, 'from', 'Von'),
(15, 1, 'your_email', 'Your e-mail'),
(16, 2, 'your_email', 'Ihre E-Mail'),
(17, 1, 'your_friend_email', 'Your friend''s e-mail'),
(18, 2, 'your_friend_email', 'Email des Freundes'),
(19, 1, 'message', 'Message'),
(20, 2, 'message', 'Nachricht'),
(21, 1, 'transfer', 'Transfer'),
(22, 2, 'transfer', 'Übertragen'),
(23, 1, 'file_upload_success', 'YEAH! Your files were successfully uploaded!'),
(24, 2, 'file_upload_success', 'Ja! Ihre Dateien wurden erfolgreich hochgeladen!'),
(25, 1, 'http_error_404_text', 'The document that you have requested does not exist'),
(26, 2, 'http_error_404_text', 'Das Dokument, das Sie angefordert haben existiert nicht'),
(27, 1, 'site_default_admin_preloader_image_path', 'Preloader Image Path (URL)'),
(28, 1, 'site_allow_language_change', 'Allow Language Change'),
(29, 1, 'site_allow_template_change', 'Allow Template Change'),
(30, 1, 'site_allowed_file_types', 'Allowed File Types'),
(31, 1, 'site_archive_prefix', 'Archive Prefix'),
(32, 1, 'site_default_avatar_url', 'Default Avatar URL'),
(33, 1, 'admin', 'Admin'),
(34, 1, 'global', 'Global'),
(35, 1, 'site_default_landing_page', 'Default Landing Page (After Login)'),
(36, 1, 'site_default_language', 'Default Site Language'),
(37, 1, 'site_default_max_upload_size', 'Max Upload Size'),
(38, 1, 'site_default_preloader_image_path', 'Site Preloader Image'),
(39, 1, 'site_default_template', 'Default Site Template'),
(40, 1, 'site_email_address', 'Site e-mail Address'),
(41, 1, 'site_guest_max_file_size', 'Max File Size for Guests'),
(42, 1, 'site_guest_max_queue_size', 'Queue Size Limit for Guests'),
(43, 1, 'site_guest_max_recipients', 'Max Number of Recipients for Guests'),
(44, 1, 'site_guest_total_file_size', 'Total Queue Size for Guests'),
(45, 1, 'site_guest_upload_retention', 'Guest Upload Retention'),
(46, 1, 'site_local_theme_url_root', 'Relative URL path of themes'),
(47, 1, 'site_moderate_new_users', 'Moderate New Users'),
(48, 1, 'site_name', 'Site Name'),
(49, 1, 'user', 'User'),
(50, 1, 'site_require_email_confirm', 'Require e-mail Confirmation after User Registration'),
(51, 1, 'site_upload_dir_users', 'Path of upload directory for end-users'),
(52, 1, 'site_upload_dir', 'Path of upload directory '),
(53, 1, 'site_token_max_length', 'Max Token Length'),
(54, 1, 'site_token_min_length', 'Minimum token length'),
(55, 1, 'site_settings', 'Site Settings'),
(56, 2, 'site_settings', 'Site-Einstellungen'),
(57, 2, 'user', 'Benutzer'),
(58, 2, 'settings', 'Einstellungen'),
(59, 2, 'site', 'Webseite'),
(60, 2, 'phrases', 'Sätze'),
(61, 2, 'users', 'Benutzer'),
(62, 2, 'all', 'alle'),
(63, 2, 'logoff', 'Abmelden'),
(64, 2, 'account_settings', 'Konto-Einstellungen'),
(65, 2, 'files', 'Dateien'),
(66, 1, 'template_color', 'Template Color'),
(67, 2, 'template_color', 'Schablone Farbe'),
(68, 1, 'save_changes', 'Save Changes'),
(69, 1, 'cancel', 'Cancel'),
(70, 2, 'save_changes', 'Änderungen speichern'),
(71, 2, 'cancel', 'Abbrechen'),
(72, 2, 'site_phrases', 'Website-Sätze'),
(73, 1, 'site_phrases', 'Site Phrases'),
(74, 1, 'logoff', 'Logout'),
(75, 1, 'uploaded_files', 'Uploaded Files'),
(76, 2, 'uploaded_files', 'Hochgeladene Dateien'),
(77, 1, 'orphaned_files', 'Orphaned Files'),
(78, 2, 'orphaned_files', 'Verwaiste Dateien');

-- --------------------------------------------------------

--
-- Table structure for table `wetransfer_recipients`
--

CREATE TABLE IF NOT EXISTS `wetransfer_recipients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL,
  `custom_token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `custom_token` (`custom_token`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `wetransfer_site_config`
--

CREATE TABLE IF NOT EXISTS `wetransfer_site_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `possible_values` text COLLATE utf8_unicode_ci,
  `category` enum('admin','global','upload','user') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'global',
  `ui_type` enum('radio','select','text','textarea') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'text',
  `hint` text COLLATE utf8_unicode_ci COMMENT 'hint to present in the UI',
  `comment` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `category` (`category`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=26 ;

--
-- Dumping data for table `wetransfer_site_config`
--

INSERT INTO `wetransfer_site_config` (`id`, `name`, `value`, `possible_values`, `category`, `ui_type`, `hint`, `comment`) VALUES
(1, 'site_name', 'White Label Transfer', NULL, 'global', 'text', 'Site Name', NULL),
(2, 'site_default_preloader_image_path', '__BASEURL__/images/preloader/486.gif', NULL, 'global', 'text', NULL, NULL),
(3, 'site_default_landing_page', '', NULL, 'global', 'text', 'Local URL to redirect to after login, do not include the scheme, i.e. http://', NULL),
(4, 'site_allow_template_change', '1', '0,1', 'global', 'radio', 'Allow Template Change', NULL),
(5, 'site_default_template', 'delta', NULL, 'global', 'text', NULL, NULL),
(6, 'site_default_max_upload_size', '1024', NULL, 'upload', 'text', 'Max upload size in bytes', 'in MB'),
(7, 'site_allowed_file_types', '*', NULL, 'upload', 'text', 'File types allowed for upload. Separate entries with commas. Use * to allow all.', NULL),
(8, 'site_default_avatar_url', '__BASEURL__/images/profiles/anonymousUser.jpg', NULL, 'user', 'text', 'Full URL of the default user avatar', NULL),
(9, 'site_default_language', 'en-us', NULL, 'global', 'text', NULL, NULL),
(10, 'site_guest_max_recipients', '3', NULL, 'upload', 'text', NULL, NULL),
(11, 'site_guest_max_queue_size', '5', NULL, 'upload', 'text', NULL, NULL),
(12, 'site_guest_max_file_size', '2', NULL, 'upload', 'text', NULL, 'in MB'),
(13, 'site_guest_total_file_size', '10', NULL, 'upload', 'text', NULL, 'in MB'),
(14, 'site_allow_language_change', '1', '0,1', 'global', 'radio', 'Allow Language Change', NULL),
(15, 'site_guest_upload_retention', '168', NULL, 'upload', 'text', NULL, 'in hours'),
(16, 'site_moderate_new_users', '1', '0,1', 'user', 'radio', NULL, NULL),
(17, 'site_require_email_confirm', '1', '0,1', 'user', 'radio', 'Require e-mail confirmation for new user registrations', NULL),
(18, 'site_email_address', 'admin@whitelabeltransfer.com', NULL, 'global', 'text', NULL, NULL),
(19, 'site_upload_dir', 'data/uploads', NULL, 'upload', 'text', 'Relative path of the local file upload directory', NULL),
(20, 'site_upload_dir_users', 'data/uploads/users', NULL, 'upload', 'text', 'Relative path of the local file upload directory for end-users', NULL),
(21, 'site_archive_prefix', 'whitelabeltransfer-', NULL, 'upload', 'text', 'All uploads files will be archived with this prefix', NULL),
(22, 'site_token_min_length', '8', NULL, 'upload', 'text', 'Tokens match users to available files', NULL),
(23, 'site_token_max_length', '15', NULL, 'upload', 'text', 'Tokens match users to available files', NULL),
(24, 'site_local_theme_url_root', 'css/jquery-ui/themes', NULL, 'global', 'text', 'Theme root relative to the site. Do not include host or scheme.', 'relative to the site root'),
(25, 'site_default_admin_preloader_image_path', 'images/preloader/477-black-124x128.gif', NULL, 'admin', 'text', 'Local URL of the preloader image for the site admin UI. Do not include the protocol scheme.', NULL),
(26, 'site_zip_via_bash', '1', '0,1', 'upload', 'radio', 'Zip files via bash', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wetransfer_site_permissions`
--

CREATE TABLE IF NOT EXISTS `wetransfer_site_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `permission_type` enum('admin','site','upload','user') COLLATE utf8_unicode_ci DEFAULT 'site',
  `comment` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permission_name` (`permission_name`,`permission_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

--
-- Dumping data for table `wetransfer_site_permissions`
--

INSERT INTO `wetransfer_site_permissions` (`id`, `permission_name`, `permission_type`, `comment`) VALUES
(1, 'can_view_site', 'site', NULL),
(2, 'can_upload', 'upload', NULL),
(3, 'can_admin_site', 'admin', NULL),
(4, 'can_view_debug_messages', 'admin', NULL),
(5, 'can_view_user_profiles', 'site', NULL),
(6, 'can_edit_own_profile', 'user', NULL),
(7, 'can_change_own_password', 'user', NULL),
(8, 'can_delete_own_account', 'user', NULL),
(9, 'can_admin_site_phrases', 'admin', NULL),
(10, 'can_admin_users', 'admin', NULL),
(11, 'can_admin_files', 'admin', 'Can Administer Uploaded Files (Admin)');

-- --------------------------------------------------------

--
-- Table structure for table `wetransfer_site_themes`
--

CREATE TABLE IF NOT EXISTS `wetransfer_site_themes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('custom','bootstrap','jquery-ui') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'custom',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `display_name` (`display_name`),
  KEY `active` (`active`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=78 ;

--
-- Dumping data for table `wetransfer_site_themes`
--

INSERT INTO `wetransfer_site_themes` (`id`, `type`, `name`, `display_name`, `active`) VALUES
(2, 'jquery-ui', 'ui-lightness', 'UI Lightness', '1'),
(3, 'jquery-ui', 'ui-darkness', 'UI Darkness', '1'),
(4, 'jquery-ui', 'smoothness', 'Smoothness', '1'),
(5, 'jquery-ui', 'start', 'Start', '1'),
(6, 'jquery-ui', 'redmond', 'Redmond', '1'),
(7, 'jquery-ui', 'sunny', 'Sunny', '1'),
(8, 'jquery-ui', 'bootstrap', 'Bootstrap', '1'),
(9, 'jquery-ui', 'delta', 'Delta', '1'),
(10, 'jquery-ui', 'selene', 'Selene', '1'),
(11, 'jquery-ui', 'aristo', 'Aristo', '1'),
(12, 'jquery-ui', 'absolution', 'Absolution', '1'),
(13, 'jquery-ui', 'arctic', 'Arctic', '1'),
(14, 'jquery-ui', 'gold', 'All Gold Everything', '1'),
(22, 'jquery-ui', 'afterdark', 'Afterdark', '1'),
(23, 'jquery-ui', 'afternoon', 'Afternoon', '1'),
(24, 'jquery-ui', 'afterwork', 'Afterwork', '1'),
(25, 'jquery-ui', 'base', 'Base', '1'),
(26, 'jquery-ui', 'black-tie', 'Black Tie', '1'),
(27, 'jquery-ui', 'bliss', 'Bliss', '1'),
(28, 'jquery-ui', 'blitzer', 'Blitzer', '1'),
(30, 'jquery-ui', 'cobalt', 'Cobalt', '1'),
(31, 'jquery-ui', 'cocoa', 'Cocoa', '1'),
(32, 'jquery-ui', 'cupertino', 'Cupertino', '1'),
(33, 'jquery-ui', 'dark-hive', 'Dark Hive', '1'),
(35, 'jquery-ui', 'dot-luv', 'Dot Luv', '1'),
(36, 'jquery-ui', 'eggplant', 'Eggplant', '1'),
(37, 'jquery-ui', 'excite-bike', 'Excite Bike', '1'),
(38, 'jquery-ui', 'facebook', 'Facebook', '1'),
(39, 'jquery-ui', 'flat-ui', 'Flat UI', '1'),
(40, 'jquery-ui', 'flick', 'Flick', '1'),
(41, 'jquery-ui', 'fluent', 'Fluent', '1'),
(42, 'jquery-ui', 'grayified', 'Grayified', '1'),
(43, 'jquery-ui', 'hot-sneaks', 'Hot Sneaks', '1'),
(44, 'jquery-ui', 'humanity', 'Humanity', '1'),
(45, 'jquery-ui', 'jface', 'jFace', '1'),
(46, 'jquery-ui', 'jflick', 'jFlick', '1'),
(47, 'jquery-ui', 'jmango', 'jMango', '1'),
(48, 'jquery-ui', 'jmetro', 'jMetro', '1'),
(49, 'jquery-ui', 'jwin8', 'jWin8', '1'),
(50, 'jquery-ui', 'le-frog', 'Le Frog', '1'),
(51, 'jquery-ui', 'midnight', 'Midnight', '1'),
(52, 'jquery-ui', 'mint-choc', 'Mint Choc', '1'),
(53, 'jquery-ui', 'modern-web', 'Modern Web', '1'),
(55, 'jquery-ui', 'overcast', 'Overcast', '1'),
(56, 'jquery-ui', 'pepper-grinder', 'Pepper Grinder', '1'),
(64, 'jquery-ui', 'rocket', 'Rocket', '1'),
(65, 'jquery-ui', 'roundcube-classic', 'Roundcube Classic', '1'),
(66, 'jquery-ui', 'roundcube-larry', 'Roundcube Larry', '1'),
(68, 'jquery-ui', 'sterling', 'Sterling', '1'),
(70, 'jquery-ui', 'south-street', 'South Street', '1'),
(72, 'jquery-ui', 'swanky-purse', 'Swanky Purse', '1'),
(73, 'jquery-ui', 'swiss-ui', 'Swiss UI', '1'),
(74, 'jquery-ui', 'trontastic', 'Trontastic', '1'),
(77, 'jquery-ui', 'vader', 'Vader', '1');

-- --------------------------------------------------------

--
-- Table structure for table `wetransfer_uploaded_files`
--

CREATE TABLE IF NOT EXISTS `wetransfer_uploaded_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL,
  `file_path` text COLLATE utf8_unicode_ci NOT NULL,
  `url_path` text COLLATE utf8_unicode_ci,
  `file_size` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `wetransfer_uploads`
--

CREATE TABLE IF NOT EXISTS `wetransfer_uploads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `uploader` int(11) unsigned DEFAULT NULL,
  `direct_url` text COLLATE utf8_unicode_ci,
  `file_path` text COLLATE utf8_unicode_ci COMMENT 'absolute file path',
  `local` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1' COMMENT 'locally hosted?',
  `total_file_size` int(10) unsigned NOT NULL,
  `upload_date` int(10) unsigned NOT NULL,
  `expiration_date` int(10) unsigned DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  `uploader_ip` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `token` (`token`),
  KEY `uploader` (`uploader`),
  KEY `uploader_ip` (`uploader_ip`),
  KEY `local` (`local`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `wetransfer_usergroups`
--

CREATE TABLE IF NOT EXISTS `wetransfer_usergroups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `comment` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `wetransfer_usergroups`
--

INSERT INTO `wetransfer_usergroups` (`id`, `name`, `title`, `comment`) VALUES
(0, 'Banned', 'Banned', 'Banned Users'),
(1, 'Guest', 'Unauthenticated', 'Guests'),
(2, 'User', 'Normal Users', 'Normal Users'),
(3, 'Administrator', 'Site Administrators', 'Site Administrators'),
(4, 'Demo', 'Demo Users', 'Demo Users');

-- --------------------------------------------------------

--
-- Table structure for table `wetransfer_usergroup_members`
--

CREATE TABLE IF NOT EXISTS `wetransfer_usergroup_members` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `usergroup_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id_and_usergroup_id` (`user_id`,`usergroup_id`),
  KEY `user_id` (`user_id`),
  KEY `usergroup_id` (`usergroup_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `wetransfer_usergroup_members`
--

INSERT INTO `wetransfer_usergroup_members` (`id`, `user_id`, `usergroup_id`) VALUES
(1, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `wetransfer_usergroup_permissions`
--

CREATE TABLE IF NOT EXISTS `wetransfer_usergroup_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usergroup_id` int(10) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usergroup_id_and_permissions_id` (`usergroup_id`,`permission_id`),
  KEY `permission_id` (`permission_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=21 ;

--
-- Dumping data for table `wetransfer_usergroup_permissions`
--

INSERT INTO `wetransfer_usergroup_permissions` (`id`, `usergroup_id`, `permission_id`) VALUES
(16, 2, 1),
(13, 2, 2),
(12, 2, 5),
(1, 3, 1),
(2, 3, 2),
(3, 3, 3),
(4, 3, 4),
(9, 3, 5),
(10, 3, 6),
(11, 3, 7),
(18, 3, 9),
(19, 3, 10),
(20, 3, 11),
(14, 4, 1),
(15, 4, 2),
(17, 4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `wetransfer_users`
--

CREATE TABLE IF NOT EXISTS `wetransfer_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` text COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `avatar_url` text COLLATE utf8_unicode_ci,
  `site_language` bigint(20) unsigned DEFAULT NULL,
  `site_status` enum('banned','pending','confirmed','auto_confirmed','unconfirmed') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'unconfirmed',
  `date_created` int(10) unsigned NOT NULL,
  `signup_ip` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_upload` int(10) unsigned DEFAULT NULL,
  `last_active` int(10) unsigned DEFAULT NULL,
  `last_login_date` int(10) unsigned DEFAULT NULL,
  `last_ip` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `site_language` (`site_language`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `wetransfer_users`
--

INSERT INTO `wetransfer_users` (`id`, `email`, `username`, `password`, `avatar_url`, `site_language`, `site_status`, `date_created`, `signup_ip`, `last_upload`, `last_active`, `last_login_date`, `last_ip`) VALUES
(1, 'admin@whitelabeltransfer.com', NULL, '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', NULL, 1, 'auto_confirmed', 1383254334, NULL, NULL, 1386891788, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wetransfer_user_confirm`
--

CREATE TABLE IF NOT EXISTS `wetransfer_user_confirm` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `wetransfer_logs_download`
--
ALTER TABLE `wetransfer_logs_download`
  ADD CONSTRAINT `wetransfer_logs_download_ibfk_1` FOREIGN KEY (`upload_id`) REFERENCES `wetransfer_uploads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `wetransfer_logs_download_ibfk_2` FOREIGN KEY (`recipient_id`) REFERENCES `wetransfer_recipients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wetransfer_phrases`
--
ALTER TABLE `wetransfer_phrases`
  ADD CONSTRAINT `wetransfer_phrases_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `wetransfer_languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wetransfer_recipients`
--
ALTER TABLE `wetransfer_recipients`
  ADD CONSTRAINT `wetransfer_recipients_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `wetransfer_uploads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wetransfer_uploaded_files`
--
ALTER TABLE `wetransfer_uploaded_files`
  ADD CONSTRAINT `wetransfer_uploaded_files_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `wetransfer_uploads` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wetransfer_uploads`
--
ALTER TABLE `wetransfer_uploads`
  ADD CONSTRAINT `wetransfer_uploads_ibfk_2` FOREIGN KEY (`uploader`) REFERENCES `wetransfer_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wetransfer_usergroup_members`
--
ALTER TABLE `wetransfer_usergroup_members`
  ADD CONSTRAINT `wetransfer_usergroup_members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `wetransfer_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `wetransfer_usergroup_members_ibfk_2` FOREIGN KEY (`usergroup_id`) REFERENCES `wetransfer_usergroups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wetransfer_usergroup_permissions`
--
ALTER TABLE `wetransfer_usergroup_permissions`
  ADD CONSTRAINT `wetransfer_usergroup_permissions_ibfk_1` FOREIGN KEY (`usergroup_id`) REFERENCES `wetransfer_usergroups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `wetransfer_usergroup_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `wetransfer_site_permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wetransfer_users`
--
ALTER TABLE `wetransfer_users`
  ADD CONSTRAINT `wetransfer_users_ibfk_1` FOREIGN KEY (`site_language`) REFERENCES `wetransfer_languages` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `wetransfer_user_confirm`
--
ALTER TABLE `wetransfer_user_confirm`
  ADD CONSTRAINT `wetransfer_user_confirm_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `wetransfer_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;