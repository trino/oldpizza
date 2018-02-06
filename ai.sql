-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 06, 2018 at 07:41 PM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ai`
--

-- --------------------------------------------------------

--
-- Table structure for table `actions`
--

CREATE TABLE `actions` (
  `id` int(11) NOT NULL,
  `eventname` varchar(64) NOT NULL,
  `party` tinyint(4) NOT NULL COMMENT '0=user,1=admin,2=restaurant',
  `sms` tinyint(1) NOT NULL,
  `phone` tinyint(1) NOT NULL,
  `email` tinyint(1) NOT NULL,
  `message` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `actions`
--

INSERT INTO `actions` (`id`, `eventname`, `party`, `sms`, `phone`, `email`, `message`) VALUES
(1, 'order_placed', 2, 0, 0, 1, '[sitename] - A new order was placed'),
(2, 'order_placed', 1, 1, 0, 0, '[sitename] - A new order was placed [url]'),
(3, 'order_placed', 0, 0, 0, 1, '[sitename] - Here is your receipt'),
(4, 'order_declined', 0, 1, 0, 1, '[sitename] - Your order was cancelled: [reason]'),
(5, 'order_declined', 1, 1, 0, 0, '[sitename] - An order was cancelled: [reason]'),
(6, 'order_confirmed', 1, 1, 0, 0, '[sitename] - An order was approved: [reason]'),
(7, 'user_registered', 0, 0, 0, 1, '[sitename] - Thank you for registering'),
(9, 'user_registered', 1, 0, 0, 1, '[sitename] - Thank you for registering'),
(10, 'order_placed', 2, 1, 0, 0, '[sitename] - A new order was placed [url]'),
(11, 'order_placed', 2, 0, 1, 0, 'Hello [name], you have an order on [sitename]');

-- --------------------------------------------------------

--
-- Table structure for table `additional_toppings`
--

CREATE TABLE `additional_toppings` (
  `id` int(10) UNSIGNED NOT NULL,
  `size` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `additional_toppings`
--

INSERT INTO `additional_toppings` (`id`, `size`, `price`) VALUES
(1, 'Small', 0.95),
(2, 'Medium', 1.2),
(3, 'Large', 1.5),
(4, 'X-Large', 1.7),
(6, 'Panzerotti', 0.95),
(7, 'Delivery', 3),
(8, 'Minimum', 15),
(10, 'DeliveryTime', 45);

-- --------------------------------------------------------

--
-- Table structure for table `combos`
--

CREATE TABLE `combos` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `baseprice` decimal(6,2) NOT NULL,
  `item_ids` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `combos`
--

INSERT INTO `combos` (`id`, `name`, `baseprice`, `item_ids`) VALUES
(1, 'Testing', '9.90', '1,1');

-- --------------------------------------------------------

--
-- Table structure for table `hours`
--

CREATE TABLE `hours` (
  `restaurant_id` int(11) NOT NULL,
  `0_open` smallint(6) NOT NULL,
  `0_close` smallint(6) NOT NULL,
  `1_open` smallint(6) NOT NULL,
  `1_close` smallint(6) NOT NULL,
  `2_open` smallint(6) NOT NULL,
  `2_close` smallint(6) NOT NULL,
  `3_open` smallint(6) NOT NULL,
  `3_close` smallint(6) NOT NULL,
  `4_open` smallint(6) NOT NULL,
  `4_close` smallint(6) NOT NULL,
  `5_open` smallint(6) NOT NULL,
  `5_close` smallint(6) NOT NULL,
  `6_open` smallint(6) NOT NULL,
  `6_close` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `hours`
--

INSERT INTO `hours` (`restaurant_id`, `0_open`, `0_close`, `1_open`, `1_close`, `2_open`, `2_close`, `3_open`, `3_close`, `4_open`, `4_close`, `5_open`, `5_close`, `6_open`, `6_close`) VALUES
(0, -1, -1, 1100, 2225, 1100, 2225, 1100, 2225, 1100, 2225, 1000, 2350, 1100, 2350),
(4, -1, -1, -1, -1, 1500, 2200, 1500, 2200, 1500, 2200, 1500, 2200, 1500, 2200),
(6, 2400, 2200, 2400, 2330, 2400, 2330, 2400, 2330, 2400, 2330, 2400, 1200, 2400, 1200),
(7, 2400, 2300, 1100, 2300, 1100, 2300, 1100, 2300, 1100, 100, 1100, 100, 1100, 100),
(9, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 1200),
(10, 2400, 2100, 1130, 2100, 1130, 2100, 1130, 2100, 1130, 2200, 1130, 2200, 2400, 2200),
(11, 1130, 2100, 1130, 2200, 1130, 2200, 1130, 2200, 1130, 2200, 1130, 2300, 1130, 2300),
(12, 1600, 2300, -1, -1, 1630, 2300, 1630, 2300, 1630, 2300, 1630, 1200, 1600, 1200),
(13, 1500, 200, -1, -1, 1500, 200, 1500, 200, 1500, 200, 1500, 200, 1500, 200),
(14, 2400, 2100, 1100, 2200, 1100, 2200, 1100, 2200, 1100, 2230, 1100, 2230, 2400, 2230),
(15, -1, -1, -1, -1, 1100, 2200, 1100, 2200, 1100, 2200, 1100, 2300, 1100, 2300),
(16, 1600, 2200, 1600, 2200, 1600, 2200, 1600, 2200, 1600, 2200, 1600, 2300, 1600, 2300),
(18, 1100, 2200, 1100, 2200, 1100, 2300, 1100, 2300, 1100, 2300, 1100, 1200, 1100, 1200),
(19, 1100, 2300, 1100, 2300, 1100, 2300, 1100, 2300, 1100, 1200, 1100, 1200, 1100, 1200),
(20, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 200, 1100, 200),
(21, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 200, 1100, 200),
(22, 1100, 2300, 1100, 2300, 1100, 2300, 1100, 1200, 1100, 1200, 1100, 100, 1100, 100),
(23, 1100, 2300, 1100, 2300, 1100, 2300, 1100, 2300, 1100, 2300, 1100, 1200, 1100, 1200),
(24, 2400, 1200, 1100, 200, 1100, 200, 1100, 200, 1100, 200, 1100, 300, 1100, 300),
(25, 1300, 2000, 1100, 2300, 1100, 2300, 1100, 2300, 1100, 2300, 1100, 2300, 1100, 2300),
(26, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 100, 1100, 300, 1100, 300),
(27, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 200, 1100, 300, 1100, 300),
(28, 2400, 2300, 1030, 2300, 1030, 2300, 1030, 2300, 1030, 2300, 900, 100, 1100, 2300),
(29, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 300, 1100, 300, 1100, 300),
(30, -1, -1, 1100, 2200, 1100, 2200, 1100, 2200, 1100, 2200, 1100, 1200, 1100, 1200),
(31, 1100, 2230, 1100, 2230, 1100, 2230, 1100, 2230, 1100, 2230, 1100, 1200, 1100, 1200),
(32, 1500, 2300, 1500, 1200, 1500, 1200, 1500, 1200, 1500, 1200, 1500, 100, 1500, 100),
(33, 1500, 2300, 1100, 200, 1100, 200, 1100, 200, 1100, 200, 1100, 200, 1100, 300),
(34, 1700, 2200, -1, -1, 1700, 2200, 2400, 2200, 2400, 2200, 2400, 2200, 1700, 2200),
(35, 1100, 2300, 1100, 2300, 1100, 2300, 1100, 2300, 1100, 2300, 1100, 1200, 1100, 1200),
(36, 1030, 100, 1030, 100, 1030, 100, 1030, 100, 1030, 100, 1030, 100, 1030, 100),
(38, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 1200, 1100, 200, 1100, 200),
(39, 1030, 1100, 1030, 2300, 1030, 2300, 1030, 2300, 1030, 100, 1030, 100, 1030, 100),
(40, 1500, 2300, 1500, 100, 1500, 100, 1500, 100, 1500, 100, 1500, 200, 1500, 200);

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) NOT NULL,
  `category` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `item` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `toppings` tinyint(1) NOT NULL,
  `wings_sauce` tinyint(4) NOT NULL,
  `calories` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'for 2 items, separate with a /. For more, use a -',
  `allergens` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `category_id`, `category`, `item`, `price`, `toppings`, `wings_sauce`, `calories`, `allergens`, `enabled`) VALUES
(1, 1, 'Pizza', 'Small Pizza', 4.95, 1, 0, '', '', 1),
(2, 1, 'Pizza', 'Medium Pizza', 5.75, 1, 0, '', '', 1),
(3, 1, 'Pizza', 'Large Pizza', 6.95, 1, 0, '', '', 1),
(4, 1, 'Pizza', 'X-Large Pizza', 9.95, 1, 0, '', '', 1),
(5, 1, 'Pizza', '2 Small Pizzas', 9.95, 2, 0, '', '', 1),
(6, 1, 'Pizza', '2 Medium Pizzas', 15.95, 2, 0, '', '', 1),
(7, 1, 'Pizza', '2 Large Pizzas', 17.95, 2, 0, '', '', 1),
(8, 1, 'Pizza', '2 X-Large Pizzas', 19.95, 2, 0, '', '', 1),
(9, 3, 'Dips', 'Tomato Dip', 0.7, 0, 0, '', '', 1),
(10, 3, 'Dips', 'Hot Dip', 0.7, 0, 0, '', '', 1),
(11, 3, 'Dips', 'Cheddar Dip', 0.7, 0, 0, '', '', 1),
(12, 3, 'Dips', 'Marinara Dip', 0.7, 0, 0, '', '', 1),
(13, 3, 'Dips', 'Ranch Dip', 0.7, 0, 0, '', '', 1),
(14, 3, 'Dips', 'Blue Cheese Dip', 0.7, 0, 0, '', '', 1),
(15, 4, 'Wings', '1 lb Wings', 6.99, 0, 1, '', '', 1),
(16, 4, 'Wings', '2 lb Wings', 12.99, 0, 2, '', '', 1),
(17, 4, 'Wings', '3 lb Wings', 17.99, 0, 3, '', '', 1),
(18, 4, 'Wings', '4 lb Wings', 24.99, 0, 4, '', '', 1),
(19, 4, 'Wings', '5 lb Wings', 28.99, 0, 5, '', '', 1),
(20, 5, 'Sides', 'Panzerotti', 5.99, 1, 0, '', '', 1),
(21, 5, 'Sides', 'Garlic Bread', 2.25, 0, 0, '', '', 1),
(22, 5, 'Sides', 'French Fries', 3.99, 0, 0, '', '', 1),
(23, 5, 'Sides', 'Potato Wedges', 3.99, 0, 0, '', '', 1),
(27, 5, 'Sides', 'Chicken Salad ', 5.99, 0, 0, '', '', 1),
(28, 5, 'Sides', 'Caesar Salad', 3.99, 0, 0, '', '', 1),
(29, 5, 'Sides', 'Garden Salad', 3.99, 0, 0, '', '', 1),
(32, 6, 'Drinks', 'Coca-Cola', 0.95, 0, 0, '', '', 1),
(33, 6, 'Drinks', 'Diet Coca-Cola', 0.95, 0, 0, '', '', 1),
(34, 6, 'Drinks', 'Pepsi', 0.95, 0, 0, '', '', 1),
(35, 6, 'Drinks', 'Diet Pepsi', 0.95, 0, 0, '', '', 1),
(36, 6, 'Drinks', 'Sprite', 0.95, 0, 0, '', '', 1),
(37, 6, 'Drinks', 'Crush Orange', 0.95, 0, 0, '', '', 1),
(38, 6, 'Drinks', 'Dr. Pepper', 0.95, 0, 0, '', '', 1),
(39, 6, 'Drinks', 'Ginger Ale', 0.95, 0, 0, '', '', 1),
(40, 6, 'Drinks', 'Nestea', 0.95, 0, 0, '', '', 1),
(41, 6, 'Drinks', 'Water Bottle', 0.95, 0, 0, '', '', 1),
(45, 6, 'Drinks', '2L Coca-Cola', 2.99, 0, 0, '', '', 1),
(46, 6, 'Drinks', '2L Sprite', 2.99, 0, 0, '', '', 1),
(47, 6, 'Drinks', '2L Brisk Iced Tea', 2.99, 0, 0, '', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `placed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `number` int(11) NOT NULL,
  `unit` varchar(16) NOT NULL,
  `buzzcode` varchar(32) NOT NULL,
  `street` varchar(255) NOT NULL,
  `postalcode` varchar(16) NOT NULL,
  `city` varchar(64) NOT NULL,
  `province` varchar(32) NOT NULL,
  `latitude` varchar(16) NOT NULL,
  `longitude` varchar(16) NOT NULL,
  `accepted_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `restaurant_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `payment_type` tinyint(4) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `cell` varchar(16) NOT NULL,
  `paid` tinyint(4) NOT NULL,
  `stripeToken` varchar(64) NOT NULL,
  `deliverytime` varchar(64) NOT NULL,
  `cookingnotes` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `email` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `placed_at`, `number`, `unit`, `buzzcode`, `street`, `postalcode`, `city`, `province`, `latitude`, `longitude`, `accepted_at`, `restaurant_id`, `type`, `payment_type`, `phone`, `cell`, `paid`, `stripeToken`, `deliverytime`, `cookingnotes`, `status`, `price`, `email`) VALUES
