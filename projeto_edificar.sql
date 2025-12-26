-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 23, 2025 at 07:10 PM
-- Server version: 8.0.44-0ubuntu0.22.04.1
-- PHP Version: 8.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projeto_edificar`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cells`
--

CREATE TABLE `cells` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `supervision_id` bigint UNSIGNED NOT NULL,
  `leader_id` bigint UNSIGNED DEFAULT NULL,
  `member_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cells`
--

INSERT INTO `cells` (`id`, `name`, `supervision_id`, `leader_id`, `member_count`, `created_at`, `updated_at`) VALUES
(1, 'Célula Supervisor S1', 1, NULL, 1, '2025-10-18 09:57:26', '2025-10-18 09:57:26');

-- --------------------------------------------------------

--
-- Table structure for table `commitment_packages`
--

CREATE TABLE `commitment_packages` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_amount` decimal(10,2) NOT NULL,
  `max_amount` decimal(10,2) DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `commitment_packages`
--

INSERT INTO `commitment_packages` (`id`, `name`, `min_amount`, `max_amount`, `description`, `order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Pacote 1', '10.00', '250.00', 'Entrada - Até 250 MT', 1, 1, '2025-10-18 09:57:26', '2025-10-18 09:57:26'),
(2, 'Pacote 2', '250.00', '500.00', 'Intermediário - De 250 a 500 MT', 2, 1, '2025-10-18 09:57:26', '2025-10-18 09:57:26'),
(3, 'Pacote 3', '500.00', '1000.00', 'Intermediário-Alto - De 500 a 1000 MT', 3, 1, '2025-10-18 09:57:26', '2025-10-18 09:57:26'),
(4, 'Pacote 4', '1000.00', '2000.00', 'Alto - De 1000 a 2000 MT', 4, 1, '2025-10-18 09:57:26', '2025-10-18 09:57:26'),
(5, 'Pacote 5', '2000.00', NULL, 'Visionário - Acima de 2000 MT (sem limite)', 5, 1, '2025-10-18 09:57:26', '2025-10-18 09:57:26');

-- --------------------------------------------------------

--
-- Table structure for table `contributions`
--

CREATE TABLE `contributions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `cell_id` bigint UNSIGNED NOT NULL,
  `supervision_id` bigint UNSIGNED NOT NULL,
  `zone_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `contribution_date` date NOT NULL,
  `proof_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pendente','verificada','rejeitada') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente',
  `registered_by_id` bigint UNSIGNED DEFAULT NULL,
  `verified_by_id` bigint UNSIGNED DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contributions`
--

