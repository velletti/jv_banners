#
# Table structure for table 'tx_jvbanners_domain_model_connector'
#
CREATE TABLE tx_jvbanners_domain_model_connector (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	eventname varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted smallint(5) unsigned DEFAULT '0' NOT NULL,
	hidden smallint(5) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),

);

#
# Table structure for table 'tx_sfbanners_domain_model_banner'
#
CREATE TABLE tx_sfbanners_domain_model_banner (
    link int(11) DEFAULT '0' NOT NULL,
    fe_user int(11) DEFAULT '0' NOT NULL,
    organizer int(11) DEFAULT '0' NOT NULL,
    key byUser (fe_user),
    key byOrganizer (organizer),
);
