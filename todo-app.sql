# Dump of table tbl_users
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_users` (
  `id` varchar(200) NOT NULL, 
  `profile_pic` char(100) NOT NULL, 
  `email` unique varchar(220) NOT NULL,
  `about_me` text,
  `persist_code` varchar(150) NULL,
  `activation_code`  varchar(150) NULL,
  `mobile_number` NOT NULL,
  `reset_password_code` varchar(220) NULL
  `full_name` varchar(220) NOT NULL,
  `first_name` varchar(220) NOT NULL,
  `last_name` varchar(220) NOT NULL,
  `gender` enum('male', 'female') NOT NULL DEFAULT 'male',
  `last_login` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('suspended', 'active', 'barred') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf-8 COLLATE=utf8_unicode_ci;

# Dump of table tbl_user_roles
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_user_roles` (
  `id` varchar(200) NOT NULL,
  `user_id` varchar(200) NOT NULL, 
  `role` enum('admin', 'manager') NOT NULL DEFAULT 'admin',
  `permissions` longtext '{"admin":{"read":["/todos/@id"],"write":["/todos/@id"]},"manager":{"read":["/todos"],"write":[]}}',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY(`id`),
  CONSTRAINT fk_uid FOREIGN KEY (`user_id`) REFERENCES `tbl_users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf-8 COLLATE=utf8_unicode_ci;

# Dump of table tbl_user_throttles
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_user_throttles` (
  `id` varchar(200) NOT NULL, --(session id, browser fingerprint)
  `ip_address` varchar(200) NOT NULL DEFAULT '0.0.0.0',
  `attempts` int NOT NULL DEFAULT 0, 
  `suspended` tinyint NOT NULL DEFAULT 0,
  `banned` tinyint NOT NULL DEFAULT 0,
  `last_attempt_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `suspended_at` datetime NULL,
  `banned_at` datetime NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf-8 COLLATE=utf8_unicode_ci;

# Dump of table tbl_todos
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_todos` (
  `id` varchar(200) NOT NULL,
  `title` varchar(150) NOT NULL,
  `description` text NULL,
  `start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `due` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `list_id` varchar(200) NOT NULL,
  `status` enum('done', 'incomplete') NOT NULL DEFAULT 'incomplete',
  `assignee` unique varchar(220) NULL,
  PRIMARY KEY(`id`),
  CONSTRAINT fk_tdlist FOREIGN KEY (`list_id`) REFERENCES `tbl_todos_list`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
  -- CONSTRAINT fk_usr FOREIGN KEY (`assignee`) REFERENCES `tbl_users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf-8 COLLATE=utf8_unicode_ci;

# Dump of table tbl_todos_list
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_todos_list` (
  `id` varchar(200) NOT NULL,
  `name` text NOT NULL DEFAULT 'general',
  `user_id` varchar(200) NOT NULL,
  `project_id` varchar(200) NOT NULL DEFAULT '45a2cd23f08bbd6477d2ff89715cba32de',
  `priority` enum('urgent', 'upcoming') NOT NULL DEFAULT 'upcoming',
  `reminder-rate` enum('frequent:interval=3.days', 'ocassional:interval=1.week') NOT NULL DEFAULT 'ocassional:interval=1.week',
  `created_at`  datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_last_at`  datetime NOT NULL DEFAULT '000-00-00 00:00:00',
  PRIMARY KEY(`id`),
  CONSTRAINT fk_uid FOREIGN KEY (`user_id`) REFERENCES `tbl_users`(`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_uid FOREIGN KEY (`project_id`) REFERENCES `tbl_projects`(`id`) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf-8 COLLATE=utf8_unicode_ci;

# Dump of table tbl_projects
# ------------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_projects` (
  `id` varchar(200) NOT NULL,
  `name` varchar(250) NOT NULL DEFAULT 'personal',
  `mode` enum('short-term', 'long-term') NOT NULL DEFAULT 'long-term',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf-8 COLLATE=utf8_unicode_ci;

INSERT INTO `tbl_projects` (`id`) VALUES ('45a2cd23f08bbd6477d2ff89715cba32de');