(241, 1, '2017-06-07 15:07:06', 300, '123', '', 'Dundas St', 'N6B 1T6', 'London', 'Ontario', '42.9854177', '-81.244139099999', '0000-00-00 00:00:00', 1, 0, 0, '9055315331', '', 1, 'tok_AWVffGXm9uLn37', 'Deliver Now', '', 0, '66.84', ''),
(244, 1, '2017-04-25 14:31:04', 18, 'side door', '', 'Oakland Dr', 'L8E 3Z2', 'Hamilton', 'Ontario', '43.2304400000000', '-79.7693198', '0000-00-00 00:00:00', 3, 0, 0, '9055315331', '', 1, '', 'April 24 at 1100', '', 0, '15.20', ''),
(245, 1, '2017-04-25 14:33:31', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 1, 0, 0, '', '', 1, 'tok_AXdl4E9HUc2Th9', 'Deliver Now', '', 0, '36.15', ''),
(246, 1, '2017-04-25 14:35:59', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 1, 0, 0, '', '', 1, 'tok_AXdnBCIKoVbsDy', 'Deliver Now', '', 0, '36.15', ''),
(247, 1, '2017-04-25 14:39:36', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 1, 0, 0, '9055315331', '', 1, '', 'Deliver Now', '', 0, '36.15', ''),
(248, 1, '2017-04-25 14:47:53', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 1, 0, 0, '9055315331', '', 1, 'tok_AXdzjNM00RsENx', 'Deliver Now', '', 0, '36.15', ''),
(249, 1, '2017-04-26 13:42:26', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 1, 0, 0, '9055315331', '', 1, 'tok_AY0Ar7ZSXPx0lB', 'Deliver Now', '', 0, '36.15', ''),
(250, 1, '2017-05-02 09:44:52', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 1, 0, 0, '9055315331', '', 1, '', 'May 2 at 1100', '', 0, '36.15', ''),
(251, 1, '2017-05-02 10:21:58', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 1, 0, 0, '9055315331', '', 1, 'tok_AaCHA085DdmEOd', 'May 2 at 1115', '', 0, '36.15', ''),
(252, 1, '2017-05-02 10:21:58', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 1, 0, 0, '9055315331', '', 1, 'tok_AaCHqSPACG0gmV', 'May 2 at 1115', '', 0, '36.15', ''),
(253, 1, '2017-05-02 10:25:03', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 1, 0, 0, '9055315331', '', 1, '', 'May 2 at 1115', '', 0, '36.15', ''),
(254, 1, '2017-05-02 11:51:51', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 1, 0, 0, '9055315331', '', 1, '', 'Deliver Now', '', 0, '20.57', ''),
(255, 1, '2017-05-03 10:04:41', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 1, 0, 0, '9055123067', '', 1, '', 'May 3 at 1100', '', 0, '21.64', ''),
(256, 1, '2017-05-23 14:20:42', 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', '0000-00-00 00:00:00', 1, 0, 0, '9055123067', '', 1, '', 'May 23 at 1115', '', 0, '36.15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `presets`
--

CREATE TABLE `presets` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `toppings` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `presets`
--

INSERT INTO `presets` (`id`, `name`, `toppings`) VALUES
(1, 'hawaiian', 'pineapple bacon ham'),
(2, 'canadian', 'pepperoni mushrooms bacon'),
(3, 'deluxe', 'pepperoni mushrooms green peppers'),
(4, 'vegetarian', 'mushrooms tomatoes green peppers'),
(5, 'meat', 'sausage salami bacon pepperoni'),
(6, 'super', 'pepperoni mushrooms green peppers'),
(7, 'supreme', 'pepperoni mushrooms green peppers');

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `cuisine` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_delivery` int(11) NOT NULL,
  `is_pickup` int(11) NOT NULL,
  `max_delivery_distance` int(11) NOT NULL,
  `delivery_fee` int(11) NOT NULL,
  `minimum` int(11) NOT NULL,
  `is_complete` int(11) NOT NULL,
  `lastorder_id` int(11) NOT NULL,
  `franchise` int(11) NOT NULL,
  `address_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `slug`, `email`, `phone`, `cuisine`, `website`, `description`, `logo`, `is_delivery`, `is_pickup`, `max_delivery_distance`, `delivery_fee`, `minimum`, `is_complete`, `lastorder_id`, `franchise`, `address_id`) VALUES
