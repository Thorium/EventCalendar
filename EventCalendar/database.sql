# --------------------------------------------------------

# -- Eventcalendar database structure

# -- This is just for demo...
# -- Not installed automatically

# -- create new database:
create database 'eventcalendar';


# -- Visible: 0 = show, others = hidden
# -- EventType: 0 = event, others = info

CREATE TABLE `EventCalendar` (
  `Id` int(11) NOT NULL auto_increment,
  `Title` varchar(255) NOT NULL default '',
  `Visible` int(11) NOT NULL default '0', 
  `EventType` int(11) NOT NULL default '0',
  `DateYear` int(11) default NULL,
  `DateMonth` int(11) default NULL,
  `DateDay` int(11) default NULL,
  `DateHour` int(11) default NULL,
  `DateMinute` int(11) default NULL,
  `City` varchar(255) NOT NULL default '',
  `StreetAddress` varchar(255) NOT NULL default '',
  `LocationDetails` varchar(255) NOT NULL default '',
  `EventDetails` varchar(255) NOT NULL default '',
  `Latitude` varchar(15) NOT NULL default '',
  `Longitude` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`Id`)
) TYPE=MyISAM COMMENT='Tapahtumakalenteri';

# -- new user and password:
CREATE USER 'eventcalendaruser'@'localhost' IDENTIFIED BY 'cal123';
GRANT INSERT,SELECT,UPDATE,DELETE ON eventcalendar.* TO 'eventcalendaruser'@'localhost' with grant option;
FLUSH PRIVILEGES;


# -- Contact requests
CREATE TABLE `ContactRequest` (
  `Id` int(11) NOT NULL auto_increment,
  `Request` varchar(255) NOT NULL default '',
  `Email` varchar(255) NOT NULL default '',
  `Contacted` int(11) NOT NULL default '0', 
  `Created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (`Id`)
) TYPE=MyISAM COMMENT='Yhteydenottopyynnot';
