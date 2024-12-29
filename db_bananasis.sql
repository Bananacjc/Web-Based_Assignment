-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 26, 2024 at 04:50 PM
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
-- Database: `db_bananasis`
--

-- --------------------------------------------------------

--
-- Table structure for table `actionlogs`
--

CREATE TABLE `actionlogs` (
  `log_id` int(11) NOT NULL,
  `employee_id` varchar(19) NOT NULL,
  `action` text NOT NULL,
  `product_id` varchar(19) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_name` varchar(255) NOT NULL,
  `category_image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_name`, `category_image`) VALUES
('Breads', '676d76e845209.jpg'),
('Fruits', '676d7687b37f7.jpg'),
('Vegetables', '676d77c3627cf.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` varchar(19) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_num` varchar(19) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `banks` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `addresses` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `cart` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cart`)),
  `promotion_records` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`promotion_records`)),
  `profile_image` varchar(255) DEFAULT NULL,
  `banned` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `username`, `email`, `contact_num`, `password`, `remember_token`, `banks`, `addresses`, `cart`, `promotion_records`, `profile_image`, `banned`) VALUES
('CUS-20241226-HScE4N', 'tanjc', 'tanjeecheng1016@gmail.com', '', '$2y$10$OnNzCxds5mgZtQ63YthdKO7VeJk3Mueu27.TUo6WhT62jXdOfbCom', '33effdb051c253eb885fca5f2ef48684cfd7746bbeb7593bc62fcfeb97bd6037', NULL, NULL, NULL, NULL, 'guest.png', 0);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` varchar(19) NOT NULL,
  `employee_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` enum('MANAGER','STAFF','DELIVERY_GUY') NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `banned` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `employee_name`, `password`, `email`, `role`, `profile_image`, `banned`) VALUES
('EMP-20241226-ko12na', 'admin', '664819d8c5343676c9225b5ed00a5cdc6f3a1ff3', 'admin@gmail.com', 'MANAGER', NULL, 0),
('EMP-20241226-s5U0Zw', 'DeliveryGuy', '0a566818cd05e2c3726677a95ec1dc8657d5cc51', 'deliveryguy@gmail.com', 'DELIVERY_GUY', '676d7ae93e125.jpg', 0),
('EMP-20241226-TWykLg', 'Staff1', '62d1677a9acbd298db0f7a906848c68cebcb154b', 'staff1@gmail.com', 'STAFF', '676d7b44324d8.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` varchar(19) NOT NULL,
  `customer_id` varchar(19) NOT NULL,
  `order_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`order_items`)),
  `promo_amount` float DEFAULT NULL,
  `subtotal` float NOT NULL,
  `shipping_fee` float NOT NULL,
  `total` float NOT NULL,
  `payment_method` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payment_method`)),
  `order_time` datetime NOT NULL,
  `status` enum('PAID','SHIPPING','DELIVERED') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` varchar(19) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `price` float NOT NULL,
  `description` varchar(255) NOT NULL,
  `current_stock` int(11) NOT NULL,
  `amount_sold` int(11) NOT NULL,
  `product_image` varchar(255) NOT NULL,
  `status` enum('AVAILABLE','UNAVAILABLE','OUT_OF_STOCK') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `category_name`, `price`, `description`, `current_stock`, `amount_sold`, `product_image`, `status`) VALUES
('PRO-20241226-3srUTR', 'Brioche', 'Breads', 3.29, 'Soft and buttery brioche with a rich, fluffy texture and a hint of sweetness. Perfect for sandwiches, toast, or enjoying on its own as a delightful treat!', 0, 0, '676d79df1ac78.jpg', 'OUT_OF_STOCK'),
('PRO-20241226-cSEwhZ', 'Banana', 'Fruits', 4.99, 'Sweet and creamy bananas, rich in potassium and essential nutrients. Perfect for snacking, smoothies, or baking. Enjoy their natural energy boost anytime!', 912, 0, '676d774282332.jpg', 'AVAILABLE'),
('PRO-20241226-i5HA8g', 'Carrot', 'Vegetables', 1.99, 'Fresh, crunchy carrots packed with vitamins and a naturally sweet flavor. Perfect for snacking, cooking, or juicing. A healthy choice for every meal!', 129, 0, '676d77c36cc96.jpg', 'AVAILABLE'),
('PRO-20241226-rWEiPT', 'Bagel', 'Breads', 4.29, 'Soft and chewy bagels baked to perfection, with a golden crust and a delightful texture. Perfect for breakfast or snacks, pair them with cream cheese, jam, or your favorite toppings. Available in plain, sesame, and everything flavors!', 42, 0, '676d76e8494e5.jpg', 'AVAILABLE'),
('PRO-20241226-sk9VcT', 'Apple', 'Fruits', 1.39, 'Juicy and crisp, our fresh red apples are the perfect blend of sweetness and tartness.', 292, 0, '676d7687b9b73.jpg', 'AVAILABLE');

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `promo_id` varchar(19) NOT NULL,
  `promo_name` varchar(255) NOT NULL,
  `promo_code` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `requirement` float NOT NULL,
  `promo_amount` float NOT NULL,
  `limit_usage` int(11) NOT NULL,
  `promo_image` varchar(255) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('AVAILABLE','UNAVAILABLE') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` varchar(19) NOT NULL,
  `customer_id` varchar(19) NOT NULL,
  `product_id` varchar(19) NOT NULL,
  `rating` float NOT NULL,
  `comment` varchar(255) NOT NULL,
  `review_image` varchar(255) NOT NULL,
  `comment_date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `actionlogs`
--
ALTER TABLE `actionlogs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_name`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_name` (`category_name`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`promo_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `actionlogs`
--
ALTER TABLE `actionlogs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `actionlogs`
--
ALTER TABLE `actionlogs`
  ADD CONSTRAINT `fk_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `fk_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_name`) REFERENCES `categories` (`category_name`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