INSERT INTO `contributions` (`id`, `user_id`, `cell_id`, `supervision_id`, `zone_id`, `amount`, `contribution_date`, `proof_path`, `status`, `registered_by_id`, `verified_by_id`, `notes`, `created_at`, `updated_at`) VALUES
(56, 1, 1, 1, 1, '50.00', '2025-10-20', NULL, 'verificada', 1, NULL, 'Verificado', '2025-10-20 06:58:53', '2025-10-22 08:51:51');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_10_16_123104_create_zones_table', 1),
(5, '2025_10_16_123119_create_supervisions_table', 1),
(6, '2025_10_16_123142_create_cells_table', 1),
(7, '2025_10_16_123159_create_commitment_packages_table', 1),
(8, '2025_10_16_123214_update_users_table', 1),
(9, '2025_10_16_123228_create_user_commitments_table', 1),
(10, '2025_10_16_123244_create_contributions_table', 1),
(11, '2025_10_18_081150_add_reporting_fields_to_user_commitments_table', 1),
(12, '2025_10_20_151306_create_notifications_table', 2),
(13, '2025_10_21_122345_add_pastor_id_to_zones_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `notifiable_id`, `notifiable_type`, `type`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('c8dc781d-4424-47ea-aac6-7200351dc24f', 1, 'App\\Models\\User', 'App\\Notifications\\ContributionVerifiedNotification', '{\"title\":\"Contribui\\u00e7\\u00e3o Verificada \\u2713\",\"message\":\"Sua contribui\\u00e7\\u00e3o de 50,00 MT foi verificada com sucesso.\",\"link\":\"http:\\/\\/146.235.224.99\\/edificar\\/contributions\\/56\",\"type\":\"contribution_verified\",\"contribution_id\":56}', '2025-10-22 20:23:39', '2025-10-22 08:51:51', '2025-10-22 20:23:39');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('iDUcYWQYB6jLS5WzzM0B6gyjjecSgQL2PIiLdtqL', 1, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:144.0) Gecko/20100101 Firefox/144.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiS2FmUmV0MmNVRjVWNDBQWWJFY1Y0V2dTZEk2WWJjaW9Bd3dRZWVmZyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjQ5OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vY29udHJpYnV0aW9ucy9wZW5kaW5nIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1760789212);

-- --------------------------------------------------------

--
-- Table structure for table `supervisions`
--

CREATE TABLE `supervisions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `zone_id` bigint UNSIGNED NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supervisions`
--

INSERT INTO `supervisions` (`id`, `name`, `zone_id`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Supervisão de Joaquina', 1, 'Supervisão 1 da Zona Centro A', '2025-10-18 09:57:26', '2025-10-20 06:49:40'),
(2, 'Supervisão Lidiane', 1, 'Supervisão 2 da Zona Centro', '2025-10-18 09:57:28', '2025-10-20 07:47:41'),
(3, 'Supervisão Nazare', 2, 'Supervisão 1', '2025-10-18 09:57:30', '2025-10-20 07:47:14'),
(4, 'Supervisão de Magide', 3, 'Supervisão 2', '2025-10-18 09:57:32', '2025-10-20 12:56:46'),
(5, 'Supervisão Filipe', 3, NULL, '2025-10-20 12:53:19', '2025-10-20 12:53:19'),
(6, 'Supervisão Bendito', 3, 'Supervisao 3', '2025-10-20 12:57:28', '2025-10-20 12:57:28');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('membro','lider_celula','supervisor','pastor_zona','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'membro',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cell_id` bigint UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`, `cell_id`, `is_active`) VALUES
(1, 'Administrador Portal Life Church', 'admin@chiesa.local', '823562000', NULL, '$2y$12$M1b/TmjH/GYT3pl/eDZhmeWyFPAOY48EZNNzTtMnzcqMkmxo6S9we', 'admin', 'jTno5O6sCMMltwJCcpT3ndV1Syzd9qBpV6bASJHoZIBS1Wf1eshfuhLKNpmf', '2025-10-18 09:57:26', '2025-10-18 10:01:18', 1, 1),
(49, 'Paulo Nazare', 'paulo@lifechurch.com', '8967690505', NULL, '$2y$12$.w2L58mQbhWFyUmvqgfco.TVFBfYIrsiCL82fQMZCAy1kDKXtbKbe', 'admin', NULL, '2025-10-20 06:52:33', '2025-10-22 20:25:48', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_commitments`
--

CREATE TABLE `user_commitments` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `cell_id` bigint UNSIGNED NOT NULL,
  `package_id` bigint UNSIGNED NOT NULL,
  `committed_amount` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `zones`
--

CREATE TABLE `zones` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `pastor_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `zones`
--

INSERT INTO `zones` (`id`, `name`, `description`, `pastor_id`, `created_at`, `updated_at`) VALUES
(1, 'Zona Centro A', 'Centro da Cidade', NULL, '2025-10-18 09:57:26', '2025-10-20 06:50:00'),
(2, 'Zona Centro B', 'Zona Este', NULL, '2025-10-18 09:57:30', '2025-10-20 06:50:21'),
(3, 'Zona do Manhaua', 'Zona Norderste', NULL, '2025-10-20 06:50:48', '2025-10-20 06:50:48'),
(4, 'Zona do Santagua', 'Zona Norte', NULL, '2025-10-20 06:51:23', '2025-10-20 06:51:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cells`
--
ALTER TABLE `cells`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cells_name_supervision_id_unique` (`name`,`supervision_id`),
  ADD KEY `cells_supervision_id_foreign` (`supervision_id`),
  ADD KEY `cells_leader_id_foreign` (`leader_id`);

--
-- Indexes for table `commitment_packages`
--
ALTER TABLE `commitment_packages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `commitment_packages_name_unique` (`name`);

--
-- Indexes for table `contributions`
--
ALTER TABLE `contributions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contributions_supervision_id_foreign` (`supervision_id`),
  ADD KEY `contributions_registered_by_id_foreign` (`registered_by_id`),
  ADD KEY `contributions_verified_by_id_foreign` (`verified_by_id`),
  ADD KEY `contributions_user_id_index` (`user_id`),
  ADD KEY `contributions_cell_id_index` (`cell_id`),
  ADD KEY `contributions_zone_id_index` (`zone_id`),
  ADD KEY `contributions_contribution_date_index` (`contribution_date`),
  ADD KEY `contributions_status_index` (`status`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_id_notifiable_type_index` (`notifiable_id`,`notifiable_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `supervisions`
--
ALTER TABLE `supervisions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `supervisions_name_zone_id_unique` (`name`,`zone_id`),
  ADD KEY `supervisions_zone_id_foreign` (`zone_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_cell_id_foreign` (`cell_id`);

--
-- Indexes for table `user_commitments`
--
ALTER TABLE `user_commitments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_commitments_package_id_foreign` (`package_id`),
  ADD KEY `user_commitments_user_id_index` (`user_id`),
  ADD KEY `user_commitments_cell_id_foreign` (`cell_id`);

--
-- Indexes for table `zones`
--
ALTER TABLE `zones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `zones_name_unique` (`name`),
  ADD KEY `zones_pastor_id_foreign` (`pastor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cells`
--
ALTER TABLE `cells`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `commitment_packages`
--
ALTER TABLE `commitment_packages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contributions`
--
ALTER TABLE `contributions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `supervisions`
--
ALTER TABLE `supervisions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `user_commitments`
--
ALTER TABLE `user_commitments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `zones`
--
ALTER TABLE `zones`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cells`
--
ALTER TABLE `cells`
  ADD CONSTRAINT `cells_leader_id_foreign` FOREIGN KEY (`leader_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `cells_supervision_id_foreign` FOREIGN KEY (`supervision_id`) REFERENCES `supervisions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contributions`
--
ALTER TABLE `contributions`
  ADD CONSTRAINT `contributions_cell_id_foreign` FOREIGN KEY (`cell_id`) REFERENCES `cells` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `contributions_registered_by_id_foreign` FOREIGN KEY (`registered_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contributions_supervision_id_foreign` FOREIGN KEY (`supervision_id`) REFERENCES `supervisions` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `contributions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contributions_verified_by_id_foreign` FOREIGN KEY (`verified_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `contributions_zone_id_foreign` FOREIGN KEY (`zone_id`) REFERENCES `zones` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `supervisions`
--
ALTER TABLE `supervisions`
  ADD CONSTRAINT `supervisions_zone_id_foreign` FOREIGN KEY (`zone_id`) REFERENCES `zones` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_cell_id_foreign` FOREIGN KEY (`cell_id`) REFERENCES `cells` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_commitments`
--
ALTER TABLE `user_commitments`
  ADD CONSTRAINT `user_commitments_cell_id_foreign` FOREIGN KEY (`cell_id`) REFERENCES `cells` (`id`),
  ADD CONSTRAINT `user_commitments_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `commitment_packages` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `user_commitments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `zones`
--
ALTER TABLE `zones`
  ADD CONSTRAINT `zones_pastor_id_foreign` FOREIGN KEY (`pastor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
