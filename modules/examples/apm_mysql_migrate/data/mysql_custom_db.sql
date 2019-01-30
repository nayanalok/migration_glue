# Custom mysql data.

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE `custom_mysql_db` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `custom_mysql_db`;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `comment_by` varchar(255) DEFAULT NULL,
  `reply_to` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `content_id`, `title`, `content`, `comment_by`, `reply_to`, `created`) VALUES
(1, 1, 'Good article', 'Really appreciate it.', 'user1', NULL, '2016-08-30 10:00:00'),
(2, 1, 'Awesome Work', 'Good going, keep writing', 'roma', 1, '2018-05-06 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE `content` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `path` varchar(255) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`id`, `title`, `body`, `image`, `keywords`, `author`, `path`, `date_created`, `status`) VALUES
(1, 'Blog 1', 'Hypertext Markup Language <strong>(HTML)</strong> is the standard markup language for creating web pages and web applications. With Cascading Style Sheets (CSS) and JavaScript, it forms a triad of cornerstone technologies for the World Wide Web. <img src="https://cdn.lynda.com/course/439683/439683-636441077028502313-16x9.jpg" alt="test" />', 'https://static.makeuseof.com/wp-content/uploads/2017/09/HTML-Effects-Featured-670x335.jpg', 'blog', 'sumo', '/blog-1', '2016-08-30 10:20:00', 1),
(2, 'Migration work', 'Why i preffer hacks over separate css files for IE: there are hacks for every browser not only IE. And even newest browsers are rendering some things differently, eg. input fields, checkboxes or table captions; everything in one file is easier to maitenance; smaller number of requests to server; not every hack is "hack". Very often hack is just smart use selectors (not) supported only by specific browser;', 'https://cdn.lynda.com/course/439683/439683-636441077028502313-16x9.jpg', 'blog,migration', 'monu', '/migration-work', '2016-09-10 11:15:00', 0),
(3, 'Mysql Migration', 'A content management system (CMS) is a software tool that lets users add, publish, edit, or remove content from a website, using a web browser on a smartphone, tablet, or desktop computer. Typically, the CMS software is written in a scripting language, and its scripts run on a computer where a database and a web server are installed. The content and settings for the website are usually stored in a database, and for each page request that comes to the web server, the scripts combine information from the database and assets (JavaScript files, CSS files, image files, etc. that are part of the CMS or have been uploaded) to build the pages of the website.', 'https://www.weebpal.com/sites/default/files/blog/learn%20Drupal%20for%20beginners_new.jpg', 'mysql,migration', 'roma', '/mysql-migration', '2016-08-30 10:20:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `taxonomy`
--

CREATE TABLE `taxonomy` (
  `term_id` int(11) NOT NULL,
  `term_name` varchar(255) DEFAULT NULL,
  `term_desc` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `taxonomy`
--

INSERT INTO `taxonomy` (`term_id`, `term_name`, `term_desc`) VALUES
(1, 'blog', 'blog description'),
(2, 'migration', 'migration desc'),
(3, 'mysql', 'mysql desc');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `roles` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `language` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `username`, `password`, `roles`, `created`, `updated`, `status`, `picture`, `language`) VALUES
(1, 'pk.j@gmail.com', 'user1', NULL, 'normal', '2016-08-30 10:00:00', '2018-12-08 07:16:09', 1, 'https://images-na.ssl-images-amazon.com/images/I/61PCWfClLNL._SX425_.jpg', 'en'),
(2, 'sumo@example2.com', 'sumo', '21232f297a57a5a743894a0e4a801fc3', 'admin,normal', '2016-08-30 10:00:00', '2018-12-02 07:16:09', 1, 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/User_icon_2.svg/220px-User_icon_2.svg.png', 'en'),
(3, 'monu@gmail.com', 'monu', '21232f297a57a5a743894a0e4a801fc3', 'normal', '2016-08-31 10:00:00', '2018-12-01 00:00:00', 0, 'http://icons.iconarchive.com/icons/icons8/ios7/256/Users-User-Male-icon.png', 'en'),
(4, 'roma@yahoo.com', 'roma', NULL, 'admin,normal', '2018-12-05 00:00:00', '2018-12-22 00:00:00', 1, 'https://image.shutterstock.com/image-vector/user-icon-vector-male-person-260nw-267256895.jpg', 'en'),
(5, 'roshini@hotmail.com', 'roshini', NULL, 'normal,admin', '2018-08-05 00:00:00', '2018-12-08 07:16:09', 1, 'http://simpleicon.com/wp-content/uploads/female-user.png', 'en');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `taxonomy`
--
ALTER TABLE `taxonomy`
  ADD PRIMARY KEY (`term_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `content`
--
ALTER TABLE `content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `taxonomy`
--
ALTER TABLE `taxonomy`
  MODIFY `term_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;