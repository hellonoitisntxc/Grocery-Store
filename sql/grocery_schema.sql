-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS grocery_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE grocery_store;

-- Table structure for table `products`
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `category` varchar(50) NOT NULL,
                            `name` varchar(100) NOT NULL,
                            `image` varchar(255) DEFAULT NULL,
                            `price` decimal(10,2) NOT NULL,
                            PRIMARY KEY (`id`),
                            KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `products`
INSERT INTO `products` (`id`, `category`, `name`, `image`, `price`) VALUES
                                                                        (1, 'Vegetables', 'Potato', 'potato.jpg', 0.99),
                                                                        (2, 'Vegetables', 'Carrots', 'carrot.jpg', 1.29), -- Corrected image name if needed
                                                                        (3, 'Vegetables', 'Broccoli', 'broccoli.jpg', 1.49),
                                                                        (4, 'Meat', 'Chicken', 'chicken.jpg', 4.99),
                                                                        (5, 'Meat', 'Fish', 'fish.jpg', 5.99),
                                                                        (6, 'Meat', 'Beef', 'beef.jpg', 6.99),
                                                                        (7, 'Meat', 'Pork', 'pork.jpg', 5.49);

-- Table structure for table `users`
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `name` varchar(100) NOT NULL,
                         `phone` varchar(15) NOT NULL, -- Increased size slightly for flexibility
                         `email` varchar(100) NOT NULL,
                         `password_hash` varchar(255) NOT NULL,
                         PRIMARY KEY (`id`),
                         UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for table `orders`
-- As per objective 7, storing customer/product details directly in the order
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `user_id` int(11) NOT NULL,
                          `user_name` varchar(100) NOT NULL,
                          `user_email` varchar(100) NOT NULL,
                          `user_phone` varchar(15) NOT NULL,
                          `product_id` int(11) NOT NULL,
                          `product_name` varchar(100) NOT NULL,
                          `product_price` decimal(10,2) NOT NULL,
                          `order_time` timestamp NOT NULL DEFAULT current_timestamp(),
                          PRIMARY KEY (`id`),
                          KEY `user_id_fk` (`user_id`), -- Foreign key constraint (optional but good practice)
                          KEY `product_id_fk` (`product_id`), -- Foreign key constraint (optional but good practice)
                          CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
                          CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE -- Assumes product deletion cascades (adjust if needed)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;