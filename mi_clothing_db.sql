-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2026 at 06:01 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mi_clothing_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `country` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`address_id`, `user_id`, `full_name`, `address_line1`, `address_line2`, `city`, `state`, `postal_code`, `country`, `phone`, `is_default`) VALUES
(1, 1, 'Hashir Hassan', 'Dhaji Road', 'Dhaji road house # 196 mohallah sultan wala', 'Jhang', 'punjab', '35200', 'Pakistan', '03187068265', 0),
(2, 1, 'Hashir Hassan', 'Dhaji road house # 196 mohallah sultan wala', '', 'Jhang', 'punjab', '35200', 'Pakistan', '03187068265', 1),
(3, 2, 'Hooria Gul hassan', 'dhaji road near a hammed tailor ', '', 'jhang sadar', 'punjab', '35200', 'pakistan', '03174566987', 0);

-- --------------------------------------------------------

--
-- Table structure for table `administrators`
--

CREATE TABLE `administrators` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administrators`
--

INSERT INTO `administrators` (`admin_id`, `username`, `password`, `created_at`, `profile_image`) VALUES
(1, 'Hashir', '$2y$10$3M9wxaFhAqXwljeZbG7EE.yOnzslHz6cNe3MzI9B9A4xtgalzTzhG', '2025-11-29 20:19:06', 'images/1764495642_admin_crop.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`) VALUES
(1, 'Men'),
(2, 'Women'),
(3, 'Accessories'),
(4, 'Shoes');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_address_id` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `order_status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `total_amount`, `shipping_address_id`, `payment_method`, `order_status`) VALUES
(1, 1, '2025-11-07 17:48:43', 1500.00, 1, 'cod', 'Shipped'),
(2, 1, '2025-11-07 18:08:29', 70.00, 2, 'cod', 'Cancelled'),
(3, 2, '2025-11-07 19:50:04', 16245.00, 3, 'cod', 'Cancelled'),
(4, 1, '2025-11-08 19:26:49', 95.00, 2, 'cod', 'Delivered'),
(5, 1, '2025-11-26 13:20:59', 3500.00, 2, 'card', 'Delivered');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price_at_purchase`) VALUES
(1, 1, 24, 1, 1500.00),
(2, 2, 14, 1, 70.00),
(3, 3, 25, 2, 4500.00),
(4, 3, 23, 1, 2000.00),
(5, 3, 20, 1, 75.00),
(6, 3, 17, 1, 70.00),
(7, 3, 21, 1, 30.00),
(8, 3, 26, 1, 3500.00),
(9, 3, 14, 1, 70.00),
(10, 3, 24, 1, 1500.00),
(11, 4, 19, 1, 95.00),
(12, 5, 15, 1, 3500.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` varchar(200) NOT NULL,
  `old_price` decimal(10,2) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'default.jpg',
  `stock_quantity` int(11) NOT NULL DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `old_price`, `category_id`, `image_url`, `stock_quantity`, `created_at`) VALUES