(1, 'Fabulous 2 for 1 Pizza', '', '(905) 512-3067', '9055315331', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 1),
(2, 'INACTIVE Marvellous Pizza', '', '(519) 452-1044', '9055315331', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 2),
(3, 'Quality Pizza & Wings', '', '', '9055315331', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 97),
(4, 'BBQ pizza', '', 'info+BBQpizza@trinoweb.com', '(905) 544-2828', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 106),
(5, 'Double Double Pizza & Chicken Inc', '', 'info+DoubleDoublePizzaChickenInc@trinoweb.com', '(905) 528-0000', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 107),
(6, 'Royal Pizza & Wings', '', 'info+RoyalPizzaWings@trinoweb.com', '(905) 529-4242', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 108),
(7, 'Royal Pizza & Wings', '', 'info+RoyalPizzaWings2@trinoweb.com', '(905) 664-8075', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 109),
(9, 'City Pizza & Wings', '', 'info+CityPizzaWings@trinoweb.com', '(905) 528-1111', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 111),
(10, 'The Express Restaurant', '', 'info+TheExpressRestaurant@trinoweb.com', '(905) 560-1475', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 112),
(11, 'The Express Restaurant', '', 'info+TheExpressRestaurant1@trinoweb.com', '(289) 389-2008', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 113),
(12, 'Chicago Style Pizza', '', 'info+ChicagoStylePizza@trinoweb.com', '(905) 575-8800', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 114),
(13, 'Doors Pub: Taco Joint & Metal Bar', '', 'info+DoorsPubTacoJointMetalBar@trinoweb.com', '(905) 540-8888', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 115),
(14, 'Bronzie\'s Place', '', 'info+BronziesPlace@trinoweb.com', '(905) 529-3403', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 116),
(15, 'Alfredo\'s Place', '', 'info+AlfredosPlace@trinoweb.com', '(905) 383-3555', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 117),
(17, 'Star Pizza', '', 'info+StarPizza@trinoweb.com', '(905) 664-4444', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 119),
(18, 'Red Rockets', '', 'info+RedRockets@trinoweb.com', '(905) 318-9555', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 120),
(19, 'Mattina Pizzeria', '', 'info+MattinaPizzeria@trinoweb.com', '(905) 527-7918', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 121),
(20, 'National Pizza & Wings', '', 'info+NationalPizzaWings@trinoweb.com', '(905) 549-7734', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 122),
(21, 'National Pizza & Wings', '', 'info+NationalPizzaWings1@trinoweb.com', '(905) 575-4500', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 123),
(22, 'Topper\'s Pizza Hamilton - Upper James', '', 'info+ToppersPizzaHamiltonUpperJames@trinoweb.com', '(905) 387-7171', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 124),
(23, 'Rymal Pizza-Wings', '', 'info+RymalPizzaWings@trinoweb.com', '(905) 387-1800', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 125),
(24, 'Hess Village Pizza & Wings', '', 'info+HessVillagePizzaWings@trinoweb.com', '(905) 525-5444', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 126),
(25, 'Diana\'s Pizza & Grille', '', 'info+DianasPizzaGrille@trinoweb.com', '(905) 549-7474', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 127),
(26, 'Niva Pizza & Wings', '', 'info+NivaPizzaWings@trinoweb.com', '(905) 544-4544', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 128),
(27, 'Lava Pizza', '', 'info+LavaPizza@trinoweb.com', '(905) 777-0666', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 129),
(28, 'Hi-Line Pizza & Wings on Parkdale', '', 'info+HiLinePizzaWingsonParkdale@trinoweb.com', '(905) 543-9555', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 130),
(29, 'Lava Pizza & Wings', '', 'info+LavaPizzaWings@trinoweb.com', '(905) 574-6666', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 131),
(30, 'Weston Pizza And Wings', '', 'info+WestonPizzaAndWings@trinoweb.com', '(905) 521-9919', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 132),
(31, 'The Pizza House', '', 'info+ThePizzaHouse@trinoweb.com', '(905) 561-0220', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 133),
(32, 'Bella Pizza', '', 'info+BellaPizza@trinoweb.com', '(905) 544-1444', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 134),
(33, 'Express 2 For 1 Pizza & Wings', '', 'info+Express2For1PizzaWings@trinoweb.com', '(905) 549-7171', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 135),
(34, 'Giuseppe\'s Italian Cuisine & Pizza', '', 'info+GiuseppesItalianCuisinePizza@trinoweb.com', '(905) 525-3334', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 136),
(35, 'Marino Pizza And Wings', '', 'info+MarinoPizzaAndWings@trinoweb.com', '(905) 574-2020', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 137),
(36, 'Twice The Deal Pizza', '', 'info+TwiceTheDealPizza@trinoweb.com', '(905) 385-0505', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 138),
(37, 'A 1 Pizza Place', '', 'info+A1PizzaPlace@trinoweb.com', '(905) 318-5050', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 139),
(38, 'Plaza Pizza & Wings', '', 'info+PlazaPizzaWings@trinoweb.com', '(905) 522-0909', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 140),
(39, 'Pizza Depot', '', 'info+PizzaDepot@trinoweb.com', '(905) 525-9711', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 141),
(40, 'Le Bella Pizza', '', 'info+LeBellaPizza@trinoweb.com', '(905) 529-7627', '', '', '', '', 1, 0, 0, 0, 0, 0, 0, 0, 142);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `keyname` varchar(255) NOT NULL,
  `value` varchar(1024) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `keyname`, `value`) VALUES
