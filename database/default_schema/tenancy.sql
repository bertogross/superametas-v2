-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 14, 2023 at 02:22 AM
-- Server version: 10.5.19-MariaDB-0+deb11u2
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smAppSCHEMA`
--

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_attachments`
--

CREATE TABLE `wlsm_attachments` (
  `attachment_id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `attachment_author` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `attachment_url` varchar(300) NOT NULL,
  `attachment_type` varchar(100) NOT NULL,
  `attachment_title` varchar(100) DEFAULT NULL,
  `attachment_description` varchar(300) DEFAULT NULL,
  `attachment_destiny` varchar(20) NOT NULL DEFAULT 'multiple',
  `attachment_size` varchar(100) NOT NULL,
  `attachment_order` tinyint(9) NOT NULL DEFAULT 0,
  `attachment_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_commentmeta`
--

CREATE TABLE `wlsm_commentmeta` (
  `meta_id` bigint(20) UNSIGNED NOT NULL,
  `comment_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_comments`
--

CREATE TABLE `wlsm_comments` (
  `comment_ID` bigint(20) UNSIGNED NOT NULL,
  `comment_post_ID` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `comment_author` tinytext NOT NULL,
  `comment_author_email` varchar(100) NOT NULL DEFAULT '',
  `comment_author_url` varchar(200) NOT NULL DEFAULT '',
  `comment_author_IP` varchar(100) NOT NULL DEFAULT '',
  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `comment_content` text NOT NULL,
  `comment_karma` int(11) NOT NULL DEFAULT 0,
  `comment_approved` varchar(20) NOT NULL DEFAULT '1',
  `comment_agent` varchar(255) NOT NULL DEFAULT '',
  `comment_type` varchar(20) NOT NULL DEFAULT 'comment',
  `comment_parent` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_companies`
--

CREATE TABLE `wlsm_companies` (
  `id` int(11) NOT NULL,
  `company_id` bigint(10) NOT NULL,
  `company_name` varchar(200) NOT NULL,
  `company_alias` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_departments`
--

CREATE TABLE `wlsm_departments` (
  `id` bigint(20) NOT NULL,
  `department_id` int(10) NOT NULL,
  `department_description` varchar(200) NOT NULL,
  `first_load` datetime NOT NULL DEFAULT current_timestamp(),
  `last_load` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_goals_sales`
--

CREATE TABLE `wlsm_goals_sales` (
  `id` bigint(200) NOT NULL,
  `company_id` bigint(20) NOT NULL DEFAULT 0,
  `department_id` bigint(20) NOT NULL DEFAULT 0,
  `category_id` int(20) NOT NULL DEFAULT 0,
  `net_value` decimal(50,2) NOT NULL DEFAULT 0.00,
  `date_sale` date DEFAULT NULL,
  `first_load` datetime NOT NULL DEFAULT current_timestamp(),
  `last_load` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_links`
--

CREATE TABLE `wlsm_links` (
  `link_id` bigint(20) UNSIGNED NOT NULL,
  `link_url` varchar(255) NOT NULL DEFAULT '',
  `link_name` varchar(255) NOT NULL DEFAULT '',
  `link_image` varchar(255) NOT NULL DEFAULT '',
  `link_target` varchar(25) NOT NULL DEFAULT '',
  `link_description` varchar(255) NOT NULL DEFAULT '',
  `link_visible` varchar(20) NOT NULL DEFAULT 'Y',
  `link_owner` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `link_rating` int(11) NOT NULL DEFAULT 0,
  `link_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `link_rel` varchar(255) NOT NULL DEFAULT '',
  `link_notes` mediumtext NOT NULL,
  `link_rss` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_options`
--

CREATE TABLE `wlsm_options` (
  `option_id` bigint(20) UNSIGNED NOT NULL,
  `option_name` varchar(191) NOT NULL DEFAULT '',
  `option_value` longtext NOT NULL,
  `autoload` varchar(20) NOT NULL DEFAULT 'yes'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wlsm_options`
--

INSERT INTO `wlsm_options` (`option_id`, `option_name`, `option_value`, `autoload`) VALUES
(1, 'siteurl', 'https://app.superametas.com', 'yes'),
(2, 'home', 'https://app.superametas.com', 'yes'),
(3, 'blogname', 'Supera Metas', 'yes'),
(4, 'blogdescription', 'Acesso Restrito', 'yes'),
(5, 'users_can_register', '0', 'yes'),
(6, 'admin_email', 'app@superametas.com', 'yes'),
(7, 'start_of_week', '0', 'yes'),
(8, 'use_balanceTags', '0', 'yes'),
(9, 'posts_per_page', '10', 'yes'),
(10, 'date_format', 'd-m-Y', 'yes'),
(11, 'time_format', 'H:i', 'yes'),
(12, 'links_updated_date_format', 'j \\d\\e F \\d\\e Y, H:i', 'yes'),
(13, 'permalink_structure', '/%postname%/', 'yes'),
(14, 'blog_charset', 'UTF-8', 'yes'),
(15, 'template', 'wlsmTmplt', 'yes'),
(16, 'stylesheet', 'wlsmTmplt', 'yes'),
(17, 'current_theme', 'wlsmTmplt', 'yes'),
(18, 'html_type', 'text/html', 'yes'),
(19, 'default_role', 'subscriber', 'yes'),
(20, 'uploads_use_yearmonth_folders', '1', 'yes'),
(21, 'upload_path', '', 'yes'),
(22, 'blog_public', '0', 'yes'),
(24, 'upload_url_path', '', 'yes'),
(25, 'thumbnail_size_w', '150', 'yes'),
(26, 'thumbnail_size_h', '150', 'yes'),
(27, 'thumbnail_crop', '1', 'yes'),
(28, 'medium_size_w', '300', 'yes'),
(29, 'medium_size_h', '300', 'yes'),
(30, 'avatar_default', 'mystery', 'yes'),
(31, 'large_size_w', '1024', 'yes'),
(32, 'large_size_h', '1024', 'yes'),
(33, 'close_comments_days_old', '14', 'yes'),
(34, 'comments_per_page', '50', 'yes'),
(35, 'default_comments_page', 'newest', 'yes'),
(36, 'comment_order', 'asc', 'yes'),
(37, 'timezone_string', 'America/Sao_Paulo', 'yes'),
(38, 'page_for_posts', '0', 'yes'),
(40, 'fresh_site', '0', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_postmeta`
--

CREATE TABLE `wlsm_postmeta` (
  `meta_id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wlsm_postmeta`
--

INSERT INTO `wlsm_postmeta` (`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES
(1, 1, '_wp_page_template', 'pages/home.php');

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_posts`
--

CREATE TABLE `wlsm_posts` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `post_author` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `post_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `post_content` longtext NOT NULL,
  `post_title` text NOT NULL,
  `post_excerpt` text NOT NULL,
  `post_status` varchar(20) NOT NULL DEFAULT 'publish',
  `comment_status` varchar(20) NOT NULL DEFAULT 'open',
  `ping_status` varchar(20) NOT NULL DEFAULT 'open',
  `post_password` varchar(255) NOT NULL DEFAULT '',
  `post_name` varchar(200) NOT NULL DEFAULT '',
  `to_ping` text NOT NULL,
  `pinged` text NOT NULL,
  `post_modified` datetime NOT NULL DEFAULT current_timestamp(),
  `post_modified_gmt` datetime NOT NULL DEFAULT current_timestamp(),
  `post_content_filtered` longtext NOT NULL,
  `post_parent` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `guid` varchar(255) NOT NULL DEFAULT '',
  `menu_order` int(11) NOT NULL DEFAULT 0,
  `post_type` varchar(20) NOT NULL DEFAULT 'post',
  `post_mime_type` varchar(100) NOT NULL DEFAULT '',
  `comment_count` bigint(20) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wlsm_posts`
--

INSERT INTO `wlsm_posts` (`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES
(1, 1, '2022-06-25 11:51:54', '2022-06-25 14:51:54', '', 'Dashboard', '', 'publish', 'closed', 'closed', '', 'home', '', '', '2022-06-25 11:51:54', '2022-06-25 14:51:54', '', 0, 'https://app.superametas.com/home/', 0, 'page', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_sales_temporary`
--

CREATE TABLE `wlsm_sales_temporary` (
  `id` bigint(200) NOT NULL,
  `company_id` bigint(20) NOT NULL DEFAULT 0,
  `department_id` bigint(20) NOT NULL DEFAULT 0,
  `category_id` int(20) NOT NULL DEFAULT 0,
  `net_value` decimal(50,2) NOT NULL DEFAULT 0.00,
  `date_sale` date DEFAULT NULL,
  `meantime` varchar(7) NOT NULL,
  `first_load` datetime NOT NULL DEFAULT current_timestamp(),
  `last_load` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_termmeta`
--

CREATE TABLE `wlsm_termmeta` (
  `meta_id` bigint(20) UNSIGNED NOT NULL,
  `term_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_terms`
--

CREATE TABLE `wlsm_terms` (
  `term_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL DEFAULT '',
  `slug` varchar(200) NOT NULL DEFAULT '',
  `term_status` int(1) NOT NULL DEFAULT 1,
  `term_group` bigint(10) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_term_relationships`
--

CREATE TABLE `wlsm_term_relationships` (
  `object_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `term_taxonomy_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `term_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_term_taxonomy`
--

CREATE TABLE `wlsm_term_taxonomy` (
  `term_taxonomy_id` bigint(20) UNSIGNED NOT NULL,
  `term_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `taxonomy` varchar(32) NOT NULL DEFAULT '',
  `description` longtext NOT NULL,
  `parent` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `count` bigint(20) NOT NULL DEFAULT 0,
  `term_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_usermeta`
--

CREATE TABLE `wlsm_usermeta` (
  `umeta_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_users`
--

CREATE TABLE `wlsm_users` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `user_login` varchar(60) NOT NULL DEFAULT '',
  `user_pass` varchar(255) NOT NULL DEFAULT '',
  `user_nicename` varchar(50) NOT NULL DEFAULT '',
  `user_email` varchar(100) NOT NULL DEFAULT '',
  `user_url` varchar(100) NOT NULL DEFAULT '',
  `user_registered` datetime NOT NULL DEFAULT current_timestamp(),
  `user_activation_key` varchar(255) NOT NULL DEFAULT '',
  `user_status` int(11) NOT NULL DEFAULT 1,
  `display_name` varchar(250) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wlsm_warehouse`
--

CREATE TABLE `wlsm_warehouse` (
  `ID` bigint(20) UNSIGNED NOT NULL,
  `wh_function` varchar(200) NOT NULL COMMENT 'Function name used in this query',
  `wh_data` longtext NOT NULL COMMENT 'Here the query result',
  `meantime` varchar(10) NOT NULL COMMENT 'Year and month',
  `first_load` datetime NOT NULL,
  `last_load` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wlsm_attachments`
--
ALTER TABLE `wlsm_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD UNIQUE KEY `attachment_id` (`attachment_id`);

--
-- Indexes for table `wlsm_commentmeta`
--
ALTER TABLE `wlsm_commentmeta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `comment_id` (`comment_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `wlsm_comments`
--
ALTER TABLE `wlsm_comments`
  ADD PRIMARY KEY (`comment_ID`),
  ADD KEY `comment_post_ID` (`comment_post_ID`),
  ADD KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),
  ADD KEY `comment_date_gmt` (`comment_date_gmt`),
  ADD KEY `comment_parent` (`comment_parent`),
  ADD KEY `comment_author_email` (`comment_author_email`(10));

--
-- Indexes for table `wlsm_companies`
--
ALTER TABLE `wlsm_companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wlsm_departments`
--
ALTER TABLE `wlsm_departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wlsm_goals_sales`
--
ALTER TABLE `wlsm_goals_sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`,`department_id`);

--
-- Indexes for table `wlsm_links`
--
ALTER TABLE `wlsm_links`
  ADD PRIMARY KEY (`link_id`),
  ADD KEY `link_visible` (`link_visible`);

--
-- Indexes for table `wlsm_options`
--
ALTER TABLE `wlsm_options`
  ADD PRIMARY KEY (`option_id`),
  ADD UNIQUE KEY `option_name` (`option_name`),
  ADD KEY `autoload` (`autoload`);

--
-- Indexes for table `wlsm_postmeta`
--
ALTER TABLE `wlsm_postmeta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `wlsm_posts`
--
ALTER TABLE `wlsm_posts`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `post_name` (`post_name`(191)),
  ADD KEY `type_status_date` (`post_type`,`post_status`,`post_date`,`ID`),
  ADD KEY `post_parent` (`post_parent`),
  ADD KEY `post_author` (`post_author`);

--
-- Indexes for table `wlsm_sales_temporary`
--
ALTER TABLE `wlsm_sales_temporary`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`,`department_id`);

--
-- Indexes for table `wlsm_termmeta`
--
ALTER TABLE `wlsm_termmeta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `term_id` (`term_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `wlsm_terms`
--
ALTER TABLE `wlsm_terms`
  ADD PRIMARY KEY (`term_id`),
  ADD KEY `slug` (`slug`(191)),
  ADD KEY `name` (`name`(191));

--
-- Indexes for table `wlsm_term_relationships`
--
ALTER TABLE `wlsm_term_relationships`
  ADD PRIMARY KEY (`object_id`,`term_taxonomy_id`),
  ADD KEY `term_taxonomy_id` (`term_taxonomy_id`);

--
-- Indexes for table `wlsm_term_taxonomy`
--
ALTER TABLE `wlsm_term_taxonomy`
  ADD PRIMARY KEY (`term_taxonomy_id`),
  ADD UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),
  ADD KEY `taxonomy` (`taxonomy`);

--
-- Indexes for table `wlsm_usermeta`
--
ALTER TABLE `wlsm_usermeta`
  ADD PRIMARY KEY (`umeta_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `meta_key` (`meta_key`(191));

--
-- Indexes for table `wlsm_users`
--
ALTER TABLE `wlsm_users`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `user_login_key` (`user_login`),
  ADD KEY `user_nicename` (`user_nicename`),
  ADD KEY `user_email` (`user_email`);

--
-- Indexes for table `wlsm_warehouse`
--
ALTER TABLE `wlsm_warehouse`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `ID` (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wlsm_attachments`
--
ALTER TABLE `wlsm_attachments`
  MODIFY `attachment_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wlsm_commentmeta`
--
ALTER TABLE `wlsm_commentmeta`
  MODIFY `meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wlsm_comments`
--
ALTER TABLE `wlsm_comments`
  MODIFY `comment_ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wlsm_companies`
--
ALTER TABLE `wlsm_companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wlsm_departments`
--
ALTER TABLE `wlsm_departments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `wlsm_goals_sales`
--
ALTER TABLE `wlsm_goals_sales`
  MODIFY `id` bigint(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=430346;

--
-- AUTO_INCREMENT for table `wlsm_links`
--
ALTER TABLE `wlsm_links`
  MODIFY `link_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wlsm_options`
--
ALTER TABLE `wlsm_options`
  MODIFY `option_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `wlsm_postmeta`
--
ALTER TABLE `wlsm_postmeta`
  MODIFY `meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wlsm_posts`
--
ALTER TABLE `wlsm_posts`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wlsm_sales_temporary`
--
ALTER TABLE `wlsm_sales_temporary`
  MODIFY `id` bigint(200) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wlsm_termmeta`
--
ALTER TABLE `wlsm_termmeta`
  MODIFY `meta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wlsm_terms`
--
ALTER TABLE `wlsm_terms`
  MODIFY `term_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wlsm_term_taxonomy`
--
ALTER TABLE `wlsm_term_taxonomy`
  MODIFY `term_taxonomy_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wlsm_usermeta`
--
ALTER TABLE `wlsm_usermeta`
  MODIFY `umeta_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wlsm_users`
--
ALTER TABLE `wlsm_users`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wlsm_warehouse`
--
ALTER TABLE `wlsm_warehouse`
  MODIFY `ID` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