(14, 'Urban Edge Jacket', 'A modern jacket for the urban explorer. Stylish and warm.', '4500.00', 5000.00, 1, 'images/jacket-blue.jpg', 8, '2025-11-03 19:25:12'),
(15, 'Classic White Tee', 'A 100% cotton classic white t-shirt. A wardrobe essential.', '3500.00', NULL, 1, 'images/tee-white.jpg', 29, '2025-11-03 19:25:12'),
(16, 'Vintage Vibe Sneakers', 'Retro-styled sneakers for daily comfort and style.', '4999.00', 5500.00, 4, 'images/sneakers-retro.jpg', 20, '2025-11-03 19:25:12'),
(17, 'Cozy Knit Sweater', 'A soft, cozy knit sweater perfect for chilly days.', '2999', NULL, 2, 'images/sweater-white.jpg', 8, '2025-11-03 19:25:12'),
(18, 'Minimalist Beanie', 'A simple and stylish beanie to top off your look.', '599.00', NULL, 3, 'images/beanie-blue.jpg', 10, '2025-11-03 19:25:12'),
(19, 'Blue Denim Hoodie', 'A relaxed-fit hoodie made from premium denim.', '3000.00', NULL, 1, 'images/hoodie-denim.jpg', 14, '2025-11-03 19:25:12'),
(20, 'Chic Track Jacket', 'A sleek and sporty track jacket for women.', '1999.00', NULL, 2, 'images/track-jacket-blue.jpg', 11, '2025-11-03 19:25:12'),
(21, 'Logo Graphic T-Shirt', 'A comfortable tee with a unique front graphic.', '3999.00', 4500.00, 2, 'images/tee-graphic.jpg', 9, '2025-11-03 19:25:12'),
(22, 'Bold sneakers', 'Sneakers are casual shoes with a flexible rubber sole and an upper, often made of canvas, leather, or synthetic materials', '1500', NULL, 4, 'images/bold sneakers.jpg', 20, '2025-11-03 19:32:25'),
(23, 'Elegent sneaker', 'Sneakers are casual shoes with a flexible rubber sole and an upper, often made of canvas, leather, or synthetic materials', '2000', NULL, 4, 'images/Elegent sneaker.jpg', 8, '2025-11-03 19:34:09'),
(24, 'Unique Belt', 'A belt is a flexible band, typically made of leather or fabric, worn around the waist to hold up clothing or as a fashion accessory', '1500', NULL, 3, 'images/unique-leather.jpg', 8, '2025-11-03 19:36:21'),
(25, 'Dimaond ring', 'A diamond ring is a piece of jewelry featuring a diamond, often symbolizing love, commitment, and eternity', '4500', NULL, 3, 'images/dimaond ring.jpg', 23, '2025-11-03 19:37:50'),
(26, 'Rebel Brown Jacket', 'This is rebel brown jacket made with pure orignl leather and this is very light weight jacket', '3500', 4500.00, 1, 'images/Rebel_Brown-Jacket.jpg', 24, '2025-11-03 20:10:14'),
(27, 'BLUE CAMBRIC EMBROIDERED', 'Due to the photographic lighting & different screen calibrations, the colors of the original product may slightly vary from the picture', '7990', NULL, 2, 'images/blue-cambric1.jpg', 10, '2025-11-26 19:31:14'),
(28, 'BLACK CASUAL KAMEEZ SHALWAR', 'A classic eastern wear set designed with clean stitching and thoughtful detailing. The Black Casual Kameez Shalwar offers a neat look & comfortable fit - ideal for long wear, daily use, or special occasions with a touch of tradition.', '3599', NULL, 1, 'images/men_clasic1.jpg', 15, '2025-11-27 17:26:21'),
(30, 'CREAM CASUAL KAMEEZ SHALWAR', 'A classic eastern wear set designed with clean stitching and thoughtful detailing. The Cream Casual Kameez Shalwar offers a neat look & comfortable fit - ideal for long wear, daily use, or special occasions with a touch of tradition.', '3999', 4500.00, 1, 'images/1764491833_main_CREAM CASUAL KAMEEZ SHALWAR1.jpg', 10, '2025-11-30 08:37:13');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image_url`) VALUES
(8, 14, 'images/jacket-blue.jpg'),
(9, 14, 'images/jacket-blue-2.jpg'),
(10, 14, 'images/jacket-blue-3.jpg'),
(11, 26, 'images/Rebel_Brown-Jacket.jpg'),
(12, 26, 'images/Rebel_Brown-Jacket-2.jpg'),
(13, 26, 'images/Rebel_Brown-Jacket-3.jpg'),
(14, 27, 'images/blue-cambric1.jpg'),
(15, 27, 'images/blue-cambric2.jpg'),
(16, 27, 'images/blue-cambric3.jpg'),
(17, 27, 'images/blue-cambric4.jpg'),
(18, 28, 'images/men_clasic1.jpg'),
(19, 28, 'images/men_clasic2.jpg'),
(20, 28, 'images/men_clasic3.jpg'),
(21, 28, 'images/men_clasic4.jpg'),
(22, 30, 'images/1764491833_gallery_0_CREAM CASUAL KAMEEZ SHALWAR2.jpg'),
(23, 30, 'images/1764491833_gallery_1_CREAM CASUAL KAMEEZ SHALWAR3.jpg'),
(25, 30, 'images/1764492267_new_0_1764491833_gallery_2_CREAM CASUAL KAMEEZ SHALWAR4.jpg'),
(26, 30, 'images/1764492267_new_1_1764491833_main_CREAM CASUAL KAMEEZ SHALWAR1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password_hash`, `created_at`) VALUES
(1, 'Hashir Hassan', 'hashirhassan157@gmail.com', '$2y$10$glkTUkmFxg87/lbJpqO5t.HgnZZOauxqDVVz31Ym7TYq3tzr6RuIO', '2025-11-02 19:32:02'),
(2, 'Hooria Gul Hassan', 'hooriaawais196@gmail.com', '$2y$10$OyzCmGpaaDwrhBbRYvvz1.npzgFd.eidfaPD5WO3WPBWAqZjGN.VO', '2025-11-07 19:43:01');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`wishlist_id`, `user_id`, `product_id`, `created_at`) VALUES
(26, 1, 27, '2025-11-26 19:43:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `administrators`
--
ALTER TABLE `administrators`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `shipping_address_id` (`shipping_address_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`wishlist_id`),
  ADD UNIQUE KEY `user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `administrators`
--
ALTER TABLE `administrators`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`shipping_address_id`) REFERENCES `addresses` (`address_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
