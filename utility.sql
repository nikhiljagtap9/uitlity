-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2024 at 07:35 AM
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
-- Database: `utility`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_all_loans_dataset` (IN `nbfc` VARCHAR(255))   BEGIN

SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;
set names utf8mb4 collate utf8mb4_unicode_ci;

select @nbfc:=nbfc;
DROP temporary TABLE IF EXISTS full_data;
  CREATE temporary TABLE full_data (
	object_id varchar(100) NOT NULL
  ) ENGINE=InnoDB;

  ALTER TABLE full_data
  ADD PRIMARY KEY object_id (object_id);

  

  INSERT INTO full_data (object_id)
  select distinct object_id
  from data_changes.loan_data_changes where data_changes.loan_data_changes.nbfc=@nbfc limit 100;

  DROP temporary TABLE IF EXISTS t_data;
  CREATE temporary TABLE t_data (
	object_id varchar(100) NOT NULL,
	loan_id varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT ''
  ) ENGINE=InnoDB;

  ALTER TABLE t_data
  ADD PRIMARY KEY object_id (object_id),
  ADD KEY loan_id (loan_id);
  
  
  create temporary table t_loan_meta
  select loan_meta.* from loan_meta join full_data 
  ON full_data.object_id=loan_meta.object_id; 
  

  
  INSERT INTO t_data (object_id) 
  SELECT full_data.object_id
  FROM 
  full_data left join  t_loan_meta 
  ON full_data.object_id=t_loan_meta.object_id where meta_key='loan_id'; 


INSERT IGNORE INTO data_store.all_loans_dataset(loan_id) 
	SELECT t_data.object_id from t_data;
	
   
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
        select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='colender_nbfc'
	) as t_loan_meta
	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.colender_nbfc=left(t_loan_meta.meta_value,50);
 
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='sanction_amount'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.sanction_amount=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='loan_status'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.loan_status=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='disbursment_by_transafer_status'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.disbursment_by_transafer_status=left(t_loan_meta.meta_value,50);
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='customer_name'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.customer_name=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='first_name'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.first_name=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='middle_name'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.middle_name=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='last_name'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.last_name=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='loan_date'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.loan_date=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='mobile_number'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.mobile_number=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='business_addr_line1'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.business_addr_line1=left(t_loan_meta.meta_value,50);
	
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='loan_acceptance_status'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.loan_acceptance_status=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='nbfc_sanction_amount'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.nbfc_sanction_amount=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='bank_sanction_amount'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.bank_sanction_amount=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='name_enquiry_status'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.name_enquiry_status=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='pan_enquiry_status'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.pan_enquiry_status=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='create_personal_cif_status'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.create_personal_cif_status=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='plant_and_machine_amend_status'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.plant_and_machine_amend_status=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='loan_acc_creation_status'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.loan_acc_creation_status=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='loan_account_additional_details_status'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.loan_account_additional_details_status=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='loan_approval_process_status'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.loan_approval_process_status=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='loan_disbursment_creation_status'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.loan_disbursment_creation_status=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='repay_schedule_emi_status'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.repay_schedule_emi_status=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='credit_txn_for_recovery_charges_status'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.credit_txn_for_recovery_charges_status=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='product_id'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.product_id=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='utr_bom_pos_update'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.utr_bom_pos_update=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='loan_account_number'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.loan_account_number=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='customer_number'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.customer_number=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='partner_id'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.partner_id=left(t_loan_meta.meta_value,50);
	

	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='sanction_limit'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.sanction_limit=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='repayment_periodinmonths'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.repayment_periodinmonths=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='mfl_ref_no'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.mfl_ref_no=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='total_security'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.total_security=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='bre_status'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.bre_status=left(t_loan_meta.meta_value,50);
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='sanction_date'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.sanction_date=left(t_loan_meta.meta_value,50);
	
	
	UPDATE data_store.all_loans_dataset AS update_table
	join
	(
		select t_data.object_id, ifnull(meta_value,'') as meta_value from t_data join t_loan_meta using(object_id) where coll_id='loan' AND meta_key='sol_id'
	) as t_loan_meta

	ON
	update_table.loan_id=t_loan_meta.object_id
	SET update_table.sol_id=left(t_loan_meta.meta_value,50);
	
    delete data_changes.loan_data_changes from data_changes.loan_data_changes join full_data 
	on loan_data_changes.object_id=full_data.object_id and loan_data_changes.nbfc=@nbfc; 
end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `account_enquiry`
--

CREATE TABLE `account_enquiry` (
  `id` int(11) NOT NULL,
  `account_no` varchar(50) DEFAULT NULL,
  `request_message` text DEFAULT NULL,
  `response_message` text DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `api_logs`
--

CREATE TABLE `api_logs` (
  `id` int(11) NOT NULL,
  `process_type` varchar(255) NOT NULL,
  `process_owner` varchar(255) NOT NULL,
  `process_id` varchar(255) NOT NULL,
  `api_name` varchar(255) NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `request_json` longtext NOT NULL,
  `response_json` longtext NOT NULL,
  `api_status` varchar(255) NOT NULL,
  `process_status` varchar(255) NOT NULL,
  `lapp_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `total_principal` double NOT NULL DEFAULT 0,
  `total_interest` double NOT NULL DEFAULT 0,
  `total_bank_principal` double NOT NULL DEFAULT 0,
  `total_nbfc_principal` double NOT NULL DEFAULT 0,
  `total_bank_interest` double NOT NULL DEFAULT 0,
  `total_nbfc_interest` double NOT NULL DEFAULT 0,
  `total_mfl_sprade` double(12,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pf_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cbs_apis`
--

CREATE TABLE `cbs_apis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `batch_id` varchar(255) NOT NULL,
  `cbs_api` varchar(255) DEFAULT NULL,
  `request` text DEFAULT NULL,
  `response` text DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pf_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collections`
--

CREATE TABLE `collections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `FORACID` varchar(255) NOT NULL,
  `REQ_NUMBER` varchar(255) NOT NULL,
  `MONTH` varchar(255) DEFAULT NULL,
  `YEAR` varchar(255) DEFAULT NULL,
  `PRINCIPAL_AMT` double DEFAULT NULL,
  `INTEREST_AMT` double DEFAULT NULL,
  `batch_id` varchar(255) NOT NULL,
  `final_principal` double(12,2) DEFAULT NULL,
  `final_interest` double(12,2) DEFAULT NULL,
  `bank_principal` double(12,2) DEFAULT 0.00,
  `status` varchar(50) DEFAULT NULL,
  `loan_booking_date` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `nbfc_principal` double(12,2) NOT NULL DEFAULT 0.00,
  `bank_interest` double(12,2) DEFAULT 0.00,
  `mfl_sprade` double(12,2) DEFAULT NULL,
  `nbfc_interest` double(12,2) NOT NULL DEFAULT 0.00,
  `transaction_date` varchar(50) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `pf_number` varchar(50) DEFAULT NULL,
  `loan_account_number` varchar(50) DEFAULT NULL,
  `Amount` decimal(12,2) DEFAULT NULL,
  `calc_bank_interest` decimal(12,2) DEFAULT NULL,
  `calc_nbfc_interest` decimal(12,2) DEFAULT NULL,
  `calc_bank_principal` decimal(12,2) DEFAULT NULL,
  `calc_nbfc_principal` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disbursebatches`
--

CREATE TABLE `disbursebatches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `pf_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_by` varchar(50) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `disbursebatches`
--

INSERT INTO `disbursebatches` (`id`, `uuid`, `status`, `pf_number`, `created_at`, `updated_at`, `deleted_by`, `deleted_at`) VALUES
(1, 'BATCH248731624490201', 'Matched', 'nikhil sudhakar jagtap', '2024-12-13 14:26:01', '2024-12-13 14:26:11', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `disbursements`
--

CREATE TABLE `disbursements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lapp_id` varchar(100) DEFAULT NULL,
  `batch_id` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `epic_number` varchar(50) DEFAULT NULL,
  `driving_lic_number` varchar(50) DEFAULT NULL,
  `ckyc_number` varchar(50) DEFAULT NULL,
  `date_of_birth` text NOT NULL,
  `aadhar_number` varchar(255) DEFAULT NULL,
  `udyam_aadhar` varchar(50) DEFAULT NULL,
  `pan` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `pan_match_score` float DEFAULT NULL,
  `ckyc_match_score` float DEFAULT NULL,
  `aadhar_match_score` float DEFAULT NULL,
  `driving_lic_match_score` float DEFAULT NULL,
  `voting_card_match_score` float DEFAULT NULL,
  `udyam_match_score` float DEFAULT NULL,
  `pf_number` varchar(255) DEFAULT NULL,
  `partner_id` varchar(255) DEFAULT NULL,
  `product_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `message` text DEFAULT NULL,
  `deleted_at` text DEFAULT NULL,
  `approved_by` varchar(255) DEFAULT NULL,
  `approved_at` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `disbursements`
--

INSERT INTO `disbursements` (`id`, `lapp_id`, `batch_id`, `full_name`, `epic_number`, `driving_lic_number`, `ckyc_number`, `date_of_birth`, `aadhar_number`, `udyam_aadhar`, `pan`, `status`, `pan_match_score`, `ckyc_match_score`, `aadhar_match_score`, `driving_lic_match_score`, `voting_card_match_score`, `udyam_match_score`, `pf_number`, `partner_id`, `product_id`, `created_at`, `updated_at`, `message`, `deleted_at`, `approved_by`, `approved_at`) VALUES
(1, 'APP8660941752868576', 'BATCH248731624490201', 'SHUBHAM SINGH', 'ITR0046391', 'HR7220180000466', '30029508366219', '13-08-1994', '265385644663', NULL, 'JDXPS7881C', 'Rejected', NULL, NULL, NULL, NULL, NULL, NULL, 'nikhil sudhakar jagtap', 'utility', 'utility', '2024-12-13 14:26:01', '2024-12-13 14:26:07', '[\"Voting Card Verification Failed\",\"PAN Verification Failed\",\"Aadhaar Verification Failed\"]', NULL, NULL, NULL),
(2, 'APP6101488847715762', 'BATCH248731624490201', 'KULDEEP LAXMAN KHOBREKAR', 'ITR0046391', 'HR7220180000466', '30029508366219', '13-08-1994', '265385644663', NULL, 'AYLPK8525B', 'Rejected', NULL, NULL, NULL, NULL, NULL, NULL, 'nikhil sudhakar jagtap', 'utility', 'utility', '2024-12-13 14:26:01', '2024-12-13 14:26:10', '[\"Voting Card Verification Failed\",\"PAN Verification Failed\",\"Aadhaar Verification Failed\"]', NULL, NULL, NULL),
(3, 'APP9948236462220780', 'BATCH248731624490201', 'sonali dattatrey mapagavkar', 'XCY8166258', 'HR7220180000466', '30029508366219', '24-02-1997', '460459628141', NULL, 'HMQPM4667E', 'Matched', NULL, NULL, NULL, NULL, 100, NULL, 'nikhil sudhakar jagtap', 'utility', 'utility', '2024-12-13 14:26:01', '2024-12-13 14:26:11', 'BRE Approved', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `process_meta`
--

CREATE TABLE `process_meta` (
  `id` bigint(20) NOT NULL,
  `process_type` varchar(255) DEFAULT NULL,
  `process_owner` varchar(255) DEFAULT NULL,
  `process_parent_id` varchar(255) DEFAULT NULL,
  `process_id` varchar(255) DEFAULT NULL,
  `model_type` varchar(255) DEFAULT current_timestamp(),
  `meta_type` varchar(255) DEFAULT NULL,
  `meta_key` varchar(255) DEFAULT current_timestamp(),
  `meta_value` longtext DEFAULT NULL,
  `lapp_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `process_meta`
--

INSERT INTO `process_meta` (`id`, `process_type`, `process_owner`, `process_parent_id`, `process_id`, `model_type`, `meta_type`, `meta_key`, `meta_value`, `lapp_id`, `created_at`, `updated_at`) VALUES
(1, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS5886007268837221', 'App\\Models\\Disbursement', 'voterCardVerification', 'process_id', 'PROCESS5886007268837221', 'APP8660941752868576', '2024-12-13 14:26:06', '2024-12-13 14:26:06'),
(2, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS5886007268837221', 'App\\Models\\Disbursement', 'voterCardVerification', 'vendor', 'Scoreme', 'APP8660941752868576', '2024-12-13 14:26:06', '2024-12-13 14:26:06'),
(3, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS5886007268837221', 'App\\Models\\Disbursement', 'voterCardVerification', 'request_json', '{\"endpoint\":\"https:\\/\\/sm-kyc-sync-sandbox-proxy.scoreme.in\\/kyc\\/external\\/voterCard\",\"data\":{\"epicNumber\":\"ITR0046391\"}}', 'APP8660941752868576', '2024-12-13 14:26:06', '2024-12-13 14:26:06'),
(4, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS5886007268837221', 'App\\Models\\Disbursement', 'voterCardVerification', 'response_json', '{\"referenceId\":\"fea48fab-b6a5-4ef2-bbc1-528398af287c\",\"responseMessage\":\"No Information Found.\",\"responseCode\":\"ENI004\"}', 'APP8660941752868576', '2024-12-13 14:26:06', '2024-12-13 14:26:06'),
(5, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS5886007268837221', 'App\\Models\\Disbursement', 'voterCardVerification', 'api_status', 'F', 'APP8660941752868576', '2024-12-13 14:26:06', '2024-12-13 14:26:06'),
(6, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS5886007268837221', 'App\\Models\\Disbursement', 'voterCardVerification', 'process_status', 'failed', 'APP8660941752868576', '2024-12-13 14:26:06', '2024-12-13 14:26:06'),
(7, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS7721703083422033', 'App\\Models\\Disbursement', 'PanVerification', 'process_id', 'PROCESS7721703083422033', 'APP8660941752868576', '2024-12-13 14:26:06', '2024-12-13 14:26:06'),
(8, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS7721703083422033', 'App\\Models\\Disbursement', 'PanVerification', 'vendor', 'Scoreme', 'APP8660941752868576', '2024-12-13 14:26:06', '2024-12-13 14:26:06'),
(9, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS7721703083422033', 'App\\Models\\Disbursement', 'PanVerification', 'request_json', '{\"endpoint\":\"https:\\/\\/sm-kyc-sync-sandbox-proxy.scoreme.in\\/kyc\\/external\\/panDataFetch\",\"data\":{\"pan\":\"JDXPS7881C\"}}', 'APP8660941752868576', '2024-12-13 14:26:06', '2024-12-13 14:26:06'),
(10, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS7721703083422033', 'App\\Models\\Disbursement', 'PanVerification', 'response_json', '{\"headers\":{},\"original\":{\"error\":\"Undefined array key \\\"responseCode\\\"\"},\"exception\":null}', 'APP8660941752868576', '2024-12-13 14:26:06', '2024-12-13 14:26:06'),
(11, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS7721703083422033', 'App\\Models\\Disbursement', 'PanVerification', 'api_status', 'F', 'APP8660941752868576', '2024-12-13 14:26:06', '2024-12-13 14:26:06'),
(12, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS7721703083422033', 'App\\Models\\Disbursement', 'PanVerification', 'process_status', 'failed', 'APP8660941752868576', '2024-12-13 14:26:06', '2024-12-13 14:26:06'),
(13, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS9469479170065009', 'App\\Models\\Disbursement', 'aadharVerification', 'process_id', 'PROCESS9469479170065009', 'APP8660941752868576', '2024-12-13 14:26:07', '2024-12-13 14:26:07'),
(14, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS9469479170065009', 'App\\Models\\Disbursement', 'aadharVerification', 'vendor', 'Scoreme', 'APP8660941752868576', '2024-12-13 14:26:07', '2024-12-13 14:26:07'),
(15, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS9469479170065009', 'App\\Models\\Disbursement', 'aadharVerification', 'request_json', '{\"endpoint\":\"https:\\/\\/sm-kyc-sync-sandbox-proxy.scoreme.in\\/kyc\\/external\\/aadhaarVerifier\",\"data\":{\"aadhaarNumber\":\"265385644663\"}}', 'APP8660941752868576', '2024-12-13 14:26:07', '2024-12-13 14:26:07'),
(16, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS9469479170065009', 'App\\Models\\Disbursement', 'aadharVerification', 'response_json', '{\"referenceId\":\"4f962c90-14c1-4622-b1c1-00cdd3d1371e\",\"responseMessage\":\"Aadhaar does not exist.\",\"responseCode\":\"EAE168\"}', 'APP8660941752868576', '2024-12-13 14:26:07', '2024-12-13 14:26:07'),
(17, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS9469479170065009', 'App\\Models\\Disbursement', 'aadharVerification', 'api_status', 'F', 'APP8660941752868576', '2024-12-13 14:26:07', '2024-12-13 14:26:07'),
(18, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS9469479170065009', 'App\\Models\\Disbursement', 'aadharVerification', 'process_status', 'failed', 'APP8660941752868576', '2024-12-13 14:26:07', '2024-12-13 14:26:07'),
(19, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS7071484971947852', 'App\\Models\\Disbursement', 'voterCardVerification', 'process_id', 'PROCESS7071484971947852', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(20, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS7071484971947852', 'App\\Models\\Disbursement', 'voterCardVerification', 'vendor', 'Scoreme', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(21, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS7071484971947852', 'App\\Models\\Disbursement', 'voterCardVerification', 'request_json', '{\"endpoint\":\"https:\\/\\/sm-kyc-sync-sandbox-proxy.scoreme.in\\/kyc\\/external\\/voterCard\",\"data\":{\"epicNumber\":\"ITR0046391\"}}', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(22, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS7071484971947852', 'App\\Models\\Disbursement', 'voterCardVerification', 'response_json', '{\"referenceId\":\"864bb128-87f9-4494-bee7-6abf39ca6366\",\"responseMessage\":\"No Information Found.\",\"responseCode\":\"ENI004\"}', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(23, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS7071484971947852', 'App\\Models\\Disbursement', 'voterCardVerification', 'api_status', 'F', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(24, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS7071484971947852', 'App\\Models\\Disbursement', 'voterCardVerification', 'process_status', 'failed', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(25, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS0118185263541108', 'App\\Models\\Disbursement', 'PanVerification', 'process_id', 'PROCESS0118185263541108', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(26, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS0118185263541108', 'App\\Models\\Disbursement', 'PanVerification', 'vendor', 'Scoreme', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(27, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS0118185263541108', 'App\\Models\\Disbursement', 'PanVerification', 'request_json', '{\"endpoint\":\"https:\\/\\/sm-kyc-sync-sandbox-proxy.scoreme.in\\/kyc\\/external\\/panDataFetch\",\"data\":{\"pan\":\"AYLPK8525B\"}}', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(28, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS0118185263541108', 'App\\Models\\Disbursement', 'PanVerification', 'response_json', '{\"headers\":{},\"original\":{\"error\":\"Undefined array key \\\"responseCode\\\"\"},\"exception\":null}', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(29, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS0118185263541108', 'App\\Models\\Disbursement', 'PanVerification', 'api_status', 'F', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(30, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS0118185263541108', 'App\\Models\\Disbursement', 'PanVerification', 'process_status', 'failed', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(31, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS1949689881109053', 'App\\Models\\Disbursement', 'aadharVerification', 'process_id', 'PROCESS1949689881109053', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(32, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS1949689881109053', 'App\\Models\\Disbursement', 'aadharVerification', 'vendor', 'Scoreme', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(33, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS1949689881109053', 'App\\Models\\Disbursement', 'aadharVerification', 'request_json', '{\"endpoint\":\"https:\\/\\/sm-kyc-sync-sandbox-proxy.scoreme.in\\/kyc\\/external\\/aadhaarVerifier\",\"data\":{\"aadhaarNumber\":\"265385644663\"}}', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(34, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS1949689881109053', 'App\\Models\\Disbursement', 'aadharVerification', 'response_json', '{\"referenceId\":\"f3469659-dda7-45c9-b482-47f93d1bf628\",\"responseMessage\":\"Aadhaar does not exist.\",\"responseCode\":\"EAE168\"}', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(35, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS1949689881109053', 'App\\Models\\Disbursement', 'aadharVerification', 'api_status', 'F', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(36, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS1949689881109053', 'App\\Models\\Disbursement', 'aadharVerification', 'process_status', 'failed', 'APP6101488847715762', '2024-12-13 14:26:10', '2024-12-13 14:26:10'),
(37, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS1864363890651852', 'App\\Models\\Disbursement', 'voterCardVerification', 'process_id', 'PROCESS1864363890651852', 'APP9948236462220780', '2024-12-13 14:26:11', '2024-12-13 14:26:11'),
(38, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS1864363890651852', 'App\\Models\\Disbursement', 'voterCardVerification', 'vendor', 'Scoreme', 'APP9948236462220780', '2024-12-13 14:26:11', '2024-12-13 14:26:11'),
(39, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS1864363890651852', 'App\\Models\\Disbursement', 'voterCardVerification', 'request_json', '{\"endpoint\":\"https:\\/\\/sm-kyc-sync-sandbox-proxy.scoreme.in\\/kyc\\/external\\/voterCard\",\"data\":{\"epicNumber\":\"XCY8166258\"}}', 'APP9948236462220780', '2024-12-13 14:26:11', '2024-12-13 14:26:11'),
(40, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS1864363890651852', 'App\\Models\\Disbursement', 'voterCardVerification', 'response_json', '{\"data\":{\"epicNumber\":\"XCY8166258\",\"pollingDate\":\"\",\"serialNumber\":\"285\",\"gender\":\"F\",\"parliamentaryConstituency\":\"32, Raigad\",\"partName\":\"Parli\",\"assemblyConstituency\":\"191, Pen\",\"district\":\"24, Raigad\",\"name\":\"sonali dattatrey mapagavkar\",\"fatherHusbandName\":\"dattatrey mapagavkar\",\"pollingStation\":\"RZP Primary Marathi School,Parli\",\"lastUpdatedOn\":\"2024-11-08T13:54:00.084+00:00\",\"partNumber\":\"180\",\"state\":\"Maharashtra\",\"age\":\"26\"},\"referenceId\":\"b30f09b6-1b54-496a-87a2-62d669e522d2\",\"responseMessage\":\"Successfully Completed.\",\"responseCode\":\"SRC001\"}', 'APP9948236462220780', '2024-12-13 14:26:11', '2024-12-13 14:26:11'),
(41, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS1864363890651852', 'App\\Models\\Disbursement', 'voterCardVerification', 'api_status', 'S', 'APP9948236462220780', '2024-12-13 14:26:11', '2024-12-13 14:26:11'),
(42, 'Disbursement', 'nikhil sudhakar jagtap', NULL, 'PROCESS1864363890651852', 'App\\Models\\Disbursement', 'voterCardVerification', 'process_status', 'completed', 'APP9948236462220780', '2024-12-13 14:26:11', '2024-12-13 14:26:11');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bank_interest` double(8,3) NOT NULL,
  `bank_roi` double(8,3) NOT NULL,
  `nbfc_interest` double(8,3) NOT NULL,
  `benchmark_rate` double(8,2) DEFAULT NULL,
  `loan_account_number` varchar(50) DEFAULT NULL,
  `loan_account_number_agri` varchar(50) DEFAULT NULL,
  `loan_account_number_msme` varchar(50) DEFAULT NULL,
  `to_loan_account_number_agri` varchar(50) DEFAULT NULL,
  `to_loan_account_number_msme` varchar(50) DEFAULT NULL,
  `service_fee` double(8,3) DEFAULT NULL,
  `gst` double(8,3) DEFAULT NULL,
  `pan_check` varchar(10) DEFAULT NULL,
  `ckyc_check` varchar(10) DEFAULT NULL,
  `udyam_check` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `bank_interest`, `bank_roi`, `nbfc_interest`, `benchmark_rate`, `loan_account_number`, `loan_account_number_agri`, `loan_account_number_msme`, `to_loan_account_number_agri`, `to_loan_account_number_msme`, `service_fee`, `gst`, `pan_check`, `ckyc_check`, `udyam_check`, `created_at`, `updated_at`) VALUES
(1, 9.900, 9.900, 23.000, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `user_role`) VALUES
(1, 'kuldeep', 'kuldeep.khobrekar@loantap.in', NULL, '$2y$10$748X4QmNB9chDmV9ImkMaO3JcXgs1MpfG3HbUCzebS6tRBNEr82Uy', NULL, '2024-11-27 12:27:36', '2024-11-27 12:27:36', NULL),
(2, 'Harsh', 'harsh.signh@loantap.in', NULL, '$2y$10$w1nhvZacZ/cS3UkdqHEEdeb5XQf.HV2jfTxMLNEBoGlTbrnoBn5kK', NULL, '2024-11-27 12:39:55', '2024-11-27 12:39:55', NULL),
(3, 'sonali mapgaonkar', 'sonali@loantap.in', NULL, '$2y$10$K4vyKfPYgMvOMUDshbg/ue2CoAuoY5/yh4coLD5cDPRHD2WXSjBte', NULL, '2024-12-02 09:49:41', '2024-12-02 09:49:41', NULL),
(4, 'nikhil sudhakar jagtap', 'nikhil.jagtap@loantap.in', NULL, '$2y$10$EaDPs/FZh59GzQKC36Owx.fhd9dJzWMd/.mkfLmgX323sVbGzP5PK', NULL, '2024-12-05 07:50:58', '2024-12-05 07:50:58', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_enquiry`
--
ALTER TABLE `account_enquiry`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `api_logs`
--
ALTER TABLE `api_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id` (`id`),
  ADD KEY `lapp_id` (`lapp_id`),
  ADD KEY `process_type` (`process_type`),
  ADD KEY `process_id` (`process_id`),
  ADD KEY `api_status` (`api_status`);

--
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `uuid_2` (`uuid`,`total_principal`,`total_interest`,`total_bank_principal`,`total_nbfc_principal`,`total_bank_interest`,`total_nbfc_interest`,`status`);

--
-- Indexes for table `cbs_apis`
--
ALTER TABLE `cbs_apis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FORACID` (`FORACID`),
  ADD KEY `REQ_NUMBER` (`REQ_NUMBER`),
  ADD KEY `MONTH` (`MONTH`,`YEAR`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `disbursebatches`
--
ALTER TABLE `disbursebatches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `disbursements`
--
ALTER TABLE `disbursements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `process_meta`
--
ALTER TABLE `process_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `process_type` (`process_type`),
  ADD KEY `process_owner` (`process_owner`),
  ADD KEY `process_id` (`process_id`),
  ADD KEY `model_type` (`model_type`),
  ADD KEY `meta_type` (`meta_type`),
  ADD KEY `meta_key` (`meta_key`),
  ADD KEY `lapp_id` (`lapp_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account_enquiry`
--
ALTER TABLE `account_enquiry`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `api_logs`
--
ALTER TABLE `api_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `batches`
--
ALTER TABLE `batches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cbs_apis`
--
ALTER TABLE `cbs_apis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `collections`
--
ALTER TABLE `collections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `disbursebatches`
--
ALTER TABLE `disbursebatches`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `disbursements`
--
ALTER TABLE `disbursements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `process_meta`
--
ALTER TABLE `process_meta`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
