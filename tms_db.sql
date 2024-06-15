-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2021 at 01:53 AM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `project_list`
--

CREATE TABLE `project_list` (
  `id` int(30) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `manager_id` int(30) NOT NULL,
  `user_ids` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `project_list`
--

INSERT INTO `project_list` (`id`, `name`, `description`, `status`, `start_date`, `end_date`, `manager_id`, `user_ids`, `date_created`) VALUES
(4, 'Thiết kế hệ thống báo cháy thông minh', '						Thiết kế hệ thống b&aacute;o ch&aacute;y th&ocirc;ng minh cho tập đo&agrave;n Vingroup					', 0, '2021-03-03', '2021-12-30', 7, '10,9', '2021-03-02 20:09:22'),
(5, 'thu bảo hiểm', 'Thu bảo hiểm khối 12', 0, '2021-03-06', '2021-12-31', 13, '10,9', '2021-03-06 07:39:20');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `cover_img` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `name`, `email`, `contact`, `address`, `cover_img`) VALUES
(1, 'Quản lý công việc', 'info@sample.comm', '+84', '63 Nguyễn Huệ', '');

-- --------------------------------------------------------

--
-- Table structure for table `task_list`
--

CREATE TABLE `task_list` (
  `id` int(30) NOT NULL,
  `project_id` int(30) NOT NULL,
  `task` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(4) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `task_list`
--

INSERT INTO `task_list` (`id`, `project_id`, `task`, `description`, `status`, `date_created`) VALUES
(7, 4, 'Nghiên cứu lý thuyết', '								&lt;p&gt;Nghi&ecirc;n cứu c&aacute;c dự &aacute;n đ&atilde; c&oacute; sẵn&lt;/p&gt;						', 3, '2021-03-02 20:23:00'),
(8, 4, 'Lập bảng mô tả nguyên lý', '				Nộp kế hoạch			', 3, '2021-03-02 20:23:26'),
(9, 4, 'Thiết kế nguyên mẫu', '								Tr&igrave;nh b&agrave;y nguy&ecirc;n mẫu cho quản l&yacute;						', 3, '2021-03-02 20:24:22'),
(10, 4, 'Thiết kế sản phẩm hoàn thiện', 'Thiết kế sản phẩm ho&agrave;n thiện, tr&igrave;nh b&agrave;y ban gd', 1, '2021-03-02 20:30:22'),
(11, 5, 'Lưu Hòa thu bảo hiểm 12a1-12a3', '														', 3, '2021-03-06 07:39:51'),
(12, 5, 'Lê kha thu bảo hiểm 12a4 - 12a7', '							', 1, '2021-03-06 07:40:05'),
(13, 5, 'cả 2 tổng hợp và kiểm kê', '							', 1, '2021-03-06 07:40:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(30) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1 = admin, 2 = staff',
  `avatar` varchar(255) NOT NULL DEFAULT 'no-image-available.png',
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `password`, `type`, `avatar`, `date_created`) VALUES
(1, 'Administrator', '', 'admin@admin.com', '21232f297a57a5a743894a0e4a801fc3', 1, 'no-image-available.png', '2020-11-26 10:57:04'),
(7, 'Chánh', 'Trần', 'chanh@gmail.com', '202cb962ac59075b964b07152d234b70', 2, '1614690180_1.png', '2021-03-02 20:03:11'),
(8, 'Thọ', 'Lê', 'tho@gmail.com', '202cb962ac59075b964b07152d234b70', 2, '1614690180_2.png', '2021-03-02 20:03:45'),
(9, 'Hòa', 'Lưu', 'hoa@gmail.com', '202cb962ac59075b964b07152d234b70', 3, '1614690240_3.png', '2021-03-02 20:04:12'),
(10, 'Kha', 'Lê', 'kha@gmail.com', '202cb962ac59075b964b07152d234b70', 3, '1614690240_4.png', '2021-03-02 20:04:33'),
(11, 'Kiên', 'Phan', 'kien@gmail.com', '202cb962ac59075b964b07152d234b70', 3, '1614690240_6.png', '2021-03-02 20:04:54'),
(12, 'Lưu', 'Lưu', 'luu@gmail.com', '202cb962ac59075b964b07152d234b70', 3, '1614691920_download.jpg', '2021-03-02 20:32:28'),
(13, 'Tuyen', 'Le', 'tuyen@gmail.com', '202cb962ac59075b964b07152d234b70', 2, '1614990900_1__6.png', '2021-03-06 07:35:30');

-- --------------------------------------------------------

--
-- Table structure for table `user_productivity`
--

CREATE TABLE `user_productivity` (
  `id` int(30) NOT NULL,
  `project_id` int(30) NOT NULL,
  `task_id` int(30) NOT NULL,
  `comment` text NOT NULL,
  `subject` varchar(200) NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `user_id` int(30) NOT NULL,
  `time_rendered` float NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_productivity`
--

INSERT INTO `user_productivity` (`id`, `project_id`, `task_id`, `comment`, `subject`, `date`, `start_time`, `end_time`, `user_id`, `time_rendered`, `date_created`) VALUES
(5, 4, 8, 'Đ&atilde; xong ạ', 'Hoàn thành', '2021-03-03', '21:25:00', '12:26:00', 9, -8.98333, '2021-03-02 20:26:07'),
(6, 4, 7, 'Đ&atilde; xong nghi&ecirc;n cứu tiến độ', 'Đã xong', '2021-03-01', '21:29:00', '22:29:00', 9, 1, '2021-03-02 20:29:30'),
(7, 4, 9, 'hẹn ban gd để xem nguy&ecirc;n mẫu', 'Đã thiết kế nguyên mẫu', '2021-03-02', '23:30:00', '12:30:00', 9, -11, '2021-03-02 20:31:06'),
(8, 5, 11, 'c&oacute; 2 người chưa&amp;nbsp; đ&oacute;ng', 'đã xong', '2021-03-07', '07:42:00', '10:42:00', 9, 3, '2021-03-06 07:42:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `project_list`
--
ALTER TABLE `project_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pl` (`manager_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `task_list`
--
ALTER TABLE `task_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tl` (`project_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_productivity`
--
ALTER TABLE `user_productivity`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `project_list`
--
ALTER TABLE `project_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `task_list`
--
ALTER TABLE `task_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_productivity`
--
ALTER TABLE `user_productivity`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `project_list`
--
ALTER TABLE `project_list`
  ADD CONSTRAINT `fk_pl` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `task_list`
--
ALTER TABLE `task_list`
  ADD CONSTRAINT `fk_tl` FOREIGN KEY (`project_id`) REFERENCES `project_list` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
