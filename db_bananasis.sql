-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2024 at 06:50 AM
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
-- Table structure for table `actionlog`
--

CREATE TABLE `actionlog` (
  `log_id` int(11) NOT NULL,
  `employee_id` varchar(19) NOT NULL,
  `action_type` varchar(255) DEFAULT NULL,
  `action_details` text DEFAULT NULL,
  `action_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `actionlog`
--

INSERT INTO `actionlog` (`log_id`, `employee_id`, `action_type`, `action_details`, `action_date`) VALUES
(27, 'EMP-20241226-ko12na', 'Delete Category', 'Deleted Category: Breads', '2024-12-29 13:33:52'),
(28, 'EMP-20241226-ko12na', 'Delete Category', 'Deleted Category: Cold Drinks', '2024-12-29 13:33:56'),
(29, 'EMP-20241226-ko12na', 'Delete Category', 'Deleted Category: Fruits', '2024-12-29 13:33:58'),
(30, 'EMP-20241226-ko12na', 'Delete Category', 'Deleted Category: Juice', '2024-12-29 13:34:01'),
(31, 'EMP-20241226-ko12na', 'Delete Category', 'Deleted Category: Meat', '2024-12-29 13:34:03'),
(32, 'EMP-20241226-ko12na', 'Delete Category', 'Deleted Category: Premium Bread', '2024-12-29 13:34:05'),
(33, 'EMP-20241226-ko12na', 'Delete Category', 'Deleted Category: Vegetables', '2024-12-29 13:34:07'),
(34, 'EMP-20241226-ko12na', 'Add Category', 'Added new category: Breads', '2024-12-29 13:34:21'),
(35, 'EMP-20241226-ko12na', 'Add Category', 'Added new category: Fruits', '2024-12-29 13:34:29'),
(36, 'EMP-20241226-ko12na', 'Add Category', 'Added new category: Vegetables', '2024-12-29 13:34:41'),
(37, 'EMP-20241226-ko12na', 'Add Category', 'Added new category: Juices', '2024-12-29 13:34:57'),
(38, 'EMP-20241226-ko12na', 'Add Category', 'Added new category: Meat', '2024-12-29 13:35:08'),
(39, 'EMP-20241226-ko12na', 'Add Category', 'Added new category: Cold Drinks', '2024-12-29 13:35:18'),
(40, 'EMP-20241226-ko12na', 'Add Category', 'Added new category: Premium Meat', '2024-12-29 13:35:39'),
(41, 'EMP-20241226-ko12na', 'Add Category', 'Added new category: Premium Bread', '2024-12-29 13:36:12'),
(42, 'EMP-20241226-ko12na', 'Add Product', 'Added new product: Bagel', '2024-12-29 13:37:44'),
(43, 'EMP-20241226-ko12na', 'Add Product', 'Added new product: Brioche', '2024-12-29 13:45:43'),
(44, 'EMP-20241226-ko12na', 'Add Product', 'Added new product: Baguette Sliced', '2024-12-29 13:46:20'),
(45, 'EMP-20241226-ko12na', 'Add Product', 'Added new product: French Boule', '2024-12-29 13:46:45');

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
('Breads', '6770df5d36477.png'),
('Cold Drinks', '6770df962b17a.png'),
('Fruits', '6770df65bb381.png'),
('Juices', '6770df8165182.png'),
('Meat', '6770df8cbe58a.png'),
('Premium Bread', '6770dfcba1c2f.png'),
('Premium Meat', '6770dfab72af7.png'),
('Vegetables', '6770df71b6049.png');

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
('CUS-20241227-JpYRow', 'tanjc', 'tanjc-wm22@student.tarc.edu.my', '0116392048', 'c19859bd96b5cbd25a75bab18b3ef4b89128183b', '8b138a2854952edd957d0c5e659fd64c1f94f3c2b69d7884b06783bd5f00641e', '[{\"accNum\":\"4242424242424242\",\"cvv\":\"123\",\"expiry\":\"2024-05\"},{\"accNum\":\"1234123412341234\",\"cvv\":\"321\",\"expiry\":\"2024-02\"},{\"accNum\":\"1231231231231234\",\"cvv\":\"123\",\"expiry\":\"2024-06\"}]', '[{\"line_1\":\"34, Jalan Merbau\",\"village\":\"Taman Saga\",\"postal_code\":\"28400\",\"city\":\"Mentakab\",\"state\":\"Pahang\"},{\"line_1\":\"20, Jalan Merbau\",\"village\":\"Taman Saga\",\"postal_code\":\"28400\",\"city\":\"Mentakab\",\"state\":\"Pahang\"},{\"line_1\":\"20 Jalan 34D\\/38A\",\"village\":\"Taman Sri Bintang\",\"postal_code\":\"52100\",\"city\":\"Kuala Lumpur\",\"state\":\"Wilayah Persekutuan Kuala Lumpur\"},{\"line_1\":\"1 Lebuh Bandar Utama\",\"village\":\"Bandar Utama\",\"postal_code\":\"47800\",\"city\":\"Petaling Jaya\",\"state\":\"Selangor\"}]', '{\"PRO-20241229-eT5XQc\":1,\"PRO-20241229-w5jZ3K\":4,\"PRO-20241229-wKuUPr\":5,\"PRO-20241229-3plhzN\":1}', '{\"PROMO-20241229-Nq4n\":{\"promoLimit\":\"3\"}}', '6770e1ca9530f.png', 0),
('CUS-20241229-j9rziM', 'user', 'tanjeecheng1016@gmail.com', '01163920123', '383e4fcf7c6757b4a12b320bbaf7ae0b79402529', NULL, '[{\"accNum\":\"8989121234345656\",\"cvv\":\"123\",\"expiry\":\"2024-02\"}]', '[{\"line_1\":\"5-4 Persiaran Pertahanan\",\"village\":\"Taman Melati\",\"postal_code\":\"53100\",\"city\":\"Kuala Lumpur\",\"state\":\"Wilayah Persekutuan Kuala Lumpur\"}]', NULL, NULL, 'guest.png', 0);

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

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `order_items`, `promo_amount`, `subtotal`, `shipping_fee`, `total`, `payment_method`, `order_time`, `status`) VALUES
('ORD-20241229-WMf70a', 'CUS-20241227-JpYRow', '{\"PRO-20241229-eT5XQc\":1,\"PRO-20241229-w5jZ3K\":4,\"PRO-20241229-wKuUPr\":5,\"PRO-20241229-3plhzN\":1}', 0, 27.5, 109, 136.5, '\"other\"', '2024-12-29 13:49:21', 'PAID');

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
('PRO-20241229-3plhzN', 'Bagel', 'Breads', 2.5, 'Bagels are a popular bread product known for their dense, chewy texture and distinctive ring shape. In Malaysia, the price of bagels varies depending on the bakery, flavor, and whether they are purchased individually or in sets.', 52, 1, '6770e02883bb2.png', 'AVAILABLE'),
('PRO-20241229-eT5XQc', 'Baguette Sliced', 'Breads', 2.5, 'Slice baguette is good', 40, 1, '6770e22c47963.png', 'AVAILABLE'),
('PRO-20241229-w5jZ3K', 'French Boule', 'Breads', 2.5, 'FRENCHHHH Boule', 37, 4, '6770e244f38f9.png', 'AVAILABLE'),
('PRO-20241229-wKuUPr', 'Brioche', 'Breads', 2.5, 'THis is brioche', 48, 5, '6770e207345bb.png', 'AVAILABLE');

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
  `promo_image` varchar(255) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('AVAILABLE','UNAVAILABLE') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`promo_id`, `promo_name`, `promo_code`, `description`, `requirement`, `promo_amount`, `limit_usage`, `promo_image`, `start_date`, `end_date`, `status`) VALUES
('PROMO-20241229-Nq4n', 'BANANASIS EOY 2024', 'BANANASISEOY2024', 'End of the Year is near; Well miss the year weve spent with you.', 30, 10, 3, '6770df964a9e1.png', '2024-12-01 00:00:00', '2025-01-15 00:00:00', 'AVAILABLE'),
('PROMO-20241229-NZhM', 'BANANASIS DAY 2024', 'BANANASIS2024', 'BANANASIS is celebrating our 2nd year anniversary', 50, 20, 1, '6770dda5ebb8d.png', '2024-11-01 13:26:00', '2024-11-30 13:26:00', 'AVAILABLE'),
('PROMO-20241229-rQWi', 'CNY Promotion', 'BANANASISCNY2025', 'CNY 2025 is coming soon, and BANANASIS is ready to celebrate it!', 100, 45, 1, '6770dedc6f4fe.png', '2025-02-01 00:00:00', '2025-02-28 00:00:00', 'AVAILABLE');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` varchar(19) NOT NULL,
  `customer_id` varchar(19) NOT NULL,
  `product_id` varchar(19) NOT NULL,
  `rating` float NOT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `review_image` varchar(255) DEFAULT NULL,
  `comment_date_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `actionlog`
--
ALTER TABLE `actionlog`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `employee_id` (`employee_id`);

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
-- AUTO_INCREMENT for table `actionlog`
--
ALTER TABLE `actionlog`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `actionlog`
--
ALTER TABLE `actionlog`
  ADD CONSTRAINT `actionlog_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`);

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
