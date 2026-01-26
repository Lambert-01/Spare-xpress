--
-- Table structure for table `vehicle_brands_enhanced`
--

CREATE TABLE `vehicle_brands_enhanced` (
  `id` int(11) NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `logo_image` varchar(255) DEFAULT NULL,
  `brand_image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `founded_year` int(11) DEFAULT NULL,
  `manufacturer_details` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_brands_enhanced`
--

INSERT INTO `vehicle_brands_enhanced` (`id`, `brand_name`, `slug`, `logo_image`, `brand_image`, `description`, `country`, `founded_year`, `manufacturer_details`, `website`, `contact_email`, `contact_phone`, `is_active`, `display_order`, `seo_title`, `seo_description`, `created_at`, `updated_at`) VALUES
(1, 'Toyota', 'toyota', NULL, NULL, 'Leading Japanese automaker', 'Japan', 1937, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, '2025-12-08 19:11:39', '2025-12-08 19:11:39'),
(2, 'Honda', 'honda', NULL, NULL, 'Innovative automotive manufacturer', 'Japan', 1948, NULL, NULL, NULL, NULL, 1, 2, NULL, NULL, '2025-12-08 19:11:39', '2025-12-08 19:11:39'),
(3, 'Nissan', 'nissan', NULL, NULL, 'Global automotive brand', 'Japan', 1932, NULL, NULL, NULL, NULL, 1, 3, NULL, NULL, '2025-12-08 19:11:39', '2025-12-08 19:11:39'),
(4, 'BMW', 'bmw', NULL, NULL, 'German luxury car manufacturer', 'Germany', 1916, NULL, NULL, NULL, NULL, 1, 4, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(5, 'Mercedes-Benz', 'mercedes-benz', NULL, NULL, 'Luxury automotive brand', 'Germany', 1926, NULL, NULL, NULL, NULL, 1, 5, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(6, 'Ford', 'ford', NULL, NULL, 'American automotive giant', 'USA', 1903, NULL, NULL, NULL, NULL, 1, 6, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(7, 'Volkswagen', 'volkswagen', NULL, NULL, 'German automotive manufacturer', 'Germany', 1937, NULL, NULL, NULL, NULL, 1, 7, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(8, 'Hyundai', 'hyundai', NULL, NULL, 'Korean automotive brand', 'South Korea', 1967, NULL, NULL, NULL, NULL, 1, 8, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(9, 'Mitsubishi', 'mitsubishi', NULL, NULL, 'Reliable Japanese manufacturer', 'Japan', 1870, NULL, NULL, NULL, NULL, 1, 9, NULL, NULL, '2025-12-08 19:11:39', '2025-12-08 19:11:39'),
(10, 'Mazda', 'mazda', NULL, NULL, 'Innovative Japanese automaker', 'Japan', 1920, NULL, NULL, NULL, NULL, 1, 10, NULL, NULL, '2025-12-08 19:11:39', '2025-12-08 19:11:39'),
(11, 'Suzuki', 'suzuki', NULL, NULL, 'Affordable Japanese vehicles', 'Japan', 1909, NULL, NULL, NULL, NULL, 1, 11, NULL, NULL, '2025-12-08 19:11:39', '2025-12-08 19:11:39'),
(12, 'Subaru', 'subaru', NULL, NULL, 'AWD specialist Japanese brand', 'Japan', 1953, NULL, NULL, NULL, NULL, 1, 12, NULL, NULL, '2025-12-08 19:11:39', '2025-12-08 19:11:39'),
(13, 'Isuzu', 'isuzu', NULL, NULL, 'Commercial vehicle expert', 'Japan', 1916, NULL, NULL, NULL, NULL, 1, 13, NULL, NULL, '2025-12-08 19:11:39', '2025-12-08 19:11:39'),
(14, 'Lexus', 'lexus', NULL, NULL, 'Luxury division of Toyota', 'Japan', 1989, NULL, NULL, NULL, NULL, 1, 14, NULL, NULL, '2025-12-08 19:11:39', '2025-12-08 19:11:39'),
(15, 'Kia', 'kia', NULL, NULL, 'Korean automotive manufacturer', 'South Korea', 1944, NULL, NULL, NULL, NULL, 1, 15, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(16, 'BYD', 'byd', NULL, NULL, 'Chinese EV and hybrid manufacturer', 'China', 1995, NULL, NULL, NULL, NULL, 1, 16, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(17, 'Geely', 'geely', NULL, NULL, 'Chinese automaker including Lynk & Co', 'China', 1986, NULL, NULL, NULL, NULL, 1, 17, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(18, 'Chery', 'chery', NULL, NULL, 'Chinese budget car manufacturer', 'China', 1997, NULL, NULL, NULL, NULL, 1, 18, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(19, 'Great Wall', 'great-wall', NULL, NULL, 'Chinese SUV manufacturer', 'China', 1984, NULL, NULL, NULL, NULL, 1, 19, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(20, 'JAC', 'jac', NULL, NULL, 'Chinese light commercial and SUV manufacturer', 'China', 1964, NULL, NULL, NULL, NULL, 1, 20, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(21, 'Dongfeng', 'dongfeng', NULL, NULL, 'Chinese automaker with passenger models', 'China', 1969, NULL, NULL, NULL, NULL, 1, 21, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(22, 'Wuling', 'wuling', NULL, NULL, 'Chinese EV and microcar manufacturer', 'China', 2002, NULL, NULL, NULL, NULL, 1, 22, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(23, 'BAIC', 'baic', NULL, NULL, 'Chinese passenger car and SUV manufacturer', 'China', 1958, NULL, NULL, NULL, NULL, 1, 23, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(24, 'Changan', 'changan', NULL, NULL, 'Chinese EV and gas car manufacturer', 'China', 1862, NULL, NULL, NULL, NULL, 1, 24, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(25, 'MG', 'mg', NULL, NULL, 'Chinese-British heritage EV and ICE manufacturer', 'China', 1924, NULL, NULL, NULL, NULL, 1, 25, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(26, 'Peugeot', 'peugeot', NULL, NULL, 'French automaker', 'France', 1810, NULL, NULL, NULL, NULL, 1, 26, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(27, 'Renault', 'renault', NULL, NULL, 'French automaker', 'France', 1899, NULL, NULL, NULL, NULL, 1, 27, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(28, 'Citroën', 'citroen', NULL, NULL, 'French automaker', 'France', 1919, NULL, NULL, NULL, NULL, 1, 28, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(29, 'Skoda', 'skoda', NULL, NULL, 'Czech automaker part of Volkswagen Group', 'Czech Republic', 1895, NULL, NULL, NULL, NULL, 1, 29, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(30, 'Audi', 'audi', NULL, NULL, 'German luxury car manufacturer', 'Germany', 1909, NULL, NULL, NULL, NULL, 1, 30, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(31, 'Volvo', 'volvo', NULL, NULL, 'Swedish automaker', 'Sweden', 1927, NULL, NULL, NULL, NULL, 1, 31, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(32, 'Chevrolet', 'chevrolet', NULL, NULL, 'American automotive brand', 'USA', 1911, NULL, NULL, NULL, NULL, 1, 32, NULL, NULL, '2025-12-08 19:11:40', '2025-12-08 19:11:40'),
(33, 'Land Rover', 'land-rover', NULL, NULL, 'British luxury SUV manufacturer', 'UK', 1948, NULL, NULL, NULL, NULL, 1, 33, NULL, NULL, '2025-12-08 19:11:39', '2025-12-08 19:11:39'),
(34, 'Jaguar', 'jaguar', NULL, NULL, 'British luxury car manufacturer', 'UK', 1922, NULL, NULL, NULL, NULL, 1, 34, NULL, NULL, '2025-12-08 19:11:39', '2025-12-08 19:11:39'),
(35, 'Mini', 'mini', NULL, NULL, 'British small car manufacturer', 'UK', 1959, NULL, NULL, NULL, NULL, 1, 35, NULL, NULL, '2025-12-08 19:11:39', '2025-12-08 19:11:39')

-- --------------------------------------------------------
--