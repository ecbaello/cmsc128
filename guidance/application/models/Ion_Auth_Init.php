<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ion_Auth_Init extends CI_Model{
	 
	const UsersTableName = DB_PREFIX."auth_users";
	const GroupsTableName = DB_PREFIX."auth_groups";
	const UsersGroupsTableName = DB_PREFIX."auth_users_groups";
	const LoginAttemptsTableName = DB_PREFIX."auth_login_attempts";
	
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->dbforge();
		$this->createModel();
	}
	
	public function createModel(){
		
		//Check if tables already exist
if($this->db->table_exists(self::UsersTableName) || $this->db->table_exists(self::GroupsTableName) || $this->db->table_exists(self::UsersGroupsTableName) || $this->db->table_exists(self::LoginAttemptsTableName)) return;
		
		//create groups table
		$this->db->query("CREATE TABLE ".self::GroupsTableName." (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		
		//default values
		$this->db->query("INSERT INTO ".self::GroupsTableName." (`id`, `name`, `description`) VALUES (1,'admin','Administrator'),(2,'members','General User')");
		
		//create users table
		$this->db->query("CREATE TABLE ".self::UsersTableName." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `email` varchar(254) NOT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `pword` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		
		//default values
		$this->db->query("INSERT INTO ".self::UsersTableName." (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
     ('1','127.0.0.1','administrator','\$2a\$07\$SeBknntpZror9uyftVopmu61qg0ms8Qv1yV6FG.kQOSM.9QhmTo36','','admin@admin.com','',NULL,'1268889823','1268889823','1', 'Admin','istrator','ADMIN','0');");
	 
		//create users groups table
		$this->db->query("CREATE TABLE ".self::UsersGroupsTableName." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_users_groups_users1_idx` (`user_id`),
  KEY `fk_users_groups_groups1_idx` (`group_id`),
  CONSTRAINT `uc_users_groups` UNIQUE (`user_id`, `group_id`),
  CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES ".self::UsersTableName." (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES ".self::GroupsTableName." (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		
		//default values
		$this->db->query("INSERT INTO ".self::UsersGroupsTableName." (`id`, `user_id`, `group_id`) VALUES
     (1,1,1),
     (2,1,2);");
		
		//create login attempts table
		$this->db->query("CREATE TABLE ".self::LoginAttemptsTableName." (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
		
	}
	
}