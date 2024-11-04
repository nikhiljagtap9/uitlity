-- Adminer 4.7.8 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DELIMITER ;;

DROP PROCEDURE IF EXISTS `sp_all_loans_dataset`;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_all_loans_dataset`(IN `nbfc` VARCHAR(255))
BEGIN

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
end;;

DELIMITER ;

DROP TABLE IF EXISTS `account_enquiry`;
CREATE TABLE `account_enquiry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_no` varchar(50) DEFAULT NULL,
  `request_message` text DEFAULT NULL,
  `response_message` text DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `api_logs`;
CREATE TABLE `api_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `lapp_id` (`lapp_id`),
  KEY `process_type` (`process_type`),
  KEY `process_id` (`process_id`),
  KEY `api_status` (`api_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `batches`;
CREATE TABLE `batches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `pf_number` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `uuid_2` (`uuid`,`total_principal`,`total_interest`,`total_bank_principal`,`total_nbfc_principal`,`total_bank_interest`,`total_nbfc_interest`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `cbs_apis`;
CREATE TABLE `cbs_apis` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `batch_id` varchar(255) NOT NULL,
  `cbs_api` varchar(255) DEFAULT NULL,
  `request` text DEFAULT NULL,
  `response` text DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `pf_number` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `collections`;
CREATE TABLE `collections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `calc_nbfc_principal` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FORACID` (`FORACID`),
  KEY `REQ_NUMBER` (`REQ_NUMBER`),
  KEY `MONTH` (`MONTH`,`YEAR`),
  KEY `batch_id` (`batch_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `disbursebatches`;
CREATE TABLE `disbursebatches` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `total_loan_amount` decimal(12,2) DEFAULT NULL,
  `total_sanction_amount` decimal(12,2) DEFAULT NULL,
  `nbfc_sanction_amount` decimal(12,2) DEFAULT NULL,
  `bank_sanction_amount` decimal(12,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `pf_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `journalNoMSME` varchar(50) DEFAULT NULL,
  `journalNoAGRI` varchar(50) DEFAULT NULL,
  `loan_account_number_agri` varchar(50) DEFAULT NULL,
  `loan_account_number_msme` varchar(50) DEFAULT NULL,
  `deleted_by` varchar(50) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `disbursements`;
CREATE TABLE `disbursements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lapp_id` varchar(100) DEFAULT NULL,
  `batch_id` varchar(255) DEFAULT NULL,
  `NBFC_Reference_Number` varchar(255) NOT NULL,
  `CGCL_Customer_Number` varchar(255) NOT NULL,
  `CGCL_Account_Number` varchar(255) NOT NULL,
  `TITLE` varchar(255) DEFAULT NULL,
  `CUSTOMER_NAME` varchar(255) NOT NULL,
  `FIRST_NAME` varchar(255) NOT NULL,
  `MIDDLE_NAME` varchar(255) NOT NULL,
  `LAST_NAME` varchar(255) DEFAULT NULL,
  `GENDER` varchar(50) DEFAULT NULL,
  `MOBILE_NO` varchar(255) DEFAULT NULL,
  `dob` varchar(50) DEFAULT NULL,
  `AGE` float DEFAULT NULL,
  `EMAIL` varchar(100) DEFAULT NULL,
  `ADD1` text DEFAULT NULL,
  `ADD2` text DEFAULT NULL,
  `CITY` varchar(255) DEFAULT NULL,
  `STATE` varchar(255) DEFAULT NULL,
  `ZIPCODE` varchar(255) DEFAULT NULL,
  `RESI_STATUS` varchar(255) DEFAULT NULL,
  `MOTHER_NAME` varchar(100) DEFAULT NULL,
  `NATIONALITY_CODE` varchar(100) DEFAULT NULL,
  `SEC_ID_TYPE` varchar(100) DEFAULT NULL,
  `sanction_amount` decimal(12,2) DEFAULT NULL,
  `SANCTION_DATE` varchar(255) DEFAULT NULL,
  `loan_amount` float DEFAULT NULL,
  `bank_sanction_amount` decimal(12,2) DEFAULT NULL,
  `nbfc_sanction_amount` decimal(12,2) DEFAULT NULL,
  `LOAN_BOOKING_DATE` varchar(50) DEFAULT NULL,
  `LOAN_TENURE` varchar(255) DEFAULT NULL,
  `REMAINING_LOAN_TENURE` varchar(255) DEFAULT NULL,
  `Total_Weight_Valuer` float DEFAULT NULL,
  `Gross_weight` float DEFAULT NULL,
  `Gold_Value` float DEFAULT NULL,
  `Gold_Rate` float DEFAULT NULL,
  `Market_Rate` float DEFAULT NULL,
  `Net_Weight` float DEFAULT NULL,
  `Total_Weight` float DEFAULT NULL,
  `Total_Value` float DEFAULT NULL,
  `LTV` float DEFAULT NULL,
  `Gold_Purity` float DEFAULT NULL,
  `PAN` varchar(255) DEFAULT NULL,
  `ckyc` varchar(255) DEFAULT NULL,
  `CKYC_DATE` varchar(255) DEFAULT NULL,
  `POS` varchar(255) DEFAULT NULL,
  `INSURANCE_FINANCED` varchar(255) DEFAULT NULL,
  `AADHAR_NO` varchar(255) DEFAULT NULL,
  `Pos_Including_insurance` varchar(255) DEFAULT NULL,
  `Name_Valuer` varchar(255) DEFAULT NULL,
  `Role_Valuer` varchar(255) DEFAULT NULL,
  `Repayment_Type` varchar(255) DEFAULT NULL,
  `Date_Disbursement` varchar(50) DEFAULT NULL,
  `Maturity_Date` varchar(50) DEFAULT NULL,
  `Account_status` varchar(255) DEFAULT NULL,
  `Collateral_Description` varchar(255) DEFAULT NULL,
  `Business_Type` varchar(255) DEFAULT NULL,
  `Valuation_Date` varchar(255) DEFAULT NULL,
  `Realizable_Security_Value` float DEFAULT NULL,
  `customer_selection` varchar(255) DEFAULT NULL,
  `REPAY_DAY` varchar(255) DEFAULT NULL,
  `EMI_START_DATE` varchar(50) DEFAULT NULL,
  `MORATORIUM` varchar(255) DEFAULT NULL,
  `DISBURSMENT_DETAIL` varchar(255) DEFAULT NULL,
  `Disbursal_Mode` varchar(255) DEFAULT NULL,
  `Assets_ID` varchar(255) DEFAULT NULL,
  `Security_Interest_ID` varchar(255) DEFAULT NULL,
  `cersai_date` varchar(50) DEFAULT NULL,
  `CIC` varchar(255) DEFAULT NULL,
  `CGCL_ROI` float DEFAULT NULL,
  `Udyam_no` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `pf_number` varchar(255) DEFAULT NULL,
  `partner_id` varchar(255) DEFAULT NULL,
  `product_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `message` text DEFAULT NULL,
  `utr_bom_pos_update` varchar(255) DEFAULT NULL,
  `loan_account_number` varchar(255) DEFAULT NULL,
  `pan_match_score` float DEFAULT NULL,
  `ckyc_match_score` float DEFAULT NULL,
  `udyam_match_score` float DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approved_by` varchar(50) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `gold_rates`;
CREATE TABLE `gold_rates` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `22k_gold_rate` decimal(10,2) DEFAULT NULL,
  `24k_gold_rate` decimal(10,2) DEFAULT NULL,
  `updated_time` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `entry_date` date NOT NULL,
  `request` text DEFAULT NULL,
  `response` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `interests`;
CREATE TABLE `interests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` varchar(50) NOT NULL,
  `interest_date` date NOT NULL,
  `bank_roi` decimal(12,3) NOT NULL,
  `nbfc_roi` decimal(12,3) NOT NULL,
  `total_interest` decimal(12,2) DEFAULT NULL,
  `bank_interest` decimal(12,2) NOT NULL,
  `nbfc_interest` decimal(12,2) NOT NULL,
  `interest_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loan_id` (`loan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `interest_months`;
CREATE TABLE `interest_months` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` varchar(50) NOT NULL,
  `interest_date` date NOT NULL,
  `interest_month` int(11) DEFAULT NULL,
  `interest_year` int(11) DEFAULT NULL,
  `bank_roi` decimal(12,3) NOT NULL,
  `nbfc_roi` decimal(12,3) NOT NULL,
  `total_interest` decimal(12,2) DEFAULT NULL,
  `bank_interest` decimal(12,2) NOT NULL,
  `nbfc_interest` decimal(12,2) NOT NULL,
  `interest_type` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `loan_id` (`loan_id`),
  KEY `interest_date` (`interest_date`),
  KEY `interest_type` (`interest_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` text NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `job_progress`;
CREATE TABLE `job_progress` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` varchar(255) NOT NULL,
  `progress` int(11) NOT NULL DEFAULT 0,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `job_statuses`;
CREATE TABLE `job_statuses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `progress` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `job_statuses_job_id_unique` (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `loan_accounts`;
CREATE TABLE `loan_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `lapp_id` varchar(100) DEFAULT NULL,
  `loan_id` varchar(100) DEFAULT NULL,
  `mfl_ref_no` varchar(100) DEFAULT NULL,
  `ucic` varchar(50) DEFAULT NULL,
  `bank_interest` decimal(12,2) DEFAULT NULL,
  `nbfc_interest` decimal(12,2) DEFAULT NULL,
  `sanction_limit` decimal(12,2) DEFAULT NULL,
  `bank_sanction_amount` decimal(12,2) DEFAULT NULL,
  `nbfc_sanction_amount` decimal(12,2) DEFAULT NULL,
  `total_balance` decimal(12,2) DEFAULT NULL,
  `bank_balance` decimal(12,2) DEFAULT NULL,
  `loan_tenure` int(11) DEFAULT 9,
  `bank_loan_date` date DEFAULT NULL,
  `nbfc_loan_date` varchar(50) DEFAULT NULL,
  `loan_status` varchar(50) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `nbfc_balance` decimal(12,2) DEFAULT NULL,
  `classification` varchar(50) DEFAULT 'STD',
  `nbfc_backdate` int(10) DEFAULT NULL,
  `bank_backdate` int(10) DEFAULT NULL,
  `utr_bom_pos_update` varchar(50) DEFAULT NULL,
  `loan_account_number` varchar(50) DEFAULT NULL,
  `job_type` varchar(50) DEFAULT NULL,
  `pan_number` varchar(50) DEFAULT NULL,
  `postal_code` varchar(50) DEFAULT NULL,
  `state_code` varchar(50) DEFAULT NULL,
  `city_code` varchar(50) DEFAULT NULL,
  `address1` text DEFAULT NULL,
  `address2` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mobile_number` varchar(50) DEFAULT NULL,
  `caste` varchar(50) DEFAULT NULL,
  `community` varchar(50) DEFAULT NULL,
  `ckyc_no` varchar(50) DEFAULT NULL,
  `date_of_birth` varchar(50) DEFAULT NULL,
  `gender` varchar(50) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `customer_title` varchar(50) DEFAULT NULL,
  `ltv` decimal(12,2) DEFAULT NULL,
  `batch_id` varchar(50) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `dob` varchar(50) DEFAULT NULL,
  `AGE` int(11) DEFAULT NULL,
  `pan_card` varchar(50) DEFAULT NULL,
  `loan_amount` decimal(12,2) DEFAULT NULL,
  `sanction_amount` decimal(12,2) DEFAULT NULL,
  `interest_rate` decimal(8,2) DEFAULT NULL,
  `processing_fees` decimal(12,2) DEFAULT NULL,
  `udyog_uaadhaar_number` varchar(50) DEFAULT NULL,
  `ckyc` varchar(50) DEFAULT NULL,
  `credit_score` varchar(50) DEFAULT NULL,
  `status` varchar(100) NOT NULL DEFAULT 'Pending',
  `CGCL_Customer_Number` varchar(50) DEFAULT NULL,
  `CGCL_Account_Number` varchar(50) DEFAULT NULL,
  `DISBURSMENT_DETAIL` varchar(50) DEFAULT NULL,
  `FIRST_NAME` varchar(50) DEFAULT NULL,
  `MIDDLE_NAME` varchar(50) DEFAULT NULL,
  `LAST_NAME` varchar(50) DEFAULT NULL,
  `MOTHER_NAME` varchar(50) DEFAULT NULL,
  `RESI_STATUS` varchar(50) DEFAULT NULL,
  `NATIONALITY_CODE` varchar(50) DEFAULT NULL,
  `SEC_ID_TYPE` varchar(50) DEFAULT NULL,
  `AADHAR_NO` varchar(50) DEFAULT NULL,
  `CKYC_DATE` varchar(50) DEFAULT NULL,
  `SANCTION_DATE` varchar(50) DEFAULT NULL,
  `POS` varchar(50) DEFAULT NULL,
  `INSURANCE_FINANCED` varchar(50) DEFAULT NULL,
  `REMAINING_LOAN_TENURE` int(11) DEFAULT NULL,
  `Total_Weight` float DEFAULT NULL,
  `Name_Valuer` varchar(50) DEFAULT NULL,
  `Role_Valuer` varchar(50) DEFAULT NULL,
  `Gross_weight` float DEFAULT NULL,
  `Total_Weight_Valuer` float DEFAULT NULL,
  `Gold_Value` float DEFAULT NULL,
  `Net_Weight` float DEFAULT NULL,
  `Gold_Rate` float DEFAULT NULL,
  `Market_Rate` float DEFAULT NULL,
  `Total_Value` float DEFAULT NULL,
  `Repayment_Type` varchar(50) DEFAULT NULL,
  `Date_Disbursement` varchar(50) DEFAULT NULL,
  `Maturity_Date` varchar(50) DEFAULT NULL,
  `Account_status` varchar(50) DEFAULT NULL,
  `Gold_Purity` float DEFAULT NULL,
  `Disbursal_Mode` varchar(50) DEFAULT NULL,
  `Collateral_Description` varchar(255) DEFAULT NULL,
  `Business_Type` varchar(50) DEFAULT NULL,
  `Valuation_Date` varchar(50) DEFAULT NULL,
  `Realizable_Security_Value` varchar(50) DEFAULT NULL,
  `REPAY_DAY` varchar(50) DEFAULT NULL,
  `EMI_START_DATE` varchar(50) DEFAULT NULL,
  `MORATORIUM` varchar(50) DEFAULT NULL,
  `Assets_ID` varchar(50) DEFAULT NULL,
  `Security_Interest_ID` varchar(50) DEFAULT NULL,
  `cersai_date` varchar(50) DEFAULT NULL,
  `CIC` varchar(50) DEFAULT NULL,
  `closing_date` datetime DEFAULT NULL,
  `pan_match_score` float DEFAULT NULL,
  `ckyc_match_score` float DEFAULT NULL,
  `udyam_match_score` float DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `loan_accounts_loan_id_unique` (`loan_id`),
  UNIQUE KEY `loan_accounts_mfl_ref_no_unique` (`mfl_ref_no`),
  KEY `loan_status` (`loan_status`),
  KEY `mfl_ref_no` (`mfl_ref_no`),
  KEY `loan_id` (`loan_id`),
  KEY `loan_tenure` (`loan_tenure`),
  KEY `sanction_limit` (`sanction_limit`),
  KEY `bank_sanction_amount` (`bank_sanction_amount`),
  KEY `nbfc_sanction_amount` (`nbfc_sanction_amount`),
  KEY `classification` (`classification`),
  KEY `ltv` (`ltv`),
  KEY `customer_name` (`customer_name`),
  KEY `ucic` (`ucic`),
  KEY `ckyc_no` (`ckyc_no`),
  KEY `batch_id` (`batch_id`),
  KEY `title` (`title`),
  KEY `dob` (`dob`),
  KEY `loan_amount` (`loan_amount`),
  KEY `sanction_amount` (`sanction_amount`),
  KEY `interest_rate` (`interest_rate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `loan_entries`;
CREATE TABLE `loan_entries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` varchar(255) NOT NULL,
  `entry_date` date DEFAULT NULL,
  `entry_month` int(11) DEFAULT NULL,
  `entry_year` int(11) NOT NULL,
  `bank_date` date DEFAULT NULL,
  `nbfc` varchar(255) DEFAULT NULL,
  `txn_set` varchar(255) DEFAULT NULL,
  `txn_set_id` varchar(255) DEFAULT NULL,
  `entry_set` varchar(255) DEFAULT NULL,
  `entry_set_id` varchar(255) DEFAULT NULL,
  `entry_timestamp` date NOT NULL DEFAULT current_timestamp(),
  `debit` decimal(12,2) DEFAULT NULL,
  `credit` decimal(12,2) DEFAULT NULL,
  `total_debit` decimal(12,2) DEFAULT NULL,
  `bank_debit` decimal(12,2) DEFAULT NULL,
  `nbfc_debit` decimal(12,2) DEFAULT NULL,
  `total_credit` decimal(12,2) DEFAULT NULL,
  `bank_credit` decimal(12,2) DEFAULT NULL,
  `nbfc_credit` decimal(12,2) DEFAULT NULL,
  `balance` decimal(12,2) DEFAULT NULL,
  `bank_balance` decimal(12,2) DEFAULT NULL,
  `nbfc_balance` decimal(12,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `head` varchar(255) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `txn_ref` varchar(255) DEFAULT NULL,
  `success_ref` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `sub_category` varchar(255) DEFAULT NULL,
  `activity` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `jnl_no` varchar(100) DEFAULT NULL,
  `classification` varchar(50) DEFAULT 'STD',
  `principal_balance` decimal(12,2) DEFAULT NULL,
  `principal_bank_balance` decimal(12,2) DEFAULT NULL,
  `principal_nbfc_balance` decimal(12,2) DEFAULT NULL,
  `interest_balance` decimal(12,2) DEFAULT NULL,
  `interest_bank_balance` decimal(12,2) DEFAULT NULL,
  `interest_nbfc_balance` decimal(12,2) DEFAULT NULL,
  `collection_id` int(50) DEFAULT NULL,
  `pf_number` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `debit` (`debit`,`credit`,`balance`),
  KEY `entry_timestamp` (`entry_timestamp`),
  KEY `jnl_no` (`jnl_no`),
  KEY `loan_id` (`loan_id`,`jnl_no`) USING BTREE,
  KEY `entry_date` (`entry_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `loan_meta`;
CREATE TABLE `loan_meta` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stamp` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `object_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `coll_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `coll_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `meta_key` (`meta_key`),
  KEY `coll_id` (`coll_id`),
  KEY `object_id` (`object_id`),
  KEY `meta_value` (`meta_value`(100)),
  KEY `object_id_coll_id_meta_key` (`object_id`,`coll_id`,`meta_key`),
  KEY `coll_type` (`coll_type`),
  KEY `coll_id_meta_key_meta_value` (`coll_id`,`meta_key`,`meta_value`(100)),
  KEY `coll_type_meta_key_meta_value` (`coll_type`,`meta_key`,`meta_value`(100))
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


DELIMITER ;;

CREATE TRIGGER `after_loan_meta_insert` AFTER INSERT ON `loan_meta` FOR EACH ROW
BEGIN
SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;
set @object_id=NEW.object_id;
set @found=NULL;
select object_id INTO @found from data_changes.loan_data_changes where object_id=@object_id limit 1;
IF @found is NULL THEN
	insert into data_changes.loan_data_changes(stamp,object_id,activity,object_type,nbfc) VALUES (date_format(now(),'%Y%m%d%H%i'),@object_id,'changed', 'loan','muthoot');
END IF;
END;;

CREATE TRIGGER `after_loan_meta_update` AFTER UPDATE ON `loan_meta` FOR EACH ROW
BEGIN
SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;
set @object_id_old=OLD.object_id;
set @found=NULL;
select object_id INTO @found from data_changes.loan_data_changes where object_id=@object_id_old limit 1;
IF @found is NULL THEN
	insert into data_changes.loan_data_changes(stamp,object_id,activity,object_type,nbfc)
	VALUES (date_format(now(),'%Y%m%d%H%i'),@object_id_old,'changed', 'loan','muthoot');
END IF;
set @object_id_new=NEW.object_id;
set @found=NULL;
select object_id INTO @found from data_changes.loan_data_changes where object_id=@object_id_new limit 1;
IF @found is NULL THEN
	insert into data_changes.loan_data_changes(stamp,object_id,activity,object_type,nbfc)
	VALUES (date_format(now(),'%Y%m%d%H%i'),@object_id_new,'changed', 'loan','muthoot');
END IF;
END;;

CREATE TRIGGER `after_loan_meta_delete` AFTER DELETE ON `loan_meta` FOR EACH ROW
BEGIN
SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED;
set @object_id=OLD.object_id;
set @found=NULL;
select object_id INTO @found from data_changes.loan_data_changes where object_id=@object_id limit 1;
IF @found is NULL THEN
	insert into data_changes.loan_data_changes(stamp,object_id,activity,object_type,nbfc)
	VALUES (date_format(now(),'%Y%m%d%H%i'),@object_id,'changed', 'loan','muthoot');
END IF;
END;;

DELIMITER ;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `process_meta`;
CREATE TABLE `process_meta` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `process_type` (`process_type`),
  KEY `process_owner` (`process_owner`),
  KEY `process_id` (`process_id`),
  KEY `model_type` (`model_type`),
  KEY `meta_type` (`meta_type`),
  KEY `meta_key` (`meta_key`),
  KEY `lapp_id` (`lapp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `repayment_schedules`;
CREATE TABLE `repayment_schedules` (
  `id` bigint(20) unsigned NOT NULL,
  `loan_id` varchar(255) NOT NULL,
  `entry_month` date NOT NULL,
  `principal` double NOT NULL,
  `interest` double NOT NULL,
  `bank_principal` double NOT NULL,
  `nbfc_principal` double NOT NULL,
  `bank_interest` double NOT NULL,
  `nbfc_interest` double NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `repayment_schedules_loan_id_unique` (`loan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_role` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2024-10-30 06:35:05
