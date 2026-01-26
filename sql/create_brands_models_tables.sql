-- Create brands table
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL UNIQUE,
  `brand_image` varchar(255) DEFAULT NULL,
  `country_of_origin` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create models table
CREATE TABLE IF NOT EXISTS `models` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) NOT NULL,
  `model_name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `year_start` int(11) DEFAULT NULL,
  `year_end` int(11) DEFAULT NULL,
  `model_image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`),
  CONSTRAINT `models_brand_fk` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample brands data
INSERT INTO `brands` (`brand_name`, `slug`, `country_of_origin`, `is_active`, `display_order`) VALUES
('Toyota', 'toyota', 'Japan', 1, 1),
('Honda', 'honda', 'Japan', 1, 2),
('Nissan', 'nissan', 'Japan', 1, 3),
('Mitsubishi', 'mitsubishi', 'Japan', 1, 4),
('Hyundai', 'hyundai', 'South Korea', 1, 5),
('Kia', 'kia', 'South Korea', 1, 6),
('Isuzu', 'isuzu', 'Japan', 1, 7),
('Mazda', 'mazda', 'Japan', 1, 8),
('Subaru', 'subaru', 'Japan', 1, 9),
('Suzuki', 'suzuki', 'Japan', 1, 10),
('Lexus', 'lexus', 'Japan', 1, 11),
('BMW', 'bmw', 'Germany', 1, 12),
('Mercedes-Benz', 'mercedes-benz', 'Germany', 1, 13),
('Audi', 'audi', 'Germany', 1, 14),
('Volkswagen', 'volkswagen', 'Germany', 1, 15),
('Ford', 'ford', 'USA', 1, 16),
('Chevrolet', 'chevrolet', 'USA', 1, 17),
('Jeep', 'jeep', 'USA', 1, 18),
('Land Rover', 'land-rover', 'UK', 1, 19),
('Range Rover', 'range-rover', 'UK', 1, 20),
('Volvo', 'volvo', 'Sweden', 1, 21),
('Peugeot', 'peugeot', 'France', 1, 22),
('Renault', 'renault', 'France', 1, 23),
('Citroen', 'citroen', 'France', 1, 24),
('Fiat', 'fiat', 'Italy', 1, 25),
('Alfa Romeo', 'alfa-romeo', 'Italy', 1, 26),
('BYD', 'byd', 'China', 1, 27);

-- Insert sample models data
INSERT INTO `models` (`brand_id`, `model_name`, `slug`, `year_start`, `year_end`, `is_active`, `display_order`) VALUES
-- Toyota models
(1, 'Corolla', 'corolla', 1966, NULL, 1, 1),
(1, 'Hilux', 'hilux', 1968, NULL, 1, 2),
(1, 'Prado', 'prado', 1984, NULL, 1, 3),
(1, 'RAV4', 'rav4', 1994, NULL, 1, 4),
(1, 'Fortuner', 'fortuner', 2005, NULL, 1, 5),
(1, 'Camry', 'camry', 1982, NULL, 1, 6),
(1, 'Prius', 'prius', 1997, NULL, 1, 7),
(1, 'Vitz', 'vitz', 1999, NULL, 1, 8),
(1, 'Allion', 'allion', 2001, NULL, 1, 9),
(1, 'Premio', 'premio', 2001, NULL, 1, 10),

-- Honda models
(2, 'Civic', 'civic', 1972, NULL, 1, 1),
(2, 'Accord', 'accord', 1976, NULL, 1, 2),
(2, 'CR-V', 'cr-v', 1995, NULL, 1, 3),
(2, 'Fit', 'fit', 2001, NULL, 1, 4),
(2, 'Jazz', 'jazz', 2002, NULL, 1, 5),
(2, 'Insight', 'insight', 1999, NULL, 1, 6),
(2, 'Vezel', 'vezel', 2013, NULL, 1, 7),
(2, 'Freed', 'freed', 2010, NULL, 1, 8),
(2, 'Stream', 'stream', 2003, NULL, 1, 9),
(2, 'Pilot', 'pilot', 2002, NULL, 1, 10),

-- Nissan models
(3, 'X-Trail', 'x-trail', 2000, NULL, 1, 1),
(3, 'Navara', 'navara', 1985, NULL, 1, 2),
(3, 'Note', 'note', 2004, NULL, 1, 3),
(3, 'Micra', 'micra', 1982, NULL, 1, 4),
(3, 'Tiida', 'tiida', 2004, NULL, 1, 5),
(3, 'Sylphy', 'sylphy', 2000, NULL, 1, 6),
(3, 'Qashqai', 'qashqai', 2006, NULL, 1, 7),
(3, 'Juke', 'juke', 2010, NULL, 1, 8),
(3, 'Murano', 'murano', 2002, NULL, 1, 9),
(3, 'Pathfinder', 'pathfinder', 1985, NULL, 1, 10),

-- Mitsubishi models
(4, 'Pajero', 'pajero', 1982, NULL, 1, 1),
(4, 'Pajero Sport', 'pajero-sport', 1996, NULL, 1, 2),
(4, 'Outlander', 'outlander', 2003, NULL, 1, 3),
(4, 'Lancer', 'lancer', 1973, NULL, 1, 4),
(4, 'Mirage', 'mirage', 1978, NULL, 1, 5),
(4, 'Colt', 'colt', 1962, NULL, 1, 6),
(4, 'Canter', 'canter', 1963, NULL, 1, 7),
(4, 'Rosa', 'rosa', 1975, NULL, 1, 8),
(4, 'L200', 'l200', 1978, NULL, 1, 9),
(4, 'Space Wagon', 'space-wagon', 1983, NULL, 1, 10),

-- Hyundai models
(5, 'Tucson', 'tucson', 2004, NULL, 1, 1),
(5, 'Santa Fe', 'santa-fe', 1999, NULL, 1, 2),
(5, 'Elantra', 'elantra', 1990, NULL, 1, 3),
(5, 'Accent', 'accent', 1994, NULL, 1, 4),
(5, 'Creta', 'creta', 2014, NULL, 1, 5),
(5, 'Kona', 'kona', 2017, NULL, 1, 6),
(5, 'i20', 'i20', 2008, NULL, 1, 7),
(5, 'H1', 'h1', 1996, NULL, 1, 8),
(5, 'Sonata', 'sonata', 1985, NULL, 1, 9),
(5, 'Ioniq', 'ioniq', 2016, NULL, 1, 10),

-- Kia models
(6, 'Sportage', 'sportage', 1993, NULL, 1, 1),
(6, 'Sorento', 'sorento', 2002, NULL, 1, 2),
(6, 'Rio', 'rio', 2000, NULL, 1, 3),
(6, 'Cerato', 'cerato', 2003, NULL, 1, 4),
(6, 'Seltos', 'seltos', 2019, NULL, 1, 5),
(6, 'Stonic', 'stonic', 2017, NULL, 1, 6),
(6, 'Carnival', 'carnival', 1998, NULL, 1, 7),
(6, 'Stinger', 'stinger', 2017, NULL, 1, 8),

-- Isuzu models
(7, 'D-Max', 'd-max', 2002, NULL, 1, 1),
(7, 'Trooper', 'trooper', 1981, NULL, 1, 2),
(7, 'Wizard', 'wizard', 1996, NULL, 1, 3),
(7, 'MU-X', 'mu-x', 2013, NULL, 1, 4),
(7, 'ELF', 'elf', 1959, NULL, 1, 5),
(7, 'Bus', 'bus', 1970, NULL, 1, 6),

-- Lexus models
(11, 'LX', 'lx', 1995, NULL, 1, 1),
(11, 'GX', 'gx', 2002, NULL, 1, 2),
(11, 'RX', 'rx', 1998, NULL, 1, 3),
(11, 'NX', 'nx', 2014, NULL, 1, 4),
(11, 'ES', 'es', 1989, NULL, 1, 5),
(11, 'IS', 'is', 1998, NULL, 1, 6),
(11, 'LS', 'ls', 1989, NULL, 1, 7),
(11, 'CT', 'ct', 2010, NULL, 1, 8),
(11, 'GS', 'gs', 1991, NULL, 1, 9),
(11, 'UX', 'ux', 2018, NULL, 1, 10);