(1, 'lastSQL', '1495549190'),
(20, 'orders', '1487775876'),
(24, 'menucache', '1493733784'),
(25, 'useraddresses', '1491932853'),
(37, 'users', '1487175217'),
(38, 'additional_toppings', '1487175322'),
(43, 'actions', '1493735344'),
(87, 'restaurants', '1489588141'),
(1398, 'shortage', '1493135529'),
(1537, 'combos', '1493826841'),
(1552, 'debugmode', '1'),
(1553, 'domenucache', '0'),
(1554, 'settings', '1495034190'),
(1560, 'onlyfiftycents', '1'),
(1567, 'over$20', '10%'),
(1571, 'over$30', '15%'),
(1575, 'over$40', '20%'),
(1579, 'deletetopping', '0'),
(1582, 'localhostdialing', '0'),
(1593, 'maxdistance_live', '5'),
(1594, 'maxdistance_local', '20');

-- --------------------------------------------------------

--
-- Table structure for table `shortage`
--

CREATE TABLE `shortage` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `tablename` varchar(255) NOT NULL,
  `item_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `toppings`
--

CREATE TABLE `toppings` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `isfree` tinyint(1) NOT NULL,
  `qualifiers` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'comma delimited list of the names for 1/2,x1,x2 if applicable',
  `isall` tinyint(4) NOT NULL DEFAULT '0',
  `groupid` int(11) NOT NULL,
  `enabled` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `toppings`
--

INSERT INTO `toppings` (`id`, `name`, `type`, `isfree`, `qualifiers`, `isall`, `groupid`, `enabled`) VALUES
(1, 'Anchovies', 'Meat', 0, '', 0, 0, 0),
(2, 'Bacon', 'Meat', 0, '', 0, 0, 1),
(3, 'Beef Salami', 'Meat', 0, '', 0, 0, 1),
(4, 'Chicken', 'Meat', 0, '', 0, 0, 1),
(5, 'Ground Beef', 'Meat', 0, '', 0, 0, 1),
(6, 'Ham', 'Meat', 0, '', 0, 0, 1),
(7, 'Hot Italian Sausage', 'Meat', 0, '', 0, 0, 1),
(8, 'Hot Sausage', 'Meat', 0, '', 0, 0, 1),
(9, 'Italian Sausage', 'Meat', 0, '', 0, 0, 1),
(10, 'Mild Sausage', 'Meat', 0, '', 0, 0, 1),
(11, 'Pepperoni', 'Meat', 0, '', 0, 0, 1),
(12, 'Salami', 'Meat', 0, '', 0, 0, 1),
(13, 'Artichoke Heart', 'Vegetable', 0, '', 0, 0, 1),
(14, 'Black Olives', 'Vegetable', 0, '', 0, 0, 1),
(15, 'Broccoli', 'Vegetable', 0, '', 0, 0, 1),
(16, 'Green Olives', 'Vegetable', 0, '', 0, 0, 1),
(17, 'Green Peppers', 'Vegetable', 0, '', 0, 0, 1),
(18, 'Hot Banana Peppers', 'Vegetable', 0, '', 0, 0, 1),
(19, 'Hot Peppers', 'Vegetable', 0, '', 0, 0, 1),
(20, 'Jalapeno Peppers', 'Vegetable', 0, '', 0, 0, 1),
(21, 'Mushrooms', 'Vegetable', 0, '', 0, 0, 1),
(22, 'Onions', 'Vegetable', 0, '', 0, 0, 1),
(23, 'Pineapple', 'Vegetable', 0, '', 0, 0, 1),
(24, 'Red Onions', 'Vegetable', 0, '', 0, 0, 1),
(25, 'Red Peppers', 'Vegetable', 0, '', 0, 0, 1),
(26, 'Spinach', 'Vegetable', 0, '', 0, 0, 1),
(27, 'Sundried Tomatoes', 'Vegetable', 0, '', 0, 0, 1),
(28, 'Tomatoes', 'Vegetable', 0, '', 0, 0, 1),
(29, 'Extra Cheese', 'Vegetable', 0, '', 0, 0, 1),
(31, 'Well Done', 'zPreparation', 1, '', 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `useraddresses`
--

CREATE TABLE `useraddresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `unit` varchar(32) NOT NULL,
  `buzzcode` varchar(16) NOT NULL,
  `street` varchar(255) NOT NULL,
  `postalcode` varchar(16) NOT NULL,
  `city` varchar(64) NOT NULL,
  `province` varchar(32) NOT NULL,
  `latitude` varchar(16) NOT NULL,
  `longitude` varchar(16) NOT NULL,
  `phone` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `useraddresses`
