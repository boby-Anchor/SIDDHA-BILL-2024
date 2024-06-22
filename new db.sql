-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.4.32-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table ceylriea_ts.bill_type
CREATE TABLE IF NOT EXISTS `bill_type` (
  `bill_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `bill_type_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`bill_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.chinvoices
CREATE TABLE IF NOT EXISTS `chinvoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(200) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `p_name` varchar(50) NOT NULL,
  `contact_no` varchar(30) NOT NULL,
  `d_name` varchar(50) DEFAULT NULL,
  `reg` varchar(30) DEFAULT NULL,
  `bill_type_id` int(11) NOT NULL,
  `payment_method` varchar(100) NOT NULL,
  `total_amount` varchar(50) NOT NULL DEFAULT '',
  `discount_percentage` int(11) DEFAULT NULL,
  `delivery_charges` varchar(50) DEFAULT NULL,
  `value_added_services` varchar(50) DEFAULT NULL,
  `paidAmount` varchar(50) NOT NULL,
  `cardPaidAmount` varchar(30) DEFAULT NULL,
  `balance` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=430 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.customizable_data
CREATE TABLE IF NOT EXISTS `customizable_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=257 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.customize_bills
CREATE TABLE IF NOT EXISTS `customize_bills` (
  `customize_bills_id` int(11) NOT NULL AUTO_INCREMENT,
  `customize_bills_logo` varchar(45) DEFAULT NULL,
  `customize_bills_mobile` varchar(45) DEFAULT NULL,
  `customize_bills_address` varchar(45) DEFAULT NULL,
  `print_meta_status` int(11) DEFAULT NULL,
  `print_paper_size` varchar(45) DEFAULT NULL,
  `discount_section-status` varchar(45) DEFAULT NULL,
  `bill_note` text DEFAULT NULL,
  `customize_bill_shop-id` int(11) DEFAULT NULL,
  PRIMARY KEY (`customize_bills_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.grn
CREATE TABLE IF NOT EXISTS `grn` (
  `grn_id` int(11) NOT NULL AUTO_INCREMENT,
  `grn_number` varchar(45) DEFAULT NULL,
  `grn_date` datetime DEFAULT NULL,
  `grn_sub_total` varchar(45) DEFAULT NULL,
  `grn_shop_id` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`grn_id`)
) ENGINE=InnoDB AUTO_INCREMENT=600 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.grn_item
CREATE TABLE IF NOT EXISTS `grn_item` (
  `grn_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `grn_number` varchar(45) DEFAULT NULL,
  `grn_p_id` varchar(45) DEFAULT NULL,
  `grn_p_qty` varchar(45) DEFAULT NULL,
  `grn_p_cost` varchar(45) DEFAULT NULL,
  `grn_p_price` varchar(45) DEFAULT NULL,
  `p_plus_discount` int(11) DEFAULT NULL,
  `p_free_qty` int(11) DEFAULT NULL,
  PRIMARY KEY (`grn_item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=903 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.hub_order
CREATE TABLE IF NOT EXISTS `hub_order` (
  `hub_order_id` int(11) NOT NULL AUTO_INCREMENT,
  `HO_number` varchar(45) DEFAULT NULL,
  `HO_item` varchar(45) DEFAULT NULL,
  `HO_brand` varchar(50) DEFAULT NULL,
  `HO_qty` varchar(45) DEFAULT NULL,
  `hub_order_unit` varchar(45) DEFAULT NULL,
  `HO_price` varchar(45) DEFAULT NULL,
  `HO_total` varchar(45) DEFAULT NULL,
  `HO_shopId` int(11) DEFAULT NULL,
  PRIMARY KEY (`hub_order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.hub_order_details
CREATE TABLE IF NOT EXISTS `hub_order_details` (
  `hub_order_details_id` int(11) NOT NULL AUTO_INCREMENT,
  `hub_order_number` varchar(45) DEFAULT NULL,
  `hub_order_subTotal` varchar(45) DEFAULT NULL,
  `hub_order_status` int(11) DEFAULT NULL,
  `hub_order_paymentType` int(11) DEFAULT NULL,
  `hub_order_paymentStatus` int(11) DEFAULT NULL,
  `HO_date` datetime DEFAULT NULL,
  PRIMARY KEY (`hub_order_details_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.invoiceitems
CREATE TABLE IF NOT EXISTS `invoiceitems` (
  `invoiceItemId` int(11) NOT NULL AUTO_INCREMENT,
  `invoiceNumber` varchar(45) DEFAULT NULL,
  `invoiceDate` datetime DEFAULT NULL,
  `invoiceItem` varchar(45) DEFAULT NULL,
  `invoiceItem_qty` int(11) DEFAULT NULL,
  `invoiceItem_unit` varchar(45) DEFAULT NULL,
  `invoiceItem_price` varchar(45) DEFAULT NULL,
  `invoiceItem_total` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`invoiceItemId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1938 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.invoices
CREATE TABLE IF NOT EXISTS `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(200) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `p_name` varchar(50) NOT NULL,
  `contact_no` varchar(30) NOT NULL,
  `d_name` varchar(50) DEFAULT NULL,
  `reg` varchar(30) DEFAULT NULL,
  `bill_type_id` int(11) NOT NULL,
  `payment_method` varchar(100) NOT NULL,
  `total_amount` varchar(50) NOT NULL DEFAULT '',
  `discount_percentage` int(11) DEFAULT NULL,
  `delivery_charges` varchar(50) DEFAULT NULL,
  `value_added_services` varchar(50) DEFAULT NULL,
  `paidAmount` varchar(50) NOT NULL,
  `cardPaidAmount` varchar(30) DEFAULT NULL,
  `balance` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=430 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.medicine_unit
CREATE TABLE IF NOT EXISTS `medicine_unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit` varchar(50) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.onlineinvoices
CREATE TABLE IF NOT EXISTS `onlineinvoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(200) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `p_name` varchar(50) NOT NULL,
  `contact_no` varchar(30) NOT NULL,
  `d_name` varchar(50) DEFAULT NULL,
  `reg` varchar(30) DEFAULT NULL,
  `bill_type_id` int(11) NOT NULL,
  `payment_method` varchar(100) NOT NULL,
  `total_amount` varchar(50) NOT NULL DEFAULT '',
  `discount_percentage` int(11) DEFAULT NULL,
  `delivery_charges` varchar(50) DEFAULT NULL,
  `value_added_services` varchar(50) DEFAULT NULL,
  `paidAmount` varchar(50) NOT NULL,
  `cardPaidAmount` varchar(30) DEFAULT NULL,
  `balance` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=430 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.order_status
CREATE TABLE IF NOT EXISTS `order_status` (
  `order_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_status` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`order_status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.payment_status
CREATE TABLE IF NOT EXISTS `payment_status` (
  `payment_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_status` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`payment_status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.payment_type
CREATE TABLE IF NOT EXISTS `payment_type` (
  `payment_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_type` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`payment_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.poinvoices
CREATE TABLE IF NOT EXISTS `poinvoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` varchar(200) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `p_name` varchar(50) NOT NULL,
  `contact_no` varchar(30) NOT NULL,
  `d_name` varchar(50) DEFAULT NULL,
  `reg` varchar(30) DEFAULT NULL,
  `bill_type_id` int(11) NOT NULL,
  `payment_method` varchar(100) NOT NULL,
  `total_amount` varchar(50) NOT NULL DEFAULT '',
  `discount_percentage` int(11) DEFAULT NULL,
  `delivery_charges` varchar(50) DEFAULT NULL,
  `value_added_services` varchar(50) DEFAULT NULL,
  `paidAmount` varchar(50) NOT NULL,
  `cardPaidAmount` varchar(30) DEFAULT NULL,
  `balance` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=430 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.producttoshop
CREATE TABLE IF NOT EXISTS `producttoshop` (
  `productToShopId` int(11) NOT NULL AUTO_INCREMENT,
  `medicinId` int(11) DEFAULT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `productToShopStatus` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`productToShopId`),
  KEY `FK_producttoshop_shop` (`shop_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3673 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.p_brand
CREATE TABLE IF NOT EXISTS `p_brand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `img` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.p_medicine
CREATE TABLE IF NOT EXISTS `p_medicine` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `category` int(11) NOT NULL,
  `brand` int(11) NOT NULL,
  `medicine_unit_id` int(11) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `selectedShops` varchar(50) DEFAULT NULL,
  `unit_variation` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=734 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.p_medicine_category
CREATE TABLE IF NOT EXISTS `p_medicine_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.p_supplier
CREATE TABLE IF NOT EXISTS `p_supplier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `store` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_p_supplier_p_brand` (`brand_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.p_supply
CREATE TABLE IF NOT EXISTS `p_supply` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `store` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `supplier` varchar(255) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `payable` decimal(10,2) NOT NULL,
  `paid` decimal(10,2) NOT NULL,
  `due` decimal(10,2) NOT NULL,
  `details` text DEFAULT NULL,
  `supplydate` date NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.shop
CREATE TABLE IF NOT EXISTS `shop` (
  `shopId` int(11) NOT NULL AUTO_INCREMENT,
  `shopName` varchar(45) DEFAULT NULL,
  `shopEmail` text DEFAULT NULL,
  `shopAddress` text DEFAULT NULL,
  `shopTel` int(11) DEFAULT NULL,
  `shopWhatsApp` int(11) DEFAULT NULL,
  `shopManagerName` varchar(45) DEFAULT NULL,
  `shopManagerEmail` varchar(45) DEFAULT NULL,
  `shopManagerAddress` varchar(45) DEFAULT NULL,
  `shopManagerTel` int(11) DEFAULT NULL,
  `shopManagerWhatsApp` int(11) DEFAULT NULL,
  `shopImg` text DEFAULT NULL,
  `shopStatus` int(11) DEFAULT 1,
  PRIMARY KEY (`shopId`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.stock2
CREATE TABLE IF NOT EXISTS `stock2` (
  `stock_id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_item_code` varchar(50) DEFAULT NULL,
  `stock_item_name` varchar(50) DEFAULT NULL,
  `stock_item_qty` float DEFAULT NULL,
  `stock_item_cost` int(11) DEFAULT NULL,
  `stock_mu_qty` int(11) DEFAULT NULL,
  `unit_cost` double DEFAULT NULL,
  `unit_s_price` double DEFAULT NULL,
  `minimum_unit` varchar(50) DEFAULT NULL,
  `added_discount` int(11) DEFAULT NULL,
  `item_s_price` int(11) DEFAULT NULL,
  `stock_shop_id` int(11) DEFAULT NULL,
  `stock_minimum_unit_barcode` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`stock_id`)
) ENGINE=InnoDB AUTO_INCREMENT=812 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.stock23
CREATE TABLE IF NOT EXISTS `stock23` (
  `stock_id` int(11) NOT NULL AUTO_INCREMENT,
  `stock_item_code` varchar(50) DEFAULT NULL,
  `stock_item_name` varchar(50) DEFAULT NULL,
  `stock_item_qty` float DEFAULT NULL,
  `stock_item_cost` int(11) DEFAULT NULL,
  `stock_mu_qty` int(11) DEFAULT NULL,
  `unit_cost` double DEFAULT NULL,
  `unit_s_price` double DEFAULT NULL,
  `minimum_unit` varchar(50) DEFAULT NULL,
  `added_discount` int(11) DEFAULT NULL,
  `item_s_price` int(11) DEFAULT NULL,
  `stock_shop_id` int(11) DEFAULT NULL,
  `stock_minimum_unit_barcode` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`stock_id`)
) ENGINE=InnoDB AUTO_INCREMENT=812 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.store
CREATE TABLE IF NOT EXISTS `store` (
  `store_idl` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) DEFAULT NULL,
  `pass` varchar(255) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`store_idl`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.test
CREATE TABLE IF NOT EXISTS `test` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `c1` varchar(225) DEFAULT NULL,
  `c2` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=227 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.unit_category_variation
CREATE TABLE IF NOT EXISTS `unit_category_variation` (
  `ucv_id` int(11) NOT NULL AUTO_INCREMENT,
  `ucv_name` varchar(50) NOT NULL,
  `p_unit_id` int(11) DEFAULT NULL,
  `ucv_status` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ucv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_pass` varchar(255) NOT NULL,
  `date_registered` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_role_id` int(11) DEFAULT NULL,
  `shop_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table ceylriea_ts.user_role
CREATE TABLE IF NOT EXISTS `user_role` (
  `user_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_role` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`user_role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
