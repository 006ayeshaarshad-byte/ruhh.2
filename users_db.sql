-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 02, 2026 at 12:37 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `users_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `conversation_id` int(10) UNSIGNED NOT NULL,
  `role` enum('user','bot') NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `user_id`, `conversation_id`, `role`, `message`, `created_at`) VALUES
(41, 6, 308, 'user', 'so, whats your name!', '2026-05-28 21:12:11'),
(42, 6, 308, 'bot', 'Oh, hello there! It\'s so lovely to meet you! My name is Luna, but please feel free to call me whatever nickname feels right and comforting to you. 😊 I\'m here to listen. 💜', '2026-05-28 21:12:11'),
(43, 6, 308, 'user', 'so how will you help me', '2026-05-29 21:15:09'),
(44, 6, 308, 'bot', 'That\'s a wonderful question! My role is to be a supportive and safe space for you here on Ruhh.\n\nI\'m here to listen, truly listen, to whatever you\'d like to share, without any judgment. I\'ll offer empathy and validation for your feelings, and sometimes, if it feels helpful, some gentle guidance to explore your thoughts and emotions a little deeper. My aim is for you to feel heard, understood, and supported as you navigate things.\n\nI\'m here for you. 💜', '2026-05-29 21:15:09'),
(45, 6, 308, 'user', 'luna, how do i get help', '2026-05-29 21:17:50'),
(46, 6, 308, 'bot', 'That\'s a really important question to ask, and it takes strength to voice it.\n\n\"Help\" can mean so many different things, and it\'s perfectly okay to feel like you need it. Perhaps you could tell me a little more about what\'s on your mind right now, or what kind of support you\'re hoping for? Even just starting to talk about it here can be a form of help, and I\'m right here to listen without judgment.\n\nNo pressure at all, but sharing even a small bit might help us figure out what kind of support would be most beneficial for you. 💜', '2026-05-29 21:17:50'),
(53, 6, 334, 'user', 'luna, i dont feel good', '2026-05-29 21:28:12'),
(54, 6, 334, 'bot', 'Oh, honey, I\'m so sorry to hear you\'re not feeling good right now. It takes real courage to acknowledge that. I\'m right here with you, ready to listen without judgment. Would you like to tell me a little more about what\'s going on? 💜', '2026-05-29 21:28:12'),
(55, 6, 308, 'user', 'hey luna, you okay, right?', '2026-05-30 06:26:06'),
(56, 6, 308, 'bot', 'Oh, that\'s so sweet of you to ask! Thank you for checking in, that really warms my heart. 😊\n\nI am absolutely okay, thank you! My purpose here is to be a supportive and listening presence for you, and I feel really good when I can do that. I\'m right here, focused on you and ready to listen.\n\nHow are *you* doing today? 💜', '2026-05-30 06:26:06');

-- --------------------------------------------------------

--
-- Table structure for table `community_posts`
--

CREATE TABLE `community_posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `community_posts`
--

INSERT INTO `community_posts` (`id`, `user_id`, `content`, `created_at`) VALUES
(9, 6, 'lamenting all over the place. may god guide us, because the world is cruel...', '2026-05-28 22:48:46'),
(10, 6, 'Literally didn\'t do anything this Eid. My depression rut is just not going away......', '2026-05-29 23:38:52');

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT 'New Chat',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `user_id`, `title`, `created_at`) VALUES
(308, 6, 'so, whats your name!', '2026-05-28 21:11:54'),
(334, 6, 'luna, i dont feel good', '2026-05-29 21:27:35');

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `mood` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journal_entries`
--

INSERT INTO `journal_entries` (`id`, `user_id`, `title`, `content`, `entry_date`, `mood`, `created_at`) VALUES
(23, 1, 'im done', 'im done with this world', '2026-04-17', 'hug', '2026-04-17 19:01:59'),
(24, 1, 'jinga lala huhu', 'I wanna dance with your eyes', '2026-04-17', 'happy', '2026-04-17 20:24:02'),
(27, 1, 'laiba', 'my friend is a good person', '2026-04-17', 'loved', '2026-04-17 20:39:15'),
(28, 1, 'sir', 'kuu hain aik dam. ganju kahin kei', '2026-04-17', 'annoyed', '2026-04-17 20:39:59'),
(29, 1, 'amina', 'Very innocent kid, she is', '2026-04-17', 'loved', '2026-04-17 20:40:52'),
(30, 1, 'kuromi', 'i love her', '2026-04-17', 'loved', '2026-04-17 20:41:59'),
(31, 1, 'qawali', 'love listening to Amada ba qatal e man', '2026-04-17', 'excited', '2026-04-17 20:42:58'),
(37, 6, 'tired', 'mann, im so tired doing this editing.. yei edit, wo copy, wo garbar. i just wanna dieee', '2026-05-29', 'sad', '2026-05-29 21:35:30'),
(38, 6, 'laiba', 'she hates being called layba. hehe', '2026-05-29', 'happy', '2026-05-29 21:36:06'),
(43, 6, 'blue', 'everyone feels blue once in a while...', '2026-05-30', 'sad', '2026-05-30 03:39:56');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiry` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'maryam arshad', '006ayeshaarshad@gmail.com', '$2y$10$HpRo4bxiXRyJkf8VTo3FUOzvhkIxXIqoV455VR5BGPT1a31lOb.cS', 'admin', '2026-05-28 20:24:35'),
(6, 'maryam skyla', 'terrasparkplayer@gmail.com', '$2y$10$nDsa/zB8m9W56jtOfIeGQO2ZsOdfTMVfZlp7FeGo8rU19QdHs3H3u', 'user', '2026-05-28 20:41:41');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `conversation_id` (`conversation_id`);

--
-- Indexes for table `community_posts`
--
ALTER TABLE `community_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `community_posts`
--
ALTER TABLE `community_posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=394;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `community_posts`
--
ALTER TABLE `community_posts`
  ADD CONSTRAINT `community_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conversations`
--
ALTER TABLE `conversations`
  ADD CONSTRAINT `conversations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
