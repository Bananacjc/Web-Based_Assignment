-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2024 at 07:03 PM
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
(45, 'EMP-20241226-ko12na', 'Add Product', 'Added new product: French Boule', '2024-12-29 13:46:45'),
(46, 'E0113', 'Delete Category', 'Deleted Category: Premium Bread', '2024-12-30 01:15:41'),
(47, 'E0113', 'Delete Category', 'Deleted Category: Premium Meat', '2024-12-30 01:15:44');

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
('PRO-20241229-wKuUPr', 'Brioche', 'Breads', 2.5, 'THis is brioche', 48, 5, '6770e207345bb.png', 'AVAILABLE'),
('PRO-20241230-0Cx2rK', 'Mango', 'Fruits', 7, 'A juicy, tropical fruit with a sweet, tangy flavor and smooth, golden-orange flesh.', 15, 0, '6771878447fa4.png', 'AVAILABLE'),
('PRO-20241230-1M3EWa', 'Watermelon', 'Fruits', 6, 'A large, juicy fruit with a high water content, making it perfect for hydration. Its sweet, refreshing flavor is great for snacking on hot days.', 45, 0, '6771884c28548.png', 'AVAILABLE'),
('PRO-20241230-3eDi2L', 'Ciabatta', 'Breads', 7, 'An Italian rustic bread with a crispy crust and soft, airy interior.', 15, 0, '67718540d655a.png', 'AVAILABLE'),
('PRO-20241230-4AkiBw', 'Pumpkin', 'Vegetables', 10, 'A versatile, round squash with a smooth orange skin and sweet, slightly earthy flavor.', 20, 0, '67718aaf42145.png', 'AVAILABLE'),
('PRO-20241230-4xed6h', 'Okra', 'Vegetables', 10, 'A green, finger-like vegetable with a slightly ribbed texture, often used in Southern and Mediterranean cuisine.', 50, 0, '67718af45b6a6.png', 'AVAILABLE'),
('PRO-20241230-54nctH', 'Mutton Chop', 'Meat', 80, 'A flavorful cut of meat from the rib or shoulder of an older sheep (mutton), known for its rich, robust taste.', 15, 0, '67718a5cd93c4.png', 'AVAILABLE'),
('PRO-20241230-5Zlmx3', 'Pork Chop', 'Meat', 80, 'A tender, flavorful cut of pork, typically taken from the loin or rib section.', 15, 0, '67718a1b7e1ed.png', 'AVAILABLE'),
('PRO-20241230-6egF5z', 'Rye Bread', 'Breads', 15, 'A dense, hearty bread made with rye flour, offering a distinct earthy flavor and slightly darker color', 50, 0, '677185ff78c2c.png', 'AVAILABLE'),
('PRO-20241230-6i3qV4', 'Kiwifuit Juice', 'Juices', 15, 'A tangy and refreshing beverage made by extracting the juice from ripe kiwifruits.', 15, 0, '67718cadbb89b.png', 'AVAILABLE'),
('PRO-20241230-7Z14lS', 'Sparkling Water', 'Cold Drinks', 10, 'A type of water that has been carbonated, giving it a fizzy, effervescent quality.', 20, 0, '67718e4906e04.png', 'AVAILABLE'),
('PRO-20241230-8tQx1q', 'Grapes', 'Fruits', 40, 'Small, sweet, and juicy fruits that come in clusters.', 50, 0, '6771882b37006.png', 'AVAILABLE'),
('PRO-20241230-bas2Np', 'Baguette', 'Breads', 10, 'A classic French bread with a long, thin shape, featuring a golden, crispy crust and a soft, airy interior.', 30, 0, '67718632c0787.png', 'AVAILABLE'),
('PRO-20241230-bNGw5R', 'Coca-cola', 'Cold Drinks', 3, 'A popular carbonated soft drink known for its sweet, refreshing taste with a unique blend of flavors, including hints of vanilla, caramel, and citrus.', 100, 0, '67718dc49b91b.png', 'AVAILABLE'),
('PRO-20241230-boZzl5', 'Blueberry', 'Fruits', 15, 'A small, round berry with a sweet-tart flavor, packed with antioxidants. Perfect for smoothies, desserts, or eating fresh.', 25, 0, '677188d1e0d01.png', 'AVAILABLE'),
('PRO-20241230-cnK5zW', 'Apple', 'Fruits', 4, 'A crisp, sweet, or tart fruit with a crunchy texture, available in various colors like red, green, and yellow. Perfect for snacking or adding to salads.', 100, 0, '677188fd91f47.png', 'AVAILABLE'),
('PRO-20241230-DJVRrj', 'Beet Juice', 'Fruits', 15, 'A vibrant, earthy beverage made by juicing fresh beets. Known for its deep red color, beet juice has a slightly sweet and earthy flavor.', 12, 0, '67718d5402bce.png', 'AVAILABLE'),
('PRO-20241230-dlU4pJ', 'Mango Juice', 'Juices', 12, 'A sweet, tropical beverage made from ripe mangoes, known for its rich and fruity flavor.', 30, 0, '67718c7b7f843.png', 'AVAILABLE'),
('PRO-20241230-DQFeS4', 'Strawberry', 'Fruits', 30, 'A juicy, red berry with a sweet, slightly tart flavor. Often used in desserts, salads, or eaten fresh.', 50, 0, '677187f96cf58.png', 'AVAILABLE'),
('PRO-20241230-Ef4g2p', 'Sourdough Bread', 'Breads', 20, 'A tangy, flavorful bread made with a naturally fermented starter, giving it a distinctive taste and chewy texture.', 20, 0, '677185cb57606.png', 'AVAILABLE'),
('PRO-20241230-IvqDdb', 'Pomegranate', 'Fruits', 20, 'A fruit with a tough outer rind and numerous jewel-like seeds. Known for its sweet-tart flavor, it’s perfect for snacking or adding to salads.', 50, 0, '677188a9a0868.png', 'AVAILABLE'),
('PRO-20241230-J9arQe', 'Radish', 'Fruits', 12, 'A small, round root vegetable with a crisp texture and a peppery, slightly spicy flavor', 45, 0, '67718ba09191b.png', 'AVAILABLE'),
('PRO-20241230-JuMWkI', 'Sports Drink', 'Cold Drinks', 6, 'A beverage designed to hydrate and replenish electrolytes lost during physical activity.', 108, 0, '67718e0c6612d.png', 'AVAILABLE'),
('PRO-20241230-Kn4Sqe', 'Prosciutto', 'Meat', 80, 'A type of Italian dry-cured ham, typically served thinly sliced.', 10, 0, '677189e485838.png', 'AVAILABLE'),
('PRO-20241230-MUFdWt', 'Pineapple Juice', 'Juices', 10, 'A sweet, tangy, and refreshing beverage made by extracting the juice from fresh pineapple.', 13, 0, '67718c55ee52e.png', 'AVAILABLE'),
('PRO-20241230-n6vSjg', 'Steak', 'Meat', 130, 'A popular cut of beef, known for its rich flavor and tender texture.', 20, 0, '677189b09ba94.png', 'AVAILABLE'),
('PRO-20241230-NAngjm', 'Blackcurrant Juice', 'Juices', 17, 'A rich, tangy beverage made from fresh or concentrated blackcurrants.', 16, 0, '67718d24824ff.png', 'AVAILABLE'),
('PRO-20241230-qIjDgv', 'Cucumber Juice', 'Juices', 7, 'A light, refreshing beverage made by blending fresh cucumber with water or ice', 15, 0, '67718c2bbe584.png', 'AVAILABLE'),
('PRO-20241230-QO9l4I', 'Banana', 'Fruits', 5, 'A sweet, tropical fruit with a soft, creamy texture and a bright yellow peel when ripe.', 30, 0, '67718733d5384.png', 'AVAILABLE'),
('PRO-20241230-r3oc6M', 'Melon juice', 'Juices', 10, 'A refreshing beverage made by blending ripe melon, typically watermelon, cantaloupe, or honeydew, with water or ice.', 15, 0, '67718bdab9b77.png', 'AVAILABLE'),
('PRO-20241230-rljgdx', 'Croissant', 'Breads', 10, 'A buttery, flaky pastry with a golden, crispy exterior and soft, airy interior.', 20, 0, '677184bd8ac6a.png', 'AVAILABLE'),
('PRO-20241230-s4CmTM', 'Energy Drink', 'Cold Drinks', 13, 'A beverage designed to boost energy and alertness, typically containing caffeine, sugar, and other ingredients like taurine, B-vitamins, and amino acids.', 50, 0, '67718eae46309.png', 'AVAILABLE'),
('PRO-20241230-sAizZL', 'Pineapple', 'Fruits', 10, 'A tropical fruit with a tangy, refreshing taste and juicy texture.', 100, 0, '677187cc93911.png', 'AVAILABLE'),
('PRO-20241230-sj3Mbm', 'Preztal', 'Breads', 5, 'A dense, chewy bread with a deep brown, crispy crust and a distinctive knot shape.', 30, 0, '677184ff118d8.png', 'AVAILABLE'),
('PRO-20241230-uj1G3F', 'Green Capsium', 'Vegetables', 10, 'A mild, slightly bitter-flavored vegetable that is part of the nightshade family.', 30, 0, '67718b68e6cef.png', 'AVAILABLE'),
('PRO-20241230-ujLASI', 'Avocado', 'Fruits', 5, 'A creamy, nutrient-packed fruit with a rich, buttery texture and mild flavor.', 20, 0, '677187076837b.png', 'AVAILABLE'),
('PRO-20241230-VPua6o', 'Passion fuits', 'Fruits', 10, 'A small, round fruit with a tough outer rind and vibrant, seed-filled interior.', 45, 0, '6771892c2cfcc.png', 'AVAILABLE'),
('PRO-20241230-xbTzr6', 'Apple Gourd', 'Vegetables', 7, 'known as turkey berry or round gourd, the apple gourd is a small, round, green vegetable with a slightly bumpy texture and mild, slightly bitter flavor.', 50, 0, '67718b378d782.png', 'AVAILABLE'),
('PRO-20241230-XICQ30', 'Coconut Juice', 'Juices', 10, 'A refreshing, naturally sweet beverage extracted from the clear liquid inside young, green coconuts.', 30, 0, '67718d81cbd89.png', 'AVAILABLE'),
('PRO-20241230-Y6LblI', 'Beef Roast', 'Meat', 100, 'A tender, flavorful cut of beef, typically slow-cooked to bring out its rich taste and juicy texture.', 10, 0, '6771897caf11d.png', 'AVAILABLE'),
('PRO-20241230-yULwnQ', 'Pita Bread', 'Breads', 7, 'A soft, round flatbread with a pocket, perfect for stuffing with fillings like falafel, shawarma, or salads.', 20, 0, '6771857d9fa90.png', 'AVAILABLE');

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
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

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