--

INSERT INTO `useraddresses` (`id`, `user_id`, `number`, `unit`, `buzzcode`, `street`, `postalcode`, `city`, `province`, `latitude`, `longitude`, `phone`) VALUES
(1, 3, 483, '', '', 'Dundas Street', 'N6B 1W4', 'London', 'Ontario', '42.9871816', '-81.2386115', ''),
(2, 4, 1565, '', '', 'Western Rd', 'N6G 1H5', 'London', 'Ontario', '43.0187', '-81.2812887', ''),
(3, 5, 483, '', '', 'Dundas Street', 'N6B 1W4', 'London', 'Ontario', '42.9871816', '-81.2386115', ''),
(4, 6, 1569, '', '', 'Oxford Street East', 'N5V 1W5', 'London', 'Ontario', '43.0109195', '-81.198983600000', ''),
(95, 48, 300, '', '', 'Dundas St', 'N6B 1T6', 'London', 'Ontario', '42.9854177', '-81.244139099999', ''),
(96, 49, 300, '', '', 'Dundas St', 'N6B 1T6', 'London', 'Ontario', '42.9854177', '-81.244139099999', ''),
(97, 50, 2372, '', '', 'Barton St E', 'L8E 2W7', 'Hamilton', 'Ontario', '43.2376467', '-79.765102399999', ''),
(103, 54, 18, 'side door', '', 'Oakland Dr', 'L8E 3Z2', 'Hamilton', 'Ontario', '43.2304400000000', '-79.7693198', ''),
(104, 55, 1001, '', '', 'Fanshawe College Blvd', 'N5V 2A5', 'London', 'Ontario', '43.013414', '-81.199466000000', ''),
(105, 1, 2396, '', '', 'Asima Dr', 'N6M 0B3', 'London', 'Ontario', '42.9505', '-81.1735999', ''),
(106, 59, 2, '', '', 'Ottawa St N', 'L8H 3Y7', 'Hamilton', 'Ontario', '43.2427012', '-79.8195032', ''),
(107, 61, 225, '', '', 'John St S', 'L8N 2C7', 'Hamilton', 'Ontario', '43.2501868', '-79.8685682', ''),
(108, 62, 180, '', '', 'James St S', 'L8P 4V1', 'Hamilton', 'Ontario', '43.2511833', '-79.87147', ''),
(109, 63, 826, '', '', 'Queenston Rd', 'L8G 2B5', 'Hamilton', 'Ontario', '43.227685', '-79.7626864', ''),
(111, 65, 581, '', '', 'Main St E', 'L8M 1H9', 'Hamilton', 'Ontario', '43.2500393', '-79.8477776', ''),
(112, 66, 349, '', '', 'Grays Rd', 'L8E 2Z1', 'Hamilton', 'Ontario', '43.2334163', '-79.7401695', ''),
(113, 67, 1034, '', '', 'King St W', 'L8S 1L5', 'Hamilton', 'Ontario', '43.261727', '-79.9066278', ''),
(114, 68, 534, '', '', 'Upper Sherman Ave', 'L8V 3M1', 'Hamilton', 'Ontario', '43.2320924', '-79.8475259', ''),
(115, 69, 56, '', '', 'Hess St S', 'L8P 4R8', 'Hamilton', 'Ontario', '43.2574042', '-79.8780152', ''),
(116, 70, 201, '', '', 'James St S', 'L8P 3A8', 'Hamilton', 'Ontario', '43.250534', '-79.8715325', ''),
(117, 71, 931, '', '', 'Fennell Ave E', 'L8V 1W9', 'Hamilton', 'Ontario', '43.2285016', '-79.8403698', ''),
(119, 73, 450, '', '', 'Hamilton Regional Rd 8', 'L8G', 'Hamilton', 'Ontario', '43.217317', '-79.7196697', ''),
(120, 74, 1405, '', '', 'Upper Ottawa St', 'L8W 3J6', 'Hamilton', 'Ontario', '43.1983765', '-79.839967', ''),
(121, 75, 104, '', '', 'Cannon St E', 'L8L 2A3', 'Hamilton', 'Ontario', '43.2595937', '-79.8629033', ''),
(122, 76, 134, '', '', 'Ottawa St N', 'L8H 3Z3', 'Hamilton', 'Ontario', '43.246283', '-79.8177461', ''),
(123, 77, 870, '', '', 'Upper James St', 'L9C 3A4', 'Hamilton', 'Ontario', '43.227159', '-79.8830249', ''),
(124, 78, 1400, '', '', 'Upper James St', 'L9B 1K2', 'Hamilton', 'Ontario', '43.2096249', '-79.8910243', ''),
(125, 79, 1001, '', '', 'Rymal Rd E', 'L8W 3R9', 'Hamilton', 'Ontario', '43.1920407', '-79.8485062', ''),
(126, 80, 54, '', '', 'Queen St S', 'L8P 3R5', 'Hamilton', 'Ontario', '43.2579948', '-79.8797476', ''),
(127, 81, 263, '', '', 'Kenilworth Ave N', 'L8H 4S6', 'Hamilton', 'Ontario', '43.2482697', '-79.8067173', ''),
(128, 82, 800, '', '', 'Barton St E', 'L8L 3B3', 'Hamilton', 'Ontario', '43.2547963', '-79.8306975', ''),
(129, 83, 387, '', '', 'Barton St E', 'L8L 2Y2', 'Hamilton', 'Ontario', '43.2601395', '-79.8487452', ''),
(130, 84, 237, '', '', 'Parkdale Ave N', 'L8H', 'Hamilton', 'Ontario', '43.2431745', '-79.7895812', ''),
(131, 85, 550, '', '', 'Fennell Ave E', 'L8V 1S9', 'Hamilton', 'Ontario', '43.2316562', '-79.8572042', ''),
(132, 86, 574, '', '', 'James St N', 'L8L 1J7', 'Hamilton', 'Ontario', '43.2721007', '-79.8621156', ''),
(133, 87, 205, '', '', 'Quigley Rd', 'L8K 5M8', 'Hamilton', 'Ontario', '43.2162208', '-79.783788', ''),
(134, 88, 2145, '', '', 'King St E', 'L8K 1W5', 'Hamilton', 'Ontario', '43.2311692', '-79.8017835', ''),
(135, 89, 1357, '', '', 'Main St E', 'L8K 1B6', 'Hamilton', 'Ontario', '43.2411024', '-79.8120347', ''),
(136, 90, 762, '', '', 'King St E', 'L8M 1A6', 'Hamilton', 'Ontario', '43.2513318', '-79.8446862', ''),
(137, 91, 601, '', '', 'Upper James St', 'L9C 2Y7', 'Hamilton', 'Ontario', '43.2396665', '-79.8765188', ''),
(138, 92, 25, '', '', 'Redmond Dr', 'L8W 3K7', 'Hamilton', 'Ontario', '43.2057258', '-79.8647566', ''),
(139, 93, 919, '', '', 'Upper Paradise Rd', 'L9B 2M9', 'Hamilton', 'Ontario', '43.2177863', '-79.9182575', ''),
(140, 94, 90, '', '', 'Wellington St N', 'L8R 1N1', 'Hamilton', 'Ontario', '43.2559382', '-79.8577265', ''),
(141, 95, 160, '', '', 'Centennial Pkwy N', 'L8E 1H9', 'Hamilton', 'Ontario', '43.2337926', '-79.7610652', ''),
(142, 96, 455, '', '', 'Barton St E', 'L8L 2Y7', 'Hamilton', 'Ontario', '43.2593099', '-79.846162', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `lastlogin` bigint(20) NOT NULL,
  `loginattempts` int(11) NOT NULL,
  `profiletype` tinyint(4) NOT NULL,
  `authcode` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `stripecustid` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`, `phone`, `lastlogin`, `loginattempts`, `profiletype`, `authcode`, `stripecustid`) VALUES
(1, 'Roy Wall', 'roy@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '0000-00-00 00:00:00', '2017-04-25 13:52:10', '9055123067', 1487608084, 0, 1, '', 'cus_AaCHoacic12HrC'),
(3, 'Fabulous', 'info+fab@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2016-11-16 15:49:31', '0000-00-00 00:00:00', '9055315331', 1481048458, 0, 2, '', 'cus_9yYE78hosPbuGH'),
(4, 'Marvellous', 'info+mar@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-02-14 15:28:50', '0000-00-00 00:00:00', '9055315331', 0, 0, 2, '', ''),
(48, 'Van Trinh', 'info@trinoweb.com', '$2y$10$.0DQCK8l9YOr49mc3AcEr.8zemyiRmUa1j69p5MJO4vf6PCIAOip.', '', '2017-04-01 17:18:32', '2017-04-22 13:15:53', '9055315331', 0, 0, 0, '', ''),
(50, 'Quality Pizza & Wings', 'odealyonline@gmail.com', '$2y$10$S5SNVTZgWk9Ufe.kLdtaMOOMo2VxRqkXUpv1k/af09Bn1c32UUrcq', '', '2017-04-01 18:20:18', '0000-00-00 00:00:00', '9055315331', 0, 0, 2, '', ''),
(54, 'Van Dao Trinh', 'dvt1985@hotmail.com', '$2y$10$202F5bICMxbyYm1VT7Petey5iUhHeMKI.HJgpM9bsB0MWFg7o5mPa', '', '2017-04-23 16:16:17', '0000-00-00 00:00:00', '9055315331', 0, 0, 0, '', 'cus_AWrH95lAblBCVy'),
(55, 'J', 'j@j.com', '$2y$10$4HDeNQb4bjCIr8.LL9LVoeKxK8H.y9ao7GNnqIo7iKWcOVClFptCm', '', '2017-04-23 21:26:03', '0000-00-00 00:00:00', '', 0, 0, 0, '', ''),
(56, 'Van Trinh', 'info+test@trinoweb.com', '$2y$10$enHdNDAfQcdCP/LibPfuPugxsRQ09aeOr.gWpHzYxpsWoyYTUClCO', '', '2017-05-16 19:25:13', '0000-00-00 00:00:00', '', 0, 0, 0, '', ''),
(58, 'Van Trinh', 'roy+test@trinoweb.com', '$2y$10$/XBPlDw/8HoAw9Pxl1d1MuMgHc0sRMOH0q8oMBC0ts/ylI9Crft5m', '', '2017-05-24 17:57:35', '0000-00-00 00:00:00', '', 0, 0, 0, '', ''),
(59, 'BBQ pizza', 'info+BBQpizza@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', NULL, '2017-06-13 21:28:32', NULL, '(905) 544-2828', 0, 0, 2, '', ''),
(61, 'Double Double Pizza & Chicken Inc', 'info+DoubleDoublePizzaChickenInc@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', NULL, '2017-06-13 21:49:24', NULL, '(905) 528-0000', 0, 0, 2, '', ''),
(62, 'Royal Pizza & Wings', 'info+RoyalPizzaWings@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', NULL, '2017-06-13 21:49:38', NULL, '(905) 529-4242', 0, 0, 2, '', ''),
(63, 'Royal Pizza & Wings', 'info+RoyalPizzaWings2@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', NULL, '2017-06-13 22:01:35', NULL, '(905) 664-8075', 0, 0, 2, '', ''),
(64, 'Royal Pizza & Wings', 'info+RoyalPizzaWings1@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:04:46', '0000-00-00 00:00:00', '(905) 664-8075', 0, 0, 2, '', ''),
(65, 'City Pizza & Wings', 'info+CityPizzaWings@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:12:25', '0000-00-00 00:00:00', '(905) 528-1111', 0, 0, 2, '', ''),
(66, 'The Express Restaurant', 'info+TheExpressRestaurant@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:12:37', '0000-00-00 00:00:00', '(905) 560-1475', 0, 0, 2, '', ''),
(67, 'The Express Restaurant', 'info+TheExpressRestaurant1@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:14:23', '0000-00-00 00:00:00', '(289) 389-2008', 0, 0, 2, '', ''),
(68, 'Chicago Style Pizza', 'info+ChicagoStylePizza@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:14:32', '0000-00-00 00:00:00', '(905) 575-8800', 0, 0, 2, '', ''),
(69, 'Doors Pub: Taco Joint & Metal Bar', 'info+DoorsPubTacoJointMetalBar@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:15:29', '0000-00-00 00:00:00', '(905) 540-8888', 0, 0, 2, '', ''),
(70, 'Bronzie\'s Place', 'info+BronziesPlace@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:15:43', '0000-00-00 00:00:00', '(905) 529-3403', 0, 0, 2, '', ''),
(71, 'Alfredo\'s Place', 'info+AlfredosPlace@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:15:54', '0000-00-00 00:00:00', '(905) 383-3555', 0, 0, 2, '', ''),
(73, 'Star Pizza', 'info+StarPizza@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:19:31', '0000-00-00 00:00:00', '(905) 664-4444', 0, 0, 2, '', ''),
(74, 'Red Rockets', 'info+RedRockets@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:26:11', '0000-00-00 00:00:00', '(905) 318-9555', 0, 0, 2, '', ''),
(75, 'Mattina Pizzeria', 'info+MattinaPizzeria@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:26:20', '0000-00-00 00:00:00', '(905) 527-7918', 0, 0, 2, '', ''),
(76, 'National Pizza & Wings', 'info+NationalPizzaWings@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:26:28', '0000-00-00 00:00:00', '(905) 549-7734', 0, 0, 2, '', ''),
(77, 'National Pizza & Wings', 'info+NationalPizzaWings1@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:26:38', '0000-00-00 00:00:00', '(905) 575-4500', 0, 0, 2, '', ''),
(78, 'Topper\'s Pizza Hamilton - Upper James', 'info+ToppersPizzaHamiltonUpperJames@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:32:33', '0000-00-00 00:00:00', '(905) 387-7171', 0, 0, 2, '', ''),
(79, 'Rymal Pizza-Wings', 'info+RymalPizzaWings@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:39:11', '0000-00-00 00:00:00', '(905) 387-1800', 0, 0, 2, '', ''),
(80, 'Hess Village Pizza & Wings', 'info+HessVillagePizzaWings@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:39:20', '0000-00-00 00:00:00', '(905) 525-5444', 0, 0, 2, '', ''),
(81, 'Diana\'s Pizza & Grille', 'info+DianasPizzaGrille@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:39:27', '0000-00-00 00:00:00', '(905) 549-7474', 0, 0, 2, '', ''),
(82, 'Niva Pizza & Wings', 'info+NivaPizzaWings@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:43:36', '0000-00-00 00:00:00', '(905) 544-4544', 0, 0, 2, '', ''),
(83, 'Lava Pizza', 'info+LavaPizza@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:43:45', '0000-00-00 00:00:00', '(905) 777-0666', 0, 0, 2, '', ''),
(84, 'Hi-Line Pizza & Wings on Parkdale', 'info+HiLinePizzaWingsonParkdale@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:43:52', '0000-00-00 00:00:00', '(905) 543-9555', 0, 0, 2, '', ''),
(85, 'Lava Pizza & Wings', 'info+LavaPizzaWings@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:44:01', '0000-00-00 00:00:00', '(905) 574-6666', 0, 0, 2, '', ''),
(86, 'Weston Pizza And Wings', 'info+WestonPizzaAndWings@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:44:09', '0000-00-00 00:00:00', '(905) 521-9919', 0, 0, 2, '', ''),
(87, 'The Pizza House', 'info+ThePizzaHouse@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:44:17', '0000-00-00 00:00:00', '(905) 561-0220', 0, 0, 2, '', ''),
(88, 'Bella Pizza', 'info+BellaPizza@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:44:25', '0000-00-00 00:00:00', '(905) 544-1444', 0, 0, 2, '', ''),
(89, 'Express 2 For 1 Pizza & Wings', 'info+Express2For1PizzaWings@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:44:33', '0000-00-00 00:00:00', '(905) 549-7171', 0, 0, 2, '', ''),
(90, 'Giuseppe\'s Italian Cuisine & Pizza', 'info+GiuseppesItalianCuisinePizza@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:44:41', '0000-00-00 00:00:00', '(905) 525-3334', 0, 0, 2, '', ''),
(91, 'Marino Pizza And Wings', 'info+MarinoPizzaAndWings@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:44:49', '0000-00-00 00:00:00', '(905) 574-2020', 0, 0, 2, '', ''),
(92, 'Twice The Deal Pizza', 'info+TwiceTheDealPizza@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:45:36', '0000-00-00 00:00:00', '(905) 385-0505', 0, 0, 2, '', ''),
(93, 'A 1 Pizza Place', 'info+A1PizzaPlace@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:45:44', '0000-00-00 00:00:00', '(905) 318-5050', 0, 0, 2, '', ''),
(94, 'Plaza Pizza & Wings', 'info+PlazaPizzaWings@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:45:53', '0000-00-00 00:00:00', '(905) 522-0909', 0, 0, 2, '', ''),
(95, 'Pizza Depot', 'info+PizzaDepot@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:46:00', '0000-00-00 00:00:00', '(905) 525-9711', 0, 0, 2, '', ''),
(96, 'Le Bella Pizza', 'info+LeBellaPizza@trinoweb.com', '$2y$10$440weczzi7gl8OpXQJROPey1Eiyx1BQWk4dFEj9pAHWO2FmagZQ52', '', '2017-06-13 22:46:08', '0000-00-00 00:00:00', '(905) 529-7627', 0, 0, 2, '', '');

-- --------------------------------------------------------

--
-- Table structure for table `wings_sauce`
--

CREATE TABLE `wings_sauce` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `isfree` tinyint(1) NOT NULL,
  `qualifiers` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'comma delimited list of the names for 1/2,x1,x2 if applicable',
  `isall` tinyint(4) NOT NULL DEFAULT '1',
  `groupid` int(11) NOT NULL,
  `enabled` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wings_sauce`
--

INSERT INTO `wings_sauce` (`id`, `name`, `type`, `isfree`, `qualifiers`, `isall`, `groupid`, `enabled`) VALUES
(1, 'Honey Garlic', 'Sauce', 0, '', 1, 1, 1),
(3, 'BBQ', 'Sauce', 0, '', 1, 1, 1),
(4, 'Hot', 'Sauce', 0, '', 1, 1, 1),
(5, 'Suicide', 'Sauce', 0, '', 1, 1, 1),
(6, 'Sauce on Side', 'zPreparation', 1, '', 1, 2, 1),
(7, 'Well Done', 'zPreparation', 1, '', 1, 3, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `actions`
--
ALTER TABLE `actions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `additional_toppings`
--
ALTER TABLE `additional_toppings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `combos`
--
ALTER TABLE `combos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hours`
--
ALTER TABLE `hours`
  ADD PRIMARY KEY (`restaurant_id`),
  ADD UNIQUE KEY `restaurant_id` (`restaurant_id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `presets`
--
ALTER TABLE `presets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `keyname` (`keyname`);

--
-- Indexes for table `shortage`
--
ALTER TABLE `shortage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `toppings`
--
ALTER TABLE `toppings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `useraddresses`
--
ALTER TABLE `useraddresses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `wings_sauce`
--
ALTER TABLE `wings_sauce`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `actions`
--
ALTER TABLE `actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `additional_toppings`
--
ALTER TABLE `additional_toppings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `combos`
--
ALTER TABLE `combos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=257;

--
-- AUTO_INCREMENT for table `presets`
--
ALTER TABLE `presets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1596;

--
-- AUTO_INCREMENT for table `shortage`
--
ALTER TABLE `shortage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `useraddresses`
--
ALTER TABLE `useraddresